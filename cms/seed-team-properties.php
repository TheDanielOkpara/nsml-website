<?php
// One-time CLI migration: seeds team_members and properties from the data
// that was previously hardcoded in about.html and properties.html.
// Run via `php cms/seed-team-properties.php`.
require_once __DIR__ . '/includes/db.php';
$pdo = db();

$team = [
    ['name' => 'Olopade Yetunde', 'role' => 'Chief Executive Officer', 'photo' => 'images/team/yetunde.jpg', 'is_ceo' => 1, 'sort_order' => 0,
     'bio' => "A seasoned expert in events marketing, sponsorship activation, and brand partnerships with over 20 years of experience. Yetunde specialises in large-scale sports events, corporate collaborations, and impactful brand engagements — driving Nilayo's strategic vision and setting industry benchmarks in sports marketing across Africa."],
    ['name' => 'Olopade Adenike', 'role' => 'Chief Operating Officer', 'photo' => 'images/team/adenike.jpg', 'is_ceo' => 0, 'sort_order' => 1,
     'bio' => "The operational architect behind NSML's growth. Adenike ensures that every sponsorship commitment, event logistics plan, and client relationship is executed to the highest standard. She brings the same precision to a community 10KM race as to a World Athletics-certified marathon — because NSML's reputation is built on delivery, every single time."],
    ['name' => 'Odeh Emmanuel', 'role' => 'Project Lead', 'photo' => 'images/team/emmanuel.jpg', 'is_ceo' => 0, 'sort_order' => 2,
     'bio' => "A results-driven professional with expertise in business development, entrepreneurship, and team leadership. With extensive experience in project execution, Emmanuel drives business growth and operational efficiency. Backed by certifications in Business Administration and Management, his strategic vision and hands-on leadership make him a key asset in project management and business development at Nilayo."],
    ['name' => 'Adekite Bolaji', 'role' => 'Admin', 'photo' => 'images/team/bolaji.jpg', 'is_ceo' => 0, 'sort_order' => 3,
     'bio' => "An experienced HR professional with a BA in English Language, an MBA, and multiple professional certifications. Bolaji specialises in talent management, employee relations, and organisational development — nurturing a dynamic work environment. Committed to excellence and innovation, she drives Nilayo Sports' success by attracting and developing top talent."],
    ['name' => 'Odi Jide', 'role' => 'Brand & Comms Lead', 'photo' => 'images/team/jide.jpg', 'is_ceo' => 0, 'sort_order' => 4,
     'bio' => "A seasoned media and communications professional with expertise in broadcast journalism, brand strategy, creative advertising, and media production. With over a decade of experience, Jide blends analytical insight with creative storytelling to strengthen Nilayo's brand presence. A trained journalist from the Nigerian Institute of Journalism and an alumnus of O2 Academy Lagos, he plays a key role in shaping the company's communication and marketing strategies."],
    ['name' => 'Omoniyi Sandra', 'role' => 'Business Strategist', 'photo' => 'images/team/sandra.jpg', 'is_ceo' => 0, 'sort_order' => 5,
     'bio' => "A specialist in sports management, strategy, and operations with a Master's in Sport Management and Administration and over a decade of experience. Sandra develops strategic solutions that drive revenue and brand growth, excels in forging high-impact partnerships, and creates sustainable business models that elevate sports properties. She combines strategic insight with deep industry expertise to deliver innovative solutions for brands and stakeholders."],
];

$teamStmt = $pdo->prepare(
    'INSERT INTO team_members (name, role, photo, bio, is_ceo, sort_order) VALUES (?,?,?,?,?,?)'
);
foreach ($team as $t) {
    $teamStmt->execute([$t['name'], $t['role'], $t['photo'], $t['bio'], $t['is_ceo'], $t['sort_order']]);
    echo "Team member: {$t['name']}\n";
}

