#!/usr/bin/env python3
"""
Validate the generated WXR demo-content file against the actual content of
the repo (stdlib only: xml.etree.ElementTree, os).

Checks:
  (a) The file is well-formed XML.
  (b) It contains exactly the expected number of post / nsml_property /
      attachment items, where "expected" is computed from the real file
      listing in the repo (not a hardcoded guess).
  (c) Every <wp:attachment_url> resolves to a file that exists on disk,
      relative to the repo root.
  (d) No item has an empty <title> or an empty wp:post_date.
  (e) Every nsml_property item has all required postmeta keys present.

Exits 0 and prints a PASS summary if everything checks out; exits 1 and
prints a FAIL summary (with details) otherwise.
"""

import os
import sys
import xml.etree.ElementTree as ET

REPO_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
WXR_PATH = os.path.join(REPO_ROOT, "wordpress-theme", "nsml", "demo-content", "nsml-demo-content.xml")

WP_NS = "http://wordpress.org/export/1.2/"


def wp_tag(name):
    return f"{{{WP_NS}}}{name}"


NON_ARTICLE_PAGES = {
    "index.html", "about.html", "services.html", "contact.html",
    "news.html", "properties.html", "article.html",
}

PROPERTY_FILES = {
    "lagos-marathon.html",
    "abuja-marathon.html",
    "abeokuta-race.html",
    "ijebu-marathon.html",
    "copa-lagos.html",
    "enugu-marathon.html",
    "yenagoa-race.html",
    "kaduna-marathon.html",
    "iau-remo-championship.html",
    "stormers-club.html",
}

REQUIRED_PROPERTY_META_KEYS = {
    "nsml_location",
    "nsml_hero_tag",
    "nsml_official_website",
    "nsml_organizer_type",
    "nsml_next_edition",
    "nsml_about",
    "nsml_stats_json",
    "nsml_gallery_urls_json",
}


def expected_article_count():
    all_html = {
        f for f in os.listdir(REPO_ROOT)
        if f.endswith(".html") and os.path.isfile(os.path.join(REPO_ROOT, f))
    }
    excluded = NON_ARTICLE_PAGES | PROPERTY_FILES
    return len(all_html - excluded)


