#!/usr/bin/env python3
"""
Generate the demo content for the NSML theme from the existing static-HTML
site, so the client can populate a fresh WordPress install with the REAL
blog articles, REAL property pages, and REAL images already present in
this repo -- instead of generic placeholder demo content.

Produces two equivalent representations of the same data:
  * manifest.php + images/ -- consumed by the theme's own custom importer
    (Appearance > Import Content), which copies images straight off disk
    into the Media Library with no HTTP request. This is the recommended
    import path; see wordpress-theme/README.md.
  * nsml-demo-content.xml -- a WXR 1.2 file for the native WordPress
    Importer (Tools > Import > WordPress), kept for compatibility/testing.
    Its native import fetches images over HTTP from the live domain,
    which some hosts block.

Usage:
    python3 generate_wxr.py

Output:
    wordpress-theme/nsml/demo-content/nsml-demo-content.xml
    wordpress-theme/nsml/demo-content/manifest.php
    wordpress-theme/nsml/demo-content/images/...

Design notes
------------
* Every <wp:attachment_url> emitted MUST correspond to a file that actually
  exists on disk (checked with os.path.exists()). If anything is missing,
  the script prints a clear list of the missing files and aborts with a
  non-zero exit code instead of silently emitting a broken URL.
* Gallery / sponsor-image / event-logo references on `nsml_property` posts
  cannot point at real WordPress attachment IDs at WXR-generation time
  (those IDs only exist after the native importer creates the attachment
  posts). So instead of nsml_gallery_json (real IDs), we emit
  nsml_gallery_urls_json (JSON array of {url, wide}) and
  nsml_sponsor_image_url / nsml_event_logo_url as plain postmeta. A small
  one-time admin_init hook in inc/cpt-property.php
  (nsml_resolve_gallery_urls_to_ids()) converts these to real attachment
  IDs the first time wp-admin loads after import, using
  attachment_url_to_postid().
"""

import json
import os
import re
import sys
from datetime import datetime, timezone
from html import escape as html_escape
from xml.sax.saxutils import escape as xml_escape

try:
    from bs4 import BeautifulSoup
except ImportError:
    print("ERROR: this script requires BeautifulSoup4 (`pip install beautifulsoup4`).",
          file=sys.stderr)
    sys.exit(1)

# ---------------------------------------------------------------------------
# Paths
# ---------------------------------------------------------------------------

REPO_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
THEME_DIR = os.path.join(REPO_ROOT, "wordpress-theme")
DEMO_CONTENT_DIR = os.path.join(THEME_DIR, "nsml", "demo-content")
OUTPUT_PATH = os.path.join(DEMO_CONTENT_DIR, "nsml-demo-content.xml")
MANIFEST_IMAGES_DIR = os.path.join(DEMO_CONTENT_DIR, "images")
MANIFEST_PATH = os.path.join(DEMO_CONTENT_DIR, "manifest.php")
CNAME_PATH = os.path.join(REPO_ROOT, "CNAME")

NON_ARTICLE_PAGES = {
    "index.html", "about.html", "services.html", "contact.html",
    "news.html", "properties.html", "article.html",
}

# A handful of property pages style their hero with a remote stock-photo URL
# (e.g. lagos-marathon.html uses an Unsplash URL) instead of a local file, so
# there is nothing for parse_property() to pick up automatically. Each entry
# here is a real local file -- shipped in this repo specifically as that
# property's hero/cover image -- used in place of the missing local hero.
PROPERTY_HERO_IMAGE_OVERRIDES = {
    "lagos-marathon": "images/events/lagos/lagos-hero.jpg",
}

PROPERTY_FILES = [
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
]

SITE_TITLE = "Nilayo Sports Management Ltd"
SITE_DESCRIPTION = "Africa's leading sports management and event organisation company."
AUTHOR_LOGIN = "nsmleditorial"
AUTHOR_EMAIL = "editorial@nilayosports.com"
AUTHOR_DISPLAY_NAME = "NSML Editorial"


