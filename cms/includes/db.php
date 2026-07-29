<?php
require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

// Self-creates the event_banners table on first use, same pattern as
// login_attempts in auth.php — no manual migration step needed on deploy.
function ensure_event_banners_table(): void {
    static $ensured = false;
    if ($ensured) return;
    db()->exec('CREATE TABLE IF NOT EXISTS event_banners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_path VARCHAR(500) NOT NULL,
        link_url VARCHAR(500) NOT NULL,
        title VARCHAR(255),
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    $ensured = true;
}

// Self-creates and self-seeds the partner_logos table on first use. Seeds
// from the logo set that used to be hardcoded into every page's marquee, so
// the migration to CMS-managed logos doesn't lose any existing partners.
// Seeding only runs once (when the table is empty) so admin edits/deletes
// afterward are never overwritten by a redeploy.
function ensure_partner_logos_table(): void {
    static $ensured = false;
    if ($ensured) return;
    db()->exec('CREATE TABLE IF NOT EXISTS partner_logos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_path VARCHAR(500) NOT NULL,
        name VARCHAR(255) NOT NULL,
        link_url VARCHAR(500),
        row_num TINYINT NOT NULL DEFAULT 1,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $count = (int) db()->query('SELECT COUNT(*) FROM partner_logos')->fetchColumn();
    if ($count === 0) {
        $row1 = [
            ['images/partners/web/pngroyale.com-access-bank-plc-logo.png', 'Access Bank'],
            ['images/partners/web/premium-trust-logo-colour-png.png', 'PremiumTrust Bank'],
            ['images/partners/web/airtel-logo.png', 'Airtel Nigeria'],
            ['images/partners/web/new-kia-logo.png', 'KIA Motors'],
            ['images/partners/web/afnlogo.png', 'AFN'],
            ['images/partners/web/dana.png', 'Dana Airlines'],
            ['images/partners/web/air-peace-icon-2048x407-x77lwkmv.png', 'Air Peace'],
            ['images/partners/web/pinnacle-logo.png', 'Pinnacle Oil'],
            ['images/partners/web/aquavie.png', 'Aquavie'],
            ['images/partners/web/firstbank-logo.png', 'First Bank'],
            ['images/partners/web/keystone.png', 'Keystone Bank'],
            ['images/partners/web/new-mtn-logo.png', 'MTN'],
            ['images/partners/web/unilever.png', 'Unilever'],
            ['images/partners/web/rite-logo.png', 'Rite Foods'],
            ['images/partners/web/valuejet_approved_logo_png.png', 'Value Jet'],
            ['images/partners/web/kenya_airways-logo.wine.png', 'Kenya Airways'],
            ['images/partners/web/casio-logo.png', 'Casio'],
            ['images/partners/web/ogun-state-logo.png', 'Ogun State'],
            ['images/partners/web/bayelsa-sports-logo.png', 'Bayelsa Sports'],
            ['images/partners/web/ramecgroup-logo.png', 'Ramec Group'],
            ['images/partners/web/greenlife-pharmaceuticals-logo.png', 'Greenlife'],
            ['images/partners/web/febbs-premium-drinking-water.png', 'FEBBS Water'],
            ['images/partners/web/binad-table-water.png', 'Binad Water'],
            ['images/partners/web/tcm-logo.png', 'TCM'],
            ['images/partners/web/rexona_logo_2015.svg.png', 'Rexona'],
            ['images/partners/web/premier-cool-deo-01.png', 'Premier Cool'],
            ['images/partners/web/vitabol-hd-logo.png', 'Vitabol'],
            ['images/partners/web/waka.png', 'Waka'],
            ['images/partners/web/what-network-logo.png', 'What Network'],
            ['images/partners/web/robb-logo.png', 'Robb'],
            ['images/partners/web/rpp-logo.png', 'RPP'],
            ['images/partners/web/ogsaa.png', 'OGSAA'],
        ];
        $row2 = [
            ['images/partners/web/bet9ja-logo.png', 'Bet9ja'],
            ['images/partners/web/cashtoken.png', 'Cash Token'],
            ['images/partners/web/channelstv-logo-new-1024x941.png', 'Channels TV'],
            ['images/partners/web/eko-atlantic-logo-clean.png', 'Eko Atlantic'],
            ['images/partners/web/nord.png', 'Nord'],
            ['images/partners/web/brila-green-logo-with-fm-.png', 'Brila FM'],
            ['images/partners/web/lasaa.png', 'LASAA'],
            ['images/partners/web/fatgbems.png', 'Fatgbems'],
            ['images/partners/web/hertage.png', 'Heritage Bank'],
            ['images/partners/web/oraimo_logo2.0.png', 'Oraimo'],
            ['images/partners/web/easytipping-front-logo.png', 'EasyTipping'],
            ['images/partners/web/2sure-logo.png', '2Sure'],
            ['images/partners/web/lssc-new-logo.png', 'LSSC'],
            ['images/partners/web/royal-crown-cola-logo-aefc4cb9e1-seeklogo.com.png', 'Royal Crown Cola'],
            ['images/partners/web/comag-logo-2023-new.png', 'Comag'],
            ['images/partners/web/conference-hotel-logo.png', 'Conference Hotel'],
            ['images/partners/web/aims.png', 'AIMS'],
            ['images/partners/web/fct.png', 'FCT'],
            ['images/partners/web/lag.png', 'LAG'],
            ['images/partners/web/peculiar.png', 'Peculiar'],
            ['images/partners/web/cr.png', 'CR'],
            ['images/partners/web/joy.png', 'Joy'],
            ['images/partners/web/mf-logo1.png', 'MF'],
            ['images/partners/web/lockup-transparent-background-01.png', 'Partner'],
            ['images/partners/web/logo_31710a86f0b01cc31d0a2f0c263ad8d4_2x.png', 'Partner'],
            ['images/partners/web/layer-1-copy-3.png', 'Partner'],
            ['images/partners/web/img_0537.png', 'Partner'],
            ['images/partners/web/img_0538.png', 'Partner'],
            ['images/partners/web/aron-.png', 'Partner'],
            ['images/partners/web/2017_1large_atb.png', 'ATB'],
            ['images/partners/web/1519896687213.png', 'Partner'],
            ['images/partners/web/1280px-suzuki_logo_2.svg.png', 'Suzuki'],
            ['images/partners/web/png-clipart-bayelsa-state-osun-state-rivers-state-kaduna-state-coat-of-arms-osun-state-bayelsa-state-rivers-state-removebg-preview.png', 'Bayelsa State'],
        ];

        $stmt = db()->prepare('INSERT INTO partner_logos (image_path, name, row_num, sort_order) VALUES (?, ?, ?, ?)');
        foreach ([1 => $row1, 2 => $row2] as $rowNum => $logos) {
            foreach ($logos as $i => [$path, $name]) {
                $stmt->execute(['/' . $path, $name, $rowNum, $i]);
            }
        }
    }
    $ensured = true;
}
