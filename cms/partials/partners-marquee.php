<?php
// Renders the "Trusted Partners & Affiliations" marquee from the CMS-managed
// partner_logos table. Include with: require __DIR__ . '/cms/partials/partners-marquee.php';
require_once __DIR__ . '/../includes/db.php';
ensure_partner_logos_table();

$partnerRows = db()->query('SELECT * FROM partner_logos WHERE is_active = 1 ORDER BY row_num ASC, sort_order ASC, id ASC')->fetchAll();
$partnerRow1 = array_filter($partnerRows, fn($p) => (int)$p['row_num'] === 1);
$partnerRow2 = array_filter($partnerRows, fn($p) => (int)$p['row_num'] === 2);

function render_partner_logo_tag(array $logo): void {
    $img = '<img src="' . htmlspecialchars($logo['image_path']) . '" alt="' . htmlspecialchars($logo['name']) . '" loading="lazy" class="partner-logo">';
    if (!empty($logo['link_url'])) {
        echo '<a href="' . htmlspecialchars($logo['link_url']) . '" target="_blank" rel="noopener" style="display:flex;align-items:center;">' . $img . '</a>';
    } else {
        echo $img;
    }
}
?>
<?php if ($partnerRow1 || $partnerRow2): ?>
<!-- PARTNERS LOGO MARQUEE (CMS-managed: cms/admin/partners.php) -->
<div class="partners">
  <div class="partners-lbl">Trusted Partners &amp; Affiliations</div>
  <?php if ($partnerRow1): ?>
  <div class="partners-track">
    <div class="partners-set">
      <?php foreach ($partnerRow1 as $logo): render_partner_logo_tag($logo); endforeach; ?>
    </div>
    <div class="partners-set" aria-hidden="true">
      <?php foreach ($partnerRow1 as $logo): render_partner_logo_tag($logo); endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
  <?php if ($partnerRow2): ?>
  <div class="partners-track partners-track-reverse">
    <div class="partners-set">
      <?php foreach ($partnerRow2 as $logo): render_partner_logo_tag($logo); endforeach; ?>
    </div>
    <div class="partners-set" aria-hidden="true">
      <?php foreach ($partnerRow2 as $logo): render_partner_logo_tag($logo); endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>