def get_base_url():
    """Read CNAME to build the absolute base URL used for wp:attachment_url."""
    with open(CNAME_PATH, encoding="utf-8") as f:
        domain = f.read().strip()
    if not domain:
        raise RuntimeError("CNAME file is empty; cannot build attachment base URL.")
    return f"https://{domain}"


BASE_URL = get_base_url()

# ---------------------------------------------------------------------------
# Small helpers
# ---------------------------------------------------------------------------

MISSING_FILES = []          # collects every missing-on-disk reference
ATTACHMENTS = {}            # rel_path -> {id, url, title}
NEXT_ID = [1000]            # arbitrary starting post ID counter


def next_id():
    NEXT_ID[0] += 1
    return NEXT_ID[0]


def repo_relpath(html_relpath):
    """A path like 'images/news/foo.jpg' is already relative to repo root
    when it appears inside a top-level *.html file, since those pages use
    `images/...`, `css/...`, etc. relative to the repo root."""
    return html_relpath.lstrip("./")


def verify_exists(rel_path, referenced_from):
    abs_path = os.path.join(REPO_ROOT, rel_path)
    if not os.path.exists(abs_path):
        MISSING_FILES.append((rel_path, referenced_from))
        return False
    return True


def register_attachment(rel_path, referenced_from, title=None):
    """Register an image (if not already known) and return its attachment
    URL. Verifies existence on disk; missing files are collected and
    reported at the end rather than silently emitted."""
    rel_path = repo_relpath(rel_path)
    ok = verify_exists(rel_path, referenced_from)
    if rel_path not in ATTACHMENTS:
        ATTACHMENTS[rel_path] = {
            "id": next_id(),
            "url": BASE_URL + "/" + rel_path.replace(os.sep, "/"),
            "title": title or os.path.splitext(os.path.basename(rel_path))[0],
            "exists": ok,
        }
    return ATTACHMENTS[rel_path]


MONTHS = {
    "January": 1, "February": 2, "March": 3, "April": 4, "May": 5, "June": 6,
    "July": 7, "August": 8, "September": 9, "October": 10, "November": 11,
    "December": 12,
}

DATE_RE = re.compile(r"(January|February|March|April|May|June|July|August|"
                      r"September|October|November|December)\s+(\d{1,2}),\s+(\d{4})")


def parse_article_date(text):
    """Parse 'June 16, 2022' style strings into a datetime (UTC, midday to
    avoid timezone-boundary surprises)."""
    m = DATE_RE.search(text or "")
    if not m:
        return None
    month, day, year = m.group(1), int(m.group(2)), int(m.group(3))
    return datetime(year, MONTHS[month], day, 12, 0, 0, tzinfo=timezone.utc)


def wxr_pubdate(dt):
    # RFC822, as required by wp:pubDate / <pubDate>
    return dt.strftime("%a, %d %b %Y %H:%M:%S +0000")


def wxr_post_date(dt):
    # WXR wp:post_date / wp:post_date_gmt format
    return dt.strftime("%Y-%m-%d %H:%M:%S")


def slug_from_filename(filename):
    return os.path.splitext(os.path.basename(filename))[0]


def cdata(text):
    text = text or ""
    return "<![CDATA[" + text.replace("]]>", "]]]]><![CDATA[>") + "]]>"


# ---------------------------------------------------------------------------
# Article (blog post) parsing
# ---------------------------------------------------------------------------

def find_article_files():
    all_html = sorted(
        f for f in os.listdir(REPO_ROOT)
        if f.endswith(".html") and os.path.isfile(os.path.join(REPO_ROOT, f))
    )
    excluded = NON_ARTICLE_PAGES | set(PROPERTY_FILES)
    articles = [f for f in all_html if f not in excluded]
    return articles