$properties = [
    ['slug' => 'lagos-marathon', 'title' => 'Access Bank Lagos City Marathon', 'tag' => 'Flagship Property', 'badge' => 'World Athletics',
     'hero_image' => 'images/events/lagos/lagos-card.jpg', 'logo_image' => 'images/events/ablcm-logo-[no-year].png',
     'description' => "Africa's Strongest Marathon Brand 2025 — a World Athletics Global Certification. The 10th anniversary edition delivered in 2025 drew participation from across the globe, backed by Access Bank, KIA Motors, Seven Up, Aquafina, and Airtel. Raises over ₦3 billion annually from the private sector since its inception in 2016.",
     'stat1_val' => '440K+', 'stat1_lbl' => 'Participants since 2016', 'stat2_val' => '₦3B+', 'stat2_lbl' => 'Raised annually', 'stat3_val' => 'Feb 2027', 'stat3_lbl' => 'Next edition',
     'detail_url' => 'lagos-marathon.html', 'is_featured' => 1, 'is_upcoming' => 0, 'sort_order' => 0],
    ['slug' => 'abuja-marathon', 'title' => 'PremiumTrust Bank Abuja City International Half Marathon', 'tag' => 'International', 'badge' => null,
     'hero_image' => 'images/events/abuja-hero.jpg', 'logo_image' => 'images/events/abj-logo-new.png',
     'description' => "The capital's premier long-distance race. Inaugural edition April 2024 with 50,000+ participants. Backed by PremiumTrust Bank, Dana Airlines, Seven Up, Aquafina, and the AFN.",
     'stat1_val' => '50K+', 'stat1_lbl' => 'Participants', 'stat2_val' => 'Nov 2026', 'stat2_lbl' => 'Next edition', 'stat3_val' => null, 'stat3_lbl' => null,
     'detail_url' => 'abuja-marathon.html', 'is_featured' => 0, 'is_upcoming' => 0, 'sort_order' => 1],
    ['slug' => 'abeokuta-race', 'title' => 'Abeokuta 10KM Race', 'tag' => 'Heritage Race', 'badge' => null,
     'hero_image' => 'images/events/abeokuta-hero.jpg', 'logo_image' => 'images/events/abk-logo.png',
     'description' => 'A race rich in cultural significance since 2019. Long-term commitments from Lotus Bank, Access Bank, Rite Foods, JAC Motors, Airtel, Trophy, Lafarge, and more.',
     'stat1_val' => '120K+', 'stat1_lbl' => 'Participants', 'stat2_val' => 'Sept 2025', 'stat2_lbl' => 'Next edition', 'stat3_val' => null, 'stat3_lbl' => null,
     'detail_url' => 'abeokuta-race.html', 'is_featured' => 0, 'is_upcoming' => 0, 'sort_order' => 2],
    ['slug' => 'enugu-marathon', 'title' => 'Enugu City International Marathon', 'tag' => 'New Property', 'badge' => null,
     'hero_image' => 'images/events/enugu-hero.jpg', 'logo_image' => 'images/events/enugu-marathon-logo.png',
     'description' => 'Maiden edition May 2025 with 13,000+ runners. Backed by Pinnacle Oil, Air Peace, World Athletics, Three Crowns, RC Cola, and the Athletics Federation of Nigeria.',
     'stat1_val' => '13K+', 'stat1_lbl' => 'Debut runners', 'stat2_val' => '2026', 'stat2_lbl' => 'Next edition', 'stat3_val' => null, 'stat3_lbl' => null,
     'detail_url' => 'enugu-marathon.html', 'is_featured' => 0, 'is_upcoming' => 0, 'sort_order' => 3],
    ['slug' => 'yenagoa-race', 'title' => 'Yenagoa City International 10KM Race', 'tag' => 'New Property', 'badge' => null,
     'hero_image' => 'images/events/yenagoa-hero.jpg', 'logo_image' => 'images/events/yc1km.png',
     'description' => 'First World Athletics-supervised race in Southern Nigeria. Inaugural edition 2026 with 5,000+ runners. Backed by Bayelsa State Government and strategic partners.',
     'stat1_val' => '5K+', 'stat1_lbl' => 'Debut runners', 'stat2_val' => '2027', 'stat2_lbl' => 'Next edition', 'stat3_val' => null, 'stat3_lbl' => null,
     'detail_url' => 'yenagoa-race.html', 'is_featured' => 0, 'is_upcoming' => 0, 'sort_order' => 4],
    ['slug' => 'stormers-club', 'title' => 'Stormers Sports Club', 'tag' => 'Sports Club', 'badge' => null,
     'hero_image' => 'images/events/stormers-hero.jpg', 'logo_image' => 'images/events/stormers-logo.png',
     'description' => "Back-to-back Lisabi Cup Champions 2024 and 2025. Academy built from Baptist Boys High School talent under long-term contracts. Senior team newly promoted to Nigeria's NNL.",
     'stat1_val' => '2x', 'stat1_lbl' => 'Lisabi Champions', 'stat2_val' => 'NNL', 'stat2_lbl' => 'Promoted', 'stat3_val' => null, 'stat3_lbl' => null,
     'detail_url' => 'stormers-club.html', 'is_featured' => 0, 'is_upcoming' => 0, 'sort_order' => 5],
    ['slug' => 'ijebu-marathon', 'title' => 'Ijebu Heritage Half Marathon', 'tag' => 'Heritage Race', 'badge' => null,
     'hero_image' => 'images/events/ijebu-hero.jpg', 'logo_image' => 'images/events/aihhm-2023-logo.png',
     'description' => 'A race steeped in cultural significance, delivered in collaboration with Airtel Nigeria. Maiden edition held 17 July 2021 with 5,000+ runners and spectators.',
     'stat1_val' => '5K+', 'stat1_lbl' => 'Participants', 'stat2_val' => '2021', 'stat2_lbl' => 'Maiden edition', 'stat3_val' => null, 'stat3_lbl' => null,
     'detail_url' => 'ijebu-marathon.html', 'is_featured' => 0, 'is_upcoming' => 0, 'sort_order' => 6],
    ['slug' => 'copa-lagos', 'title' => 'Copa Lagos Beach Soccer', 'tag' => null, 'badge' => 'Upcoming · Dec 2026',
     'hero_image' => 'images/events/copa-lagos-hero.jpg', 'logo_image' => 'images/events/copa-lagos_web.png',
     'description' => "Nigeria's premier beach football and lifestyle event returns to Eko Atlantic City. A three-day high-energy weekend drawing 20,000+ fans, athletes, and lifestyle enthusiasts. Licensee granted by Kinetic Sports — first return since the 2019 edition.",
     'stat1_val' => null, 'stat1_lbl' => null, 'stat2_val' => null, 'stat2_lbl' => null, 'stat3_val' => null, 'stat3_lbl' => null,
     'detail_url' => 'copa-lagos.html', 'is_featured' => 0, 'is_upcoming' => 1, 'sort_order' => 0],
];

$propStmt = $pdo->prepare(
    'INSERT INTO properties (slug, title, tag, badge, hero_image, logo_image, description,
        stat1_val, stat1_lbl, stat2_val, stat2_lbl, stat3_val, stat3_lbl, detail_url, is_featured, is_upcoming, sort_order)
     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
     ON DUPLICATE KEY UPDATE title=VALUES(title)'
);
foreach ($properties as $p) {
    $propStmt->execute([
        $p['slug'], $p['title'], $p['tag'], $p['badge'], $p['hero_image'], $p['logo_image'], $p['description'],
        $p['stat1_val'], $p['stat1_lbl'], $p['stat2_val'], $p['stat2_lbl'], $p['stat3_val'], $p['stat3_lbl'],
        $p['detail_url'], $p['is_featured'], $p['is_upcoming'], $p['sort_order'],
    ]);
    echo "Property: {$p['title']}\n";
}

echo "\nDone.\n";