def main():
    failures = []
    notes = []

    if not os.path.exists(WXR_PATH):
        print(f"FAIL: WXR file does not exist at {WXR_PATH}")
        sys.exit(1)

    if os.path.getsize(WXR_PATH) == 0:
        print(f"FAIL: WXR file is empty at {WXR_PATH}")
        sys.exit(1)

    # (a) well-formed XML
    try:
        tree = ET.parse(WXR_PATH)
    except ET.ParseError as e:
        print(f"FAIL: WXR file is not well-formed XML: {e}")
        sys.exit(1)
    root = tree.getroot()
    notes.append("(a) Well-formed XML: OK")

    items = root.findall(".//item")
    by_type = {"post": [], "nsml_property": [], "attachment": [], "other": []}
    for item in items:
        pt_el = item.find(wp_tag("post_type"))
        pt = pt_el.text.strip() if pt_el is not None and pt_el.text else None
        if pt in by_type:
            by_type[pt].append(item)
        else:
            by_type["other"].append(item)

    # (b) expected counts
    expected_posts = expected_article_count()
    expected_properties = len(PROPERTY_FILES)

    actual_posts = len(by_type["post"])
    actual_properties = len(by_type["nsml_property"])
    actual_attachments = len(by_type["attachment"])

    if actual_posts != expected_posts:
        failures.append(
            f"(b) post item count mismatch: expected {expected_posts}, got {actual_posts}"
        )
    else:
        notes.append(f"(b) post item count OK: {actual_posts}")

    if actual_properties != expected_properties:
        failures.append(
            f"(b) nsml_property item count mismatch: expected {expected_properties}, got {actual_properties}"
        )
    else:
        notes.append(f"(b) nsml_property item count OK: {actual_properties}")

    if actual_attachments == 0:
        failures.append("(b) attachment item count is 0 -- expected at least 1")
    else:
        notes.append(f"(b) attachment item count: {actual_attachments} (non-zero, OK)")

    if by_type["other"]:
        failures.append(
            f"(b) found {len(by_type['other'])} item(s) with unexpected/missing post_type"
        )

    # (c) every wp:attachment_url resolves to a real file on disk
    attachment_urls = root.findall(f".//{wp_tag('attachment_url')}")
    if not attachment_urls:
        failures.append("(c) no <wp:attachment_url> elements found at all")
    else:
        base_urls_seen = set()
        missing = []
        for el in attachment_urls:
            url = (el.text or "").strip()
            if not url:
                missing.append("(empty attachment_url)")
                continue
            # Strip scheme://host to get the path; find rel path after domain.
            # url looks like https://nilayosports.designthngs.com/images/...
            idx = url.find("://")
            path_part = url[idx + 3:] if idx != -1 else url
            slash = path_part.find("/")
            rel_path = path_part[slash + 1:] if slash != -1 else ""
            base_urls_seen.add(path_part[:slash] if slash != -1 else path_part)
            abs_path = os.path.join(REPO_ROOT, rel_path)
            if not rel_path or not os.path.exists(abs_path):
                missing.append(f"{url}  (resolved rel path: '{rel_path}')")
        if missing:
            failures.append(
                f"(c) {len(missing)} attachment URL(s) do not resolve to files on disk:\n      "
                + "\n      ".join(missing)
            )
        else:
            notes.append(f"(c) all {len(attachment_urls)} attachment_url(s) resolve to real files on disk")

    # (d) no item has empty title or empty wp:post_date
    empty_title_or_date = []
    for item in items:
        title_el = item.find("title")
        date_el = item.find(wp_tag("post_date"))
        title_text = (title_el.text or "").strip() if title_el is not None else ""
        date_text = (date_el.text or "").strip() if date_el is not None else ""
        if not title_text or not date_text:
            pt_el = item.find(wp_tag("post_type"))
            name_el = item.find(wp_tag("post_name"))
            label = f"post_type={pt_el.text if pt_el is not None else '?'} name={name_el.text if name_el is not None else '?'}"
            empty_title_or_date.append(label)

    if empty_title_or_date:
        failures.append(
            f"(d) {len(empty_title_or_date)} item(s) have an empty title or post_date:\n      "
            + "\n      ".join(empty_title_or_date)
        )
    else:
        notes.append(f"(d) all {len(items)} item(s) have non-empty title and post_date")

    # (e) every nsml_property item has all required postmeta keys present
    property_meta_failures = []
    for item in by_type["nsml_property"]:
        name_el = item.find(wp_tag("post_name"))
        name = name_el.text if name_el is not None else "?"
        keys_present = set()
        for meta_el in item.findall(wp_tag("postmeta")):
            key_el = meta_el.find(wp_tag("meta_key"))
            if key_el is not None and key_el.text:
                keys_present.add(key_el.text.strip())
        missing_keys = REQUIRED_PROPERTY_META_KEYS - keys_present
        if missing_keys:
            property_meta_failures.append(f"{name}: missing {sorted(missing_keys)}")

    if property_meta_failures:
        failures.append(
            f"(e) {len(property_meta_failures)} nsml_property item(s) missing required postmeta:\n      "
            + "\n      ".join(property_meta_failures)
        )
    else:
        notes.append(f"(e) all {len(by_type['nsml_property'])} nsml_property item(s) have required postmeta")

    # --- summary ---------------------------------------------------------
    print("WXR validation report")
    print("=" * 60)
    print(f"File: {WXR_PATH}")
    print(f"Total <item> elements: {len(items)}")
    print(f"  post:          {actual_posts}")
    print(f"  nsml_property: {actual_properties}")
    print(f"  attachment:    {actual_attachments}")
    print()
    for n in notes:
        print(f"  PASS  {n}")
    print()

    if failures:
        for f in failures:
            print(f"  FAIL  {f}")
        print()
        print(f"RESULT: FAIL ({len(failures)} check(s) failed)")
        sys.exit(1)
    else:
        print("RESULT: PASS (all checks passed)")
        sys.exit(0)


if __name__ == "__main__":
    main()