def parse_article(filename):
    path = os.path.join(REPO_ROOT, filename)
    with open(path, encoding="utf-8") as f:
        raw = f.read()
    soup = BeautifulSoup(raw, "html.parser")

    title_el = soup.select_one("h1.article-hero-title")
    title = title_el.get_text(strip=True) if title_el else slug_from_filename(filename)

    # Date: first .meta-item inside .article-meta-bar matching a Month D, YYYY pattern
    date_dt = None
    for meta_item in soup.select(".article-meta-bar .meta-item"):
        d = parse_article_date(meta_item.get_text())
        if d:
            date_dt = d
            break
    if date_dt is None:
        # Fall back to any date-looking text in the document.
        d = parse_article_date(raw)
        date_dt = d or datetime(2022, 1, 1, 12, 0, 0, tzinfo=timezone.utc)

    # Hero image: prefer the <style> rule for .article-hero-img, fall back to
    # an inline style attribute on an element with that class.
    hero_image = None
    style_tag_text = "\n".join(s.get_text() for s in soup.find_all("style"))
    m = re.search(r"\.article-hero-img\s*\{[^}]*background:\s*url\(['\"]?([^'\")]+)['\"]?\)", style_tag_text)
    if m:
        hero_image = m.group(1)
    if not hero_image:
        hero_el = soup.select_one(".article-hero-img")
        if hero_el and hero_el.get("style"):
            m2 = re.search(r"url\(['\"]?([^'\")]+)['\"]?\)", hero_el["style"])
            if m2:
                hero_image = m2.group(1)

    # Body: paragraphs inside .article-body, stopping before .article-tags
    body = soup.select_one(".article-body")
    content_parts = []
    if body:
        for el in body.find_all(["p", "h2", "h3", "ul", "ol", "blockquote"], recursive=False):
            if el.find_parent(class_="article-tags") is not None:
                continue
            # find_all(recursive=False) on body only gets direct children;
            # the real markup nests p tags directly under article-body, so this is fine.
            content_parts.append(str(el))
    if not content_parts and body:
        # fallback: grab every <p> anywhere inside body except inside .article-tags
        for p in body.find_all("p"):
            if p.find_parent(class_="article-tags") is not None:
                continue
            content_parts.append(str(p))
    content_html = "\n\n".join(content_parts)

    # Tags
    tags = [a.get_text(strip=True) for a in soup.select(".article-tags .tag-pill")]

    return {
        "filename": filename,
        "slug": slug_from_filename(filename),
        "title": title,
        "date": date_dt,
        "hero_image": hero_image,
        "content_html": content_html,
        "tags": tags,
    }


# ---------------------------------------------------------------------------
# Property page parsing
# ---------------------------------------------------------------------------

