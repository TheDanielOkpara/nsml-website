<?php
// One-time CLI migration: parses the existing static article HTML files
// (built on the article.html / WP-comment template) and inserts them into
// blog_posts. Run once via `php cms/seed-from-static.php` after importing
// schema.sql, then delete the static article .html files once confirmed.
require_once __DIR__ . '/includes/db.php';

$root = dirname(__DIR__);
$files = [
    'arrangements-in-top-gear-for-gold-label-access-bank-lagos-city-marathon.html',
    'bayelsa-state-government-and-nilayo-sports-management-limited-announce-the-inaugural-yenagoa-city-international-10km-race.html',
    'betking-sponsors-all-para-sports-at-national-sports-festival-delta-2022.html',
    'ceo-of-nilayo-sports-management-limited-mrs-yetunde-olopade-wins-prestigious-newstap-swan-5-star-award.html',
    'chief-yetunde-olopade-md-ceo-nilayo-sports-management-limited-congratulates-lagos-state-on-the-historic-e1-lagos-grand-prix.html',
    'damilola-pedro-bags-award.html',
    'delta-2022-theme-song-challenge.html',
    'enugu-nilayo-seal-deal-on-marathon-sports-festival.html',
    'lotus-bank-abeokuta-10km-race-nilayo-sports-febbs-premium-water-sign-partnership-deal.html',
    'nilayo-partners-fmysd-to-deliver-cross-country-race-in-jos.html',
    'nilayo-sports-management-md-chief-yetunde-olopade-named-among-2025-top-50-most-influential-african-women-in-sports.html',
    'nilayo-to-sign-multiple-multi-billion-naira-deals-for-abuja-city-international-marathon.html',
    'ogun-set-to-host-nsf-2024-olopade-adebajo-others-to-lead-16-man-committee.html',
    'premium-trust-bank-announced-as-official-banking-partner-of-athletics-federation-of-nigeria.html',
    'premium-trust-bank-announced-as-official-banking-partner-of-national-sports-festival-delta-2022.html',
    'unveiling-of-the-21st-national-sports-festival-delta-2022-logo-mascot.html',
];

$inserted = 0;
$skipped = [];

foreach ($files as $file) {
    $path = $root . '/' . $file;
    if (!is_file($path)) { $skipped[] = "$file (not found)"; continue; }
    $html = file_get_contents($path);

    $slug = preg_replace('/\.html$/', '', $file);

    // Only search the <body> — the <head> contains a WP-conversion comment that
    // also literally contains the string '<h1 class="article-hero-title">'.
    $bodyHtml = preg_match('/<body.*<\/html>/is', $html, $bm) ? $bm[0] : $html;

    if (!preg_match('/<h1 class="article-hero-title">\s*(.*?)\s*<\/h1>/s', $bodyHtml, $m)) {
        $skipped[] = "$file (no title found)"; continue;
    }
    $title = trim(html_entity_decode(strip_tags($m[1])));

    $coverImage = null;
    if (preg_match('/id="heroParallax"\s+style="background-image:url\(\'(.*?)\'\)"/', $bodyHtml, $m)) {
        $coverImage = $m[1];
    }

    $body = '';
    if (preg_match('/<div class="article-body">(.*?)<\/div><!-- \/article-body -->/s', $bodyHtml, $m)) {
        $body = trim($m[1]);
        // Drop the static tags block — there's no tag taxonomy in the new schema.
        $body = preg_replace('/<div class="article-tags">.*?<\/div>\s*$/s', '', $body);
    }

    $excerpt = null;
    if (preg_match('/<p class="intro">\s*(.*?)\s*<\/p>/s', $bodyHtml, $m)) {
        $excerpt = mb_substr(trim(strip_tags($m[1])), 0, 220);
    }

    $publishedAt = null;
    if (preg_match('/<div class="meta-item">([A-Za-z]+ \d{1,2}, \d{4})<\/div>/', $bodyHtml, $m)) {
        $ts = strtotime($m[1]);
        if ($ts) $publishedAt = date('Y-m-d', $ts);
    }

    $stmt = db()->prepare(
        'INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published)
         VALUES (?, ?, ?, ?, ?, ?, 1)
         ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body),
           cover_image=VALUES(cover_image), published_at=VALUES(published_at)'
    );
    $stmt->execute([$slug, $title, $excerpt, $body, $coverImage, $publishedAt]);
    $inserted++;
    echo "Imported: $title\n";
}

echo "\nDone. Imported/updated {$inserted} posts.\n";
if ($skipped) {
    echo "Skipped:\n - " . implode("\n - ", $skipped) . "\n";
}