def parse_property(filename):
    path = os.path.join(REPO_ROOT, filename)
    with open(path, encoding="utf-8") as f:
        raw = f.read()
    soup = BeautifulSoup(raw, "html.parser")

    title_el = soup.select_one("h1.prop-hero-title")
    title = title_el.get_text(strip=True) if title_el else slug_from_filename(filename)

    location_el = soup.select_one(".prop-hero-location")
    location = location_el.get_text(strip=True).lstrip("📍").strip() if location_el else ""

    hero_tag_el = soup.select_one(".prop-hero-tag")
    hero_tag = hero_tag_el.get_text(strip=True) if hero_tag_el else ""

    # Official website: only an absolute http(s) URL counts (internal links
    # like contact.html are not "official websites").
    website = ""
    website_el = soup.select_one(".prop-hero-actions a.btn-fill")
    if website_el:
        href = website_el.get("href", "")
        if href.startswith("http://") or href.startswith("https://"):
            website = href

    # Organizer type: look at every sidebar card title for the canonical phrases.
    organizer_type = "owned"
    for title_div in soup.select(".prop-sidebar-title"):
        t = title_div.get_text()
        if "Consultant" in t and "Organized" in t:
            organizer_type = "consultant"
            break
        if "Owned and Organized" in t:
            organizer_type = "owned"
            break

    # Next edition: prefer the dedicated .prop-next callout text.
    next_edition = ""
    next_el = soup.select_one(".prop-next")
    if next_el:
        next_edition = next_el.get_text(" ", strip=True)
        next_edition = re.sub(r"\s+", " ", next_edition)
    if not next_edition:
        for stat_item in soup.select(".prop-stat-item"):
            lbl = stat_item.select_one(".prop-stat-lbl")
            if lbl and "Next Edition" in lbl.get_text():
                val = stat_item.select_one(".prop-stat-val")
                next_edition = f"Next Edition: {val.get_text(strip=True) if val else ''}".strip()
                break

    # About paragraphs: <p> tags directly inside .prop-body, excluding the
    # .prop-next callout block's own text.
    about_paras = []
    body_el = soup.select_one(".prop-body")
    if body_el:
        for p in body_el.find_all("p", recursive=False):
            about_paras.append(str(p))
        if not about_paras:
            for p in body_el.find_all("p"):
                about_paras.append(str(p))
    about_html = "\n".join(about_paras)

    # Stats band
    stats = []
    for stat_item in soup.select(".prop-stat-item"):
        val_el = stat_item.select_one(".prop-stat-val")
        lbl_el = stat_item.select_one(".prop-stat-lbl")
        if val_el and lbl_el:
            stats.append({
                "value": val_el.get_text(strip=True),
                "label": lbl_el.get_text(strip=True),
            })

    # Gallery
    gallery = []
    gallery_grid = soup.select_one(".prop-gallery-grid")
    if gallery_grid:
        for img in gallery_grid.select("img.gallery-img"):
            src = img.get("src", "")
            if not src or src.startswith("http"):
                continue
            slot = img.find_parent(class_="gallery-slot")
            wide = bool(slot and "gallery-slot-wide" in (slot.get("class") or []))
            gallery.append({"src": src, "wide": wide})

    # Sponsor image: an <img> inside a sidebar card titled "Sponsors & Partners"
    sponsor_image = None
    for card in soup.select(".prop-sidebar-card"):
        title_div = card.select_one(".prop-sidebar-title")
        if title_div and "Sponsor" in title_div.get_text():
            img = card.select_one("img")
            if img and img.get("src") and not img["src"].startswith("http"):
                sponsor_image = img["src"]
            break

    # Event logo
    event_logo = None
    logo_el = soup.select_one(".event-logo-wrap img.event-logo") or soup.select_one("img.event-logo")
    if logo_el and logo_el.get("src") and not logo_el["src"].startswith("http"):
        event_logo = logo_el["src"]

    # Hero background image (some properties use a remote Unsplash hero, in
    # which case there is no local file to import and we leave it unset;
    # the post thumbnail then simply isn't set for that one property).
    hero_bg = None
    style_tag_text = "\n".join(s.get_text() for s in soup.find_all("style"))
    m = re.search(r"\.prop-hero-bg\s*\{[^}]*background:\s*url\(['\"]?([^'\")]+)['\"]?\)", style_tag_text)
    if m and not m.group(1).startswith("http"):
        hero_bg = m.group(1)

    slug = slug_from_filename(filename)
    if not hero_bg and slug in PROPERTY_HERO_IMAGE_OVERRIDES:
        hero_bg = PROPERTY_HERO_IMAGE_OVERRIDES[slug]

    return {
        "filename": filename,
        "slug": slug,
        "title": title,
        "location": location,
        "hero_tag": hero_tag,
        "website": website,
        "organizer_type": organizer_type,
        "next_edition": next_edition,
        "about_html": about_html,
        "stats": stats,
        "gallery": gallery,
        "sponsor_image": sponsor_image,
        "event_logo": event_logo,
        "hero_bg": hero_bg,
    }


# ---------------------------------------------------------------------------
# WXR emission
# ---------------------------------------------------------------------------

def emit_attachment_item(rel_path, att, post_date):
    """One <item> of post_type 'attachment' for a single image."""
    guid = att["url"]
    return f"""    <item>
      <title>{xml_escape(att['title'])}</title>
      <link>{xml_escape(att['url'])}</link>
      <pubDate>{wxr_pubdate(post_date)}</pubDate>
      <dc:creator>{cdata(AUTHOR_LOGIN)}</dc:creator>
      <guid isPermaLink="false">{xml_escape(guid)}</guid>
      <description></description>
      <content:encoded>{cdata('')}</content:encoded>
      <excerpt:encoded>{cdata('')}</excerpt:encoded>
      <wp:post_id>{att['id']}</wp:post_id>
      <wp:post_date>{wxr_post_date(post_date)}</wp:post_date>
      <wp:post_date_gmt>{wxr_post_date(post_date)}</wp:post_date_gmt>
      <wp:comment_status>closed</wp:comment_status>
      <wp:ping_status>closed</wp:ping_status>
      <wp:post_name>{xml_escape(slug_from_filename(rel_path))}</wp:post_name>
      <wp:status>inherit</wp:status>
      <wp:post_parent>0</wp:post_parent>
      <wp:menu_order>0</wp:menu_order>
      <wp:post_type>attachment</wp:post_type>
      <wp:post_password></wp:post_password>
      <wp:is_sticky>0</wp:is_sticky>
      <wp:attachment_url>{xml_escape(att['url'])}</wp:attachment_url>
      <wp:postmeta>
        <wp:meta_key>_wp_attached_file</wp:meta_key>
        <wp:meta_value>{cdata(rel_path)}</wp:meta_value>
      </wp:postmeta>
    </item>
"""


def emit_post_item(article, post_id, hero_att):
    tags_xml = "\n".join(
        f'      <category domain="post_tag" nicename="{xml_escape(re.sub(r"[^a-z0-9-]+", "-", t.lower()).strip("-"))}">{cdata(t)}</category>'
        for t in article["tags"]
    )
    thumb_meta = ""
    if hero_att:
        thumb_meta = f"""      <wp:postmeta>
        <wp:meta_key>_thumbnail_id</wp:meta_key>
        <wp:meta_value>{cdata(str(hero_att['id']))}</wp:meta_value>
      </wp:postmeta>
"""
    return f"""    <item>
      <title>{xml_escape(article['title'])}</title>
      <link>{xml_escape(BASE_URL + '/' + article['slug'] + '/')}</link>
      <pubDate>{wxr_pubdate(article['date'])}</pubDate>
      <dc:creator>{cdata(AUTHOR_LOGIN)}</dc:creator>
      <guid isPermaLink="false">{xml_escape(BASE_URL + '/?p=' + str(post_id))}</guid>
      <description></description>
      <content:encoded>{cdata(article['content_html'])}</content:encoded>
      <excerpt:encoded>{cdata('')}</excerpt:encoded>
      <wp:post_id>{post_id}</wp:post_id>
      <wp:post_date>{wxr_post_date(article['date'])}</wp:post_date>
      <wp:post_date_gmt>{wxr_post_date(article['date'])}</wp:post_date_gmt>
      <wp:comment_status>closed</wp:comment_status>
      <wp:ping_status>closed</wp:ping_status>
      <wp:post_name>{xml_escape(article['slug'])}</wp:post_name>
      <wp:status>publish</wp:status>
      <wp:post_parent>0</wp:post_parent>
      <wp:menu_order>0</wp:menu_order>
      <wp:post_type>post</wp:post_type>
      <wp:post_password></wp:post_password>
      <wp:is_sticky>0</wp:is_sticky>
{tags_xml}
{thumb_meta}    </item>
"""


def emit_property_item(prop, post_id, hero_att):
    def meta(key, value):
        return f"""      <wp:postmeta>
        <wp:meta_key>{xml_escape(key)}</wp:meta_key>
        <wp:meta_value>{cdata(value)}</wp:meta_value>
      </wp:postmeta>
"""

    metas = []
    metas.append(meta("nsml_location", prop["location"]))
    metas.append(meta("nsml_hero_tag", prop["hero_tag"]))
    metas.append(meta("nsml_official_website", prop["website"]))
    metas.append(meta("nsml_organizer_type", prop["organizer_type"]))
    metas.append(meta("nsml_next_edition", prop["next_edition"]))
    metas.append(meta("nsml_about", prop["about_html"]))
    metas.append(meta("nsml_stats_json", json.dumps(prop["stats"], ensure_ascii=False)))

    gallery_urls = []
    for g in prop["gallery"]:
        att = register_attachment(g["src"], referenced_from=prop["filename"])
        gallery_urls.append({"url": att["url"], "wide": g["wide"]})
    metas.append(meta("nsml_gallery_urls_json", json.dumps(gallery_urls, ensure_ascii=False)))

    if prop["sponsor_image"]:
        att = register_attachment(prop["sponsor_image"], referenced_from=prop["filename"])
        metas.append(meta("nsml_sponsor_image_url", att["url"]))

    if prop["event_logo"]:
        att = register_attachment(prop["event_logo"], referenced_from=prop["filename"])
        metas.append(meta("nsml_event_logo_url", att["url"]))

    thumb_meta = ""
    if hero_att:
        thumb_meta = meta("_thumbnail_id", str(hero_att["id"]))

    metas_xml = "\n".join(metas)
    pub_date = datetime(2024, 1, 1, 12, 0, 0, tzinfo=timezone.utc)

    return f"""    <item>
      <title>{xml_escape(prop['title'])}</title>
      <link>{xml_escape(BASE_URL + '/properties/' + prop['slug'] + '/')}</link>
      <pubDate>{wxr_pubdate(pub_date)}</pubDate>
      <dc:creator>{cdata(AUTHOR_LOGIN)}</dc:creator>
      <guid isPermaLink="false">{xml_escape(BASE_URL + '/?post_type=nsml_property&p=' + str(post_id))}</guid>
      <description></description>
      <content:encoded>{cdata(prop['about_html'])}</content:encoded>
      <excerpt:encoded>{cdata('')}</excerpt:encoded>
      <wp:post_id>{post_id}</wp:post_id>
      <wp:post_date>{wxr_post_date(pub_date)}</wp:post_date>
      <wp:post_date_gmt>{wxr_post_date(pub_date)}</wp:post_date_gmt>
      <wp:comment_status>closed</wp:comment_status>
      <wp:ping_status>closed</wp:ping_status>
      <wp:post_name>{xml_escape(prop['slug'])}</wp:post_name>
      <wp:status>publish</wp:status>
      <wp:post_parent>0</wp:post_parent>
      <wp:menu_order>0</wp:menu_order>
      <wp:post_type>nsml_property</wp:post_type>
      <wp:post_password></wp:post_password>
      <wp:is_sticky>0</wp:is_sticky>
{thumb_meta}{metas_xml}
    </item>
"""


WXR_HEADER = """<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"
  xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:wfw="http://wellformedweb.org/CommentAPI/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:wp="http://wordpress.org/export/1.2/"
>
<channel>
  <title>{site_title}</title>
  <link>{base_url}</link>
  <description>{site_description}</description>
  <pubDate>{pub_date}</pubDate>
  <language>en-US</language>
  <wp:wxr_version>1.2</wp:wxr_version>
  <wp:base_site_url>{base_url}</wp:base_site_url>
  <wp:base_blog_url>{base_url}</wp:base_blog_url>

  <wp:author>
    <wp:author_id>1</wp:author_id>
    <wp:author_login>{author_login}</wp:author_login>
    <wp:author_email>{author_email}</wp:author_email>
    <wp:author_display_name>{cdata_author_display_name}</wp:author_display_name>
    <wp:author_first_name><![CDATA[]]></wp:author_first_name>
    <wp:author_last_name><![CDATA[]]></wp:author_last_name>
  </wp:author>

  <generator>https://wordpress.org/?v=6.6</generator>

"""

WXR_FOOTER = """
</channel>
</rss>
"""


def php_str(value):
    """A safely single-quoted PHP string literal."""
    return "'" + str(value).replace("\\", "\\\\").replace("'", "\\'") + "'"


def php_value(value, indent=0):
    pad = "    " * indent
    pad_in = "    " * (indent + 1)
    if value is None:
        return "null"
    if isinstance(value, bool):
        return "true" if value else "false"
    if isinstance(value, (int, float)):
        return str(value)
    if isinstance(value, str):
        return php_str(value)
    if isinstance(value, dict):
        if not value:
            return "array()"
        lines = [pad + "array("]
        for k, v in value.items():
            lines.append(f"{pad_in}{php_str(k)} => {php_value(v, indent + 1)},")
        lines.append(pad + ")")
        return "\n".join(lines)
    if isinstance(value, list):
        if not value:
            return "array()"
        lines = [pad + "array("]
        for v in value:
            lines.append(f"{pad_in}{php_value(v, indent + 1)},")
        lines.append(pad + ")")
        return "\n".join(lines)
    raise TypeError(f"Cannot serialize value of type {type(value)} to PHP")


def copy_attachment_files():
    """Copy every referenced image into demo-content/images/<rel_path>,
    preserving the same relative path structure used in the WXR, so the
    custom local importer (inc/custom-importer.php) never needs to fetch
    anything over HTTP -- it just reads these bundled files straight off
    disk into the Media Library."""
    import shutil
    copied = 0
    for rel_path in ATTACHMENTS:
        src = os.path.join(REPO_ROOT, rel_path)
        dst = os.path.join(MANIFEST_IMAGES_DIR, rel_path)
        os.makedirs(os.path.dirname(dst), exist_ok=True)
        shutil.copyfile(src, dst)
        copied += 1
    return copied


def export_manifest(articles, properties):
    """Write demo-content/manifest.php: a plain PHP array (no XML, no
    remote HTTP fetch) consumed by inc/custom-importer.php to populate
    posts/properties/pages directly from files bundled with the theme."""
    posts_data = []
    for art in articles:
        hero_rel = repo_relpath(art["hero_image"]) if art["hero_image"] else None
        posts_data.append({
            "slug": art["slug"],
            "title": art["title"],
            "date": wxr_post_date(art["date"]),
            "content": art["content_html"],
            "tags": art["tags"],
            "hero_image": hero_rel,
        })

    properties_data = []
    for prop in properties:
        gallery = [
            {"image": repo_relpath(g["src"]), "wide": g["wide"]}
            for g in prop["gallery"]
        ]
        properties_data.append({
            "slug": prop["slug"],
            "title": prop["title"],
            "location": prop["location"],
            "hero_tag": prop["hero_tag"],
            "website": prop["website"],
            "organizer_type": prop["organizer_type"],
            "next_edition": prop["next_edition"],
            "about_html": prop["about_html"],
            "stats": prop["stats"],
            "gallery": gallery,
            "sponsor_image": repo_relpath(prop["sponsor_image"]) if prop["sponsor_image"] else None,
            "event_logo": repo_relpath(prop["event_logo"]) if prop["event_logo"] else None,
            "hero_image": repo_relpath(prop["hero_bg"]) if prop["hero_bg"] else None,
        })

    pages_data = [
        {"slug": "about", "title": "About", "template": "page-about.php"},
        {"slug": "services", "title": "Services", "template": "page-services.php"},
        {"slug": "contact", "title": "Contact", "template": "page-contact.php"},
        {"slug": "home", "title": "Home", "template": ""},
        {"slug": "news", "title": "News", "template": ""},
    ]

    manifest = {
        "posts": posts_data,
        "properties": properties_data,
        "pages": pages_data,
    }

    php_code = (
        "<?php\n"
        "/**\n"
        " * Auto-generated by tools/generate_wxr.py. Do not edit by hand --\n"
        " * re-run the generator script instead. Consumed by\n"
        " * inc/custom-importer.php. Every image path here is relative to\n"
        " * demo-content/images/ (bundled with the theme; no remote fetch).\n"
        " */\n\n"
        "if ( ! defined( 'ABSPATH' ) ) {\n\texit;\n}\n\n"
        "return " + php_value(manifest) + ";\n"
    )

    os.makedirs(os.path.dirname(MANIFEST_PATH), exist_ok=True)
    with open(MANIFEST_PATH, "w", encoding="utf-8") as f:
        f.write(php_code)

    n_images = copy_attachment_files()
    print(f"\nWrote {MANIFEST_PATH}")
    print(f"  posts:       {len(posts_data)}")
    print(f"  properties:  {len(properties_data)}")
    print(f"  pages:       {len(pages_data)}")
    print(f"  images copied into {MANIFEST_IMAGES_DIR}: {n_images}")


def main():
    print(f"Repo root: {REPO_ROOT}")
    print(f"Base URL (from CNAME): {BASE_URL}")

    article_files = find_article_files()
    print(f"Found {len(article_files)} article files.")
    if len(article_files) != 16:
        print("ERROR: expected exactly 16 blog article HTML files, found "
              f"{len(article_files)}:", file=sys.stderr)
        for f in article_files:
            print(f"  - {f}", file=sys.stderr)
        sys.exit(1)

    for f in PROPERTY_FILES:
        if not os.path.exists(os.path.join(REPO_ROOT, f)):
            print(f"ERROR: expected property file missing: {f}", file=sys.stderr)
            sys.exit(1)

    articles = [parse_article(f) for f in article_files]
    properties = [parse_property(f) for f in PROPERTY_FILES]

    # --- Register hero/article images, verifying existence -----------------
    article_hero_attachments = {}
    for art in articles:
        if art["hero_image"]:
            att = register_attachment(art["hero_image"], referenced_from=art["filename"])
            article_hero_attachments[art["slug"]] = att
        else:
            print(f"WARNING: no hero image found for article '{art['filename']}'", file=sys.stderr)
            article_hero_attachments[art["slug"]] = None

    property_hero_attachments = {}
    for prop in properties:
        if prop["hero_bg"]:
            att = register_attachment(prop["hero_bg"], referenced_from=prop["filename"])
            property_hero_attachments[prop["slug"]] = att
        else:
            property_hero_attachments[prop["slug"]] = None

        if prop["event_logo"]:
            register_attachment(prop["event_logo"], referenced_from=prop["filename"])
        if prop["sponsor_image"]:
            register_attachment(prop["sponsor_image"], referenced_from=prop["filename"])
        for g in prop["gallery"]:
            register_attachment(g["src"], referenced_from=prop["filename"])

    # --- Abort if any referenced image is missing on disk -------------------
    if MISSING_FILES:
        print("\nERROR: the following referenced images do NOT exist on disk:", file=sys.stderr)
        for rel_path, src in MISSING_FILES:
            print(f"  - {rel_path}   (referenced from {src})", file=sys.stderr)
        print(f"\n{len(MISSING_FILES)} missing file(s). Aborting WXR generation.", file=sys.stderr)
        sys.exit(1)

    print(f"All {len(ATTACHMENTS)} referenced images verified to exist on disk.")

    # --- Build XML ------------------------------------------------------------
    now = datetime.now(timezone.utc)
    parts = [WXR_HEADER.format(
        site_title=xml_escape(SITE_TITLE),
        base_url=xml_escape(BASE_URL),
        site_description=xml_escape(SITE_DESCRIPTION),
        pub_date=wxr_pubdate(now),
        author_login=xml_escape(AUTHOR_LOGIN),
        author_email=xml_escape(AUTHOR_EMAIL),
        cdata_author_display_name=cdata(AUTHOR_DISPLAY_NAME),
    )]

    post_id_map = {}
    for art in articles:
        pid = next_id()
        post_id_map[art["slug"]] = pid
        parts.append(emit_post_item(art, pid, article_hero_attachments[art["slug"]]))

    prop_id_map = {}
    for prop in properties:
        pid = next_id()
        prop_id_map[prop["slug"]] = pid
        parts.append(emit_property_item(prop, pid, property_hero_attachments[prop["slug"]]))

    for rel_path, att in ATTACHMENTS.items():
        parts.append(emit_attachment_item(rel_path, att, now))

    parts.append(WXR_FOOTER)

    xml_doc = "".join(parts)

    os.makedirs(os.path.dirname(OUTPUT_PATH), exist_ok=True)
    with open(OUTPUT_PATH, "w", encoding="utf-8") as f:
        f.write(xml_doc)

    print(f"\nWrote {OUTPUT_PATH}")
    print(f"  posts:       {len(articles)}")
    print(f"  properties:  {len(properties)}")
    print(f"  attachments: {len(ATTACHMENTS)}")

    # --- Sanity check: well-formed XML ----------------------------------------
    import xml.etree.ElementTree as ET
    try:
        ET.parse(OUTPUT_PATH)
        print("XML well-formedness check: PASS")
    except ET.ParseError as e:
        print(f"XML well-formedness check: FAIL ({e})", file=sys.stderr)
        sys.exit(1)

    export_manifest(articles, properties)


if __name__ == "__main__":
    main()
