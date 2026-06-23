-- Self-contained blog seed (generated from the original static article pages).
-- Import after schema.sql:  mysql -u USER -p DBNAME < cms/seed-blog.sql

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'arrangements-in-top-gear-for-gold-label-access-bank-lagos-city-marathon',
  'ARRANGEMENTS IN TOP GEAR FOR GOLD-LABEL ACCESS BANK LAGOS CITY MARATHON',
  'The organisers of the Gold-Label Access Bank Lagos City Marathon, Nilayo Sports Management Limited, has declared the organisation’s readiness for all peculiarities of the host City, Lagos, as they conclude preparations f',
  '<p class="intro">
        The organisers of the Gold-Label Access Bank Lagos City Marathon, Nilayo Sports Management Limited, has declared the organisation’s readiness for all peculiarities of the host City, Lagos, as they conclude preparations for the February 4 Gold- Label Race, the first of its kind in Nigeria.
      </p>

      <p>
        The Managing Director of Nilayo Sports Management Limited and initiator of the Access Bank Lagos City Marathon, Honourable Bukola Olopade, disclosed Monday that, his team led by its General Manager, Yusuf Ali, is ready for all the peculliarities of Lagos to ensure the success of the first Gold-Label full marathon race in Nigeria.
      </p>

      <p>
        Olopade, noted: “At last year’s race, we noticed some darkness at the starting point, which obstructed some of the opening formalitites, such that we had to delay the flag off for some minutes.
      </p>

      <p>
        “That is why, for this year’s race, the official flag-off time has been readjusted and rescheduled for 7am and not 6am as it used to be in previous races.”
      </p>

      <p>
        Olopade stressed that, because of the harmattan weather noticed in Lagos since the beginning of the new year, the organisers will do everything possible to make sure the weather does not affect the timing of the race. For better output and development, we must achieve a road race with a finish time in the region of 2.10 hours, which will go a long way in helping move inches close to our target of getting a Platinum Label race in the nearest future.”
      </p>

      <p>
        The Nilayo Sports Management Limited Chief Executive, disclosed that, to enable the Elite athletes record a good time in the Gold-Label 2023 Access Bank Lagos City Marathon, their date of arrival has been moved forward to February 1, three days ahead of the marathon on February 4.
      </p>

      <p>
        “This early arrival is aimed at giving the athletes the opportunity to understand and perfect the route.
      </p>

      <p>
        “A date has been fixed for the athletes’ tour of the route ahead of the competition date. To help them record good timing on February 4. The online registration of runners ends this Friday, January 13, while the physical registration continues, for better planning.”
      </p>

      <p>
        Olopade added that: “This time around, the top athletes for the Gold-Label Access Bank Lagos City Marathon Road Race will be allowed three pacers each to improve their timing. This is aimed in the Long run to improve the standings and international ratings of the race at World Athletics,” Bukola Olopade said in Lagos.
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2023/01/ABLCM-Logo.2-.png',
  '2023-01-10',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'bayelsa-state-government-and-nilayo-sports-management-limited-announce-the-inaugural-yenagoa-city-international-10km-race',
  'Bayelsa State Government and Nilayo Sports Management Limited Announce the Inaugural Yenagoa City International 10KM Race',
  '&lt;p style=&quot;text-align: justify; margin: 0cm 0cm 12.0pt 0cm;&quot;&gt;&lt;span style=&quot;font-size: 11.0pt; font-family: &#x27;Arial&#x27;,sans-serif; color: #1f1f1f;&quot;&gt;The Bayelsa State Government is prou',
  '<p class="intro">
        &lt;p style=&quot;text-align: justify; margin: 0cm 0cm 12.0pt 0cm;&quot;&gt;&lt;span style=&quot;font-size: 11.0pt; font-family: &#x27;Arial&#x27;,sans-serif; color: #1f1f1f;&quot;&gt;The Bayelsa State Government is proud to officially announce the debut of the Yenagoa City International 10KM Race, a landmark sporting event set to position the Bayelsa State as a premier destination for global sports tourism. Scheduled for Saturday, April 4, 2026, this inaugural race is owned by the Bayelsa State Government and will be expertly organized by Africa’s leading sports management firm, Nilayo Sports Management Limited. Under the theme &lt;/span&gt;&lt;b&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;&quot;The Reveal&quot;&lt;/span&gt;&lt;/b&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;, the event serves as a grand unveiling of the state’s immense human and cultural potential to the international community, inviting the world to witness the grit and talent inherent in the heart of the Niger Delta.&lt;/span&gt;&lt;/p&gt; &lt;p style=&quot;text-align: justify; margin: 0cm 0cm 12.0pt 0cm;&quot;&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;&lt;span style=&quot;font-size: 11.0pt; font-family: &#x27;Arial&#x27;,sans-serif; color: #1f1f1f;&quot;&gt;The race will officially flag off at 9:30 AM from the Opollo Roundabout, taking participants through a meticulously planned 10KM route that showcases the scenic landscapes and vibrant energy of Yenagoa. The course will culminate in a spectacular finish at the Peace Park Square, where the competition transitions into a grand celebration of endurance and community spirit. To ensure the event is a truly unforgettable experience for both athletes and spectators, the finish line will host an electrifying post-race concert featuring high-octane performances from top-tier A-list artistes.&lt;/span&gt;&lt;/span&gt;&lt;/p&gt; &lt;p style=&quot;text-align: justify; margin: 0cm 0cm 12.0pt 0cm;&quot;&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;&lt;span style=&quot;font-size: 11.0pt; font-family: &#x27;Arial&#x27;,sans-serif; color: #1f1f1f;&quot;&gt;Athletes and fitness enthusiasts looking to be part of this historic occasion can register online via the official website at&lt;/span&gt;&lt;/span&gt;&lt;a href=&quot;https://www.google.com/search?q=https://www.yenagoacity10kmrace.com&quot;&gt;&lt;span style=&quot;font-size: 11.0pt; font-family: &#x27;Arial&#x27;,sans-serif; color: #1f1f1f; text-decoration: none; text-underline: none;&quot;&gt; &lt;b&gt;&lt;span style=&quot;-webkit-text-decoration-skip: none; text-decoration-skip-ink: none; white-space: pre-wrap;&quot;&gt;www.yenagoacity10kmrace.com&lt;/span&gt;&lt;/b&gt;&lt;/span&gt;&lt;/a&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;&lt;span style=&quot;font-size: 11.0pt; font-family: &#x27;Arial&#x27;,sans-serif; color: #1f1f1f;&quot;&gt;. Physical registration forms are also available for pickup at the Sports Department Office, Ministry of Sports, Sports Complex, Opposite First Baptist Church, Ovom, Yenagoa, Bayelsa State. Following registration, participants must visit the designated Peace Park for bib and race kit collection, which is scheduled to take place daily from March 30 to April 3, 2026, between 9:00 AM and 4:00 PM.&lt;/span&gt;&lt;/span&gt;&lt;/p&gt; &lt;p style=&quot;text-align: justify; margin: 0cm 0cm 12.0pt 0cm;&quot;&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;&lt;span style=&quot;font-size: 11.0pt; font-family: &#x27;Arial&#x27;,sans-serif; color: #1f1f1f;&quot;&gt;This premier road race is currently on track for World Athletics accreditation, reflecting the commitment of the Bayelsa State Government and Nilayo Sports Management Limited to meeting the highest international standards of excellence. As the countdown to race day begins, the call to the world resonates loud and clear: &lt;/span&gt;&lt;b&gt;&lt;i&gt;Bayelsa, Wuan!!!&lt;/i&gt;&lt;/b&gt;&lt;i&gt; &lt;/i&gt;&lt;/span&gt;&lt;/p&gt; &lt;p style=&quot;text-align: justify; margin: 0cm 0cm 12.0pt 0cm;&quot;&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;&lt;span style=&quot;font-size: 11.0pt; font-family: &#x27;Arial&#x27;,sans-serif; color: #1f1f1f;&quot;&gt;Join us as we shine forth and usher in a new era of global sporting achievement in the heart of the Niger Delta.&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2026/03/sport-c4.jpg-scaled.jpeg',
  '2026-03-09',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'betking-sponsors-all-para-sports-at-national-sports-festival-delta-2022',
  'BETKING SPONSORS ALL PARA SPORTS AT NATIONAL SPORTS FESTIVAL, DELTA 2022',
  '&lt;strong&gt;8&lt;sup&gt;TH&lt;/sup&gt; June 2022&lt;/strong&gt; - Nilayo Sports Management Limited, marketing consultants for National Sports Festival, Delta 2022 has announced BetKing Nigeria as the official sponsor o',
  '<p class="intro">
        &lt;strong&gt;8&lt;sup&gt;TH&lt;/sup&gt; June 2022&lt;/strong&gt; - Nilayo Sports Management Limited, marketing consultants for National Sports Festival, Delta 2022 has announced BetKing Nigeria as the official sponsor of all Para Sports during the Festival.
      </p>

      <p>
        Mr Bukola Olopade, the Chief Executive Officer of Nilayo Sports said “We are delighted to have BetKing on board as official sponsors of all Para Sports at National Sports Festival, Delta 2022. This will greatly increase the confidence of Para Athletes as they will be motivated to remain committed to trailblazing excellence. This will also inspire a lot of young people across the country.
      </p>

      <p>
        “BetKing has taken this bold step to support Para Sports, and this shows that BetKing is an iconic brand completely dedicated to improving the lives of Para Athletes in Nigeria. As we welcome BetKing on board, we once again reiterate that this National Sports Festival will be the best Nigeria has ever witnessed.”
      </p>

      <p>
        Speaking on Betking Nigeria’s sponsorship of the National Sports Festival, Gossy Ukanwoke the Managing Director of KingMakers said “Our sponsorship of Para Sports at the National Sports Festival reflects on our corporate social responsibility agenda to develop sports in Nigeria and support themes and athletes that are otherwise not considered for sponsorship. Our Para Athletes have brought so much glory to Nigeria, and we want to help discover and support the next generation of Para Athletes, who are going above their physical disabilities to express their sportsmanship”
      </p>

      <p>
        The 21st National Sports Festival is scheduled for November 2nd to 15th. Delta state will host athletes from across the country who will participate in 33 sports including eight Para Sports.
      </p>

      <p>
        &lt;strong&gt;NATIONAL SPORTS FESTIVAL&lt;/strong&gt; - The &lt;strong&gt;Nigerian National Sports Festival&lt;/strong&gt; is a biennial multi-sport event organized by the Federal Government of Nigeria through the National Sports Commission for athletes from the 36 States of Nigeria. National Sports Festival started in 1973 as a unifying tool for the promotion of cross-cultural affiliation in Nigeria after the Civil War. The first National Sports Festival was held at the National Stadium, Surulere, Lagos. Delta State has been selected to host the 21&lt;sup&gt;st&lt;/sup&gt; National Sports Festival tagged Delta 2022. Delta state is known for its proactive sports atmosphere. Delta won the highest medals in the 20&lt;sup&gt;th&lt;/sup&gt; edition of the National Sports Festival held in Edo State and the state is ready to deliver the best National Sports Festival since inception.
      </p>

      <p>
        &lt;strong&gt;BETKING &lt;/strong&gt;- is a sports betting and entertainment platform offering online services in Nigeria, Kenya, and Ethiopia, and agency services in Nigeria. Among the offerings of the company’s services are sports betting, not restricted to football, hockey, cricket, tennis, basketball, and more, and customized state-of-the-art virtual games including the exclusive Kings&#x27; League and Colour-Colour. BetKing also offers agency opportunities for individuals who will come to be called Kingmakers once they sign up to deliver offline betting services to customers. BetKing is a KingMakers company and the birth of the brand was initiated by evolution in strategy and the objective to offer more value to customers.
      </p>

      <p>
        &lt;strong&gt;NILAYO SPORTS MANAGEMENT LIMITED&lt;/strong&gt; - NILAYO Sports Management Ltd is a sports management and Sponsorship consulting company, working with some of the biggest names in the world of sports like WORLD ATHLETICS, CAA, AFN and NNL. Our aim is to promote sports from grass root level to international in Africa by bridging the gap between the sports industry and the Private sector, hereby forging alliances through sponsorship and value creation.
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2022/06/BETKING-BLUEYELLOW-LOGO.png',
  '2022-06-17',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'ceo-of-nilayo-sports-management-limited-mrs-yetunde-olopade-wins-prestigious-newstap-swan-5-star-award',
  'CEO of Nilayo Sports Management Limited, Mrs. Yetunde Olopade, Wins Prestigious Newstap/SWAN 5-Star Award',
  'The Managing Director and Chief Executive Officer of Nilayo Sports Management Limited, Mrs. Yetunde Olopade, has been honoured with the prestigious Newstap/SWAN 5-Star Award, in recognition of her outstanding contributio',
  '<p class="intro">
        The Managing Director and Chief Executive Officer of Nilayo Sports Management Limited, Mrs. Yetunde Olopade, has been honoured with the prestigious Newstap/SWAN 5-Star Award, in recognition of her outstanding contributions to sports marketing and sports event management in Nigeria.
      </p>

      <p>
        Notably, Mrs. Olopade is the only female recipient among this year’s awardees, further underscoring her trailblazing role in a sector traditionally dominated by men.
      </p>

      <p>
        Mrs. Olopade’s decoration comes as a reflection of the remarkable transformation she has brought to the sports landscape through her leadership at Nilayo Sports Management Limited, widely regarded today as the leading sports management company in Africa.
      </p>

      <p>
        Under her stewardship, the company has successfully organized and managed some of Nigeria’s most prominent road races and sporting events. These include the globally acclaimed gold labelled Access Bank Lagos City Marathon, the fast-growing Premium Trust Bank Abuja City Half Marathon, and the newly introduced Yenagoa City International 10KM Race, among several other elite road races and sporting initiatives across the nation.
      </p>

      <p>
        Through innovation, professionalism, and strategic partnerships, Mrs. Olopade has helped elevate Nigeria’s profile in international road running while simultaneously promoting sports tourism, youth participation, and economic activity within host cities.
      </p>

      <p>
        Industry observers note that the Award does not come as a surprise, given the consistent excellence, organizational capacity, and global recognition that Nilayo Sports Management Limited has achieved under her leadership.
      </p>

      <p>
        The Newstap/SWAN 5-Star Awards celebrate individuals and organizations that have made significant impact in the development and promotion of sports in Nigeria. Other awardees in the category are the Governor of Bayelsa State Duoye Diri, DG/CEO National Institute for Sports Comrade Phillip Shaibu and Proprietor of Yacateco Boxing Promotions Omolei Imadu.
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2026/03/WhatsApp-Image-2026-03-09-at-09.47.07.jpeg',
  '2026-03-09',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'chief-yetunde-olopade-md-ceo-nilayo-sports-management-limited-congratulates-lagos-state-on-the-historic-e1-lagos-grand-prix',
  'Chief Yetunde Olopade, MD/CEO, Nilayo Sports Management Limited Congratulates Lagos State on the Historic E1 Lagos Grand Prix',
  'Nilayo Sports Management Limited, Africa’s leading sports management company, congratulates the Lagos State Government and His Excellency, Governor Babajide Olusola Sanwo-Olu, on the successful hosting of the E1 Lagos Gr',
  '<p class="intro">
        Nilayo Sports Management Limited, Africa’s leading sports management company, congratulates the Lagos State Government and His Excellency, Governor Babajide Olusola Sanwo-Olu, on the successful hosting of the E1 Lagos Grand Prix the first-ever E1 electric powerboat race in Africa.
      </p>

      <p>
        This remarkable achievement further cements Lagos’ position as a global sports tourism destination and a beacon of innovation on the continent. Under Governor Sanwo-Olu’s visionary leadership, Lagos continues to set the pace in delivering world-class sporting events that drive tourism, sustainability, and youth engagement.
      </p>

      <p>
        As organizers of the Access Bank Lagos City Marathon, we at Nilayo Sports Management Limited deeply appreciate the continuous support of the Lagos State Government towards our event and the wider sporting ecosystem. This partnership has been instrumental in positioning Lagos as the sporting heartbeat of Africa.
      </p>

      <p>
        We celebrate this new milestone the E1 Lagos Grand Prix as another testament to the State’s excellence and unwavering commitment to sports development.
      </p>

      <p>
        Congratulations once again to His Excellency, Governor Babajide Sanwo-Olu, and the great people of Lagos State.
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2025/10/IMG_2701.jpg',
  '2025-10-13',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'damilola-pedro-bags-award',
  'Damilola Pedro Bags FIFA/CIES Award',
  'At Nilayo Sports Management Limited, we aim to promote sporting activities from the grassroots level to the international level in Africa and we know that one of the ways to achieve this is by building the capacity of yo',
  '<p class="intro">
        At Nilayo Sports Management Limited, we aim to promote sporting activities from the grassroots level to the international level in Africa and we know that one of the ways to achieve this is by building the capacity of young people to ensure that we continue to maintain excellence.
      </p>

      <p>
        We congratulate our former Brand and Communications Manager, Damilola Pedro, who was recently awarded the Prize of the FIFA/CIES International University Network 2020 by the the Fédération Internationale de Football Association (FIFA) and the International Centre for Sports Studies (CIES).
      </p>

      <p>
        Damilola received this Prize at the FIFA Headquarters in Zurich, Switzerland because of her brilliant dissertation on Esports League System in Africa. Damilola wrote the dissertation in 2018 while she was still at Nilayo Sports and in 2020, she led the team to organise Esports Championship Eseries.
      </p>

      <p>
        Today, Damilola is currently redefining Esports in Africa through her work at Gamr, West Africa&#x27;s biggest Esports platform.
      </p>

      <p>
        Damilola dedicated the Award to Mr Bukola Olopade, our visionary Chief Executive Officer who inspired her to turn her dream into reality.
      </p>

      <p>
        Congratulations Damilola Pedro!
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2022/06/c1bf87f8-0d29-47f9-a8be-1ce50a60d93e.jpg',
  '2022-06-16',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'delta-2022-theme-song-challenge',
  'DELTA 2022 THEME SONG CHALLENGE',
  'Here is an opportunity to grab 500k in the Delta 2022 Theme song Challenge.',
  '<p class="intro">
        Here is an opportunity to grab 500k in the Delta 2022 Theme song Challenge.
      </p>

      <p>
        1. Download the Festival&#x27;s theme song instrumental on &lt;a href=&quot;http://www.delta2022.com/&quot; target=&quot;_blank&quot; rel=&quot;noopener&quot; data-saferedirecturl=&quot;https://www.google.com/url?q=http://www.delta2022.com&amp;source=gmail&amp;ust=1659177044376000&amp;usg=AOvVaw2p1AXTIR-PfKVwu2HyJwsQ&quot;&gt;www.delta2022.com&lt;/a&gt; 2. Record &amp; Post your verse with the lyrics of your verse and use #delta2022themesongchallenge. Tag @nsfdelta2022 and @premiumtrustbank in your post. Make sure you are following @nsfdelta2022 and @premiumtrustbank 3. @nsfdelta2022 will repost your post, tag your friends to like and comment on the @nsfdelta2022 IG page. The post with the highest number of likes &amp; comments wins the challenge. 4. The winner of the challenge wins the sum of N500,000. &lt;div&gt;The 10 runners-up will get a Premium Trust Bank account with N10,000 deposited.&lt;/div&gt; &lt;div&gt;&lt;/div&gt; &lt;div&gt;&lt;b&gt;Entry closes: 19th August, 2022.&lt;/b&gt;&lt;/div&gt;
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2022/07/WhatsApp-Image-2022-07-27-at-12.08.52-PM.jpeg',
  '2022-07-29',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'enugu-nilayo-seal-deal-on-marathon-sports-festival',
  'Enugu, Nilayo seal deal on marathon, sports festival',
  'Governor &lt;a href=&quot;https://www.instagram.com/pnmbah/?hl=en&quot;&gt;Peter Ndubuisi Mbahi&lt;/a&gt;-led Enugu State government has handed the two biggest sports fiestas in the state in the next two years to the man',
  '<p class="intro">
        Governor &lt;a href=&quot;https://www.instagram.com/pnmbah/?hl=en&quot;&gt;Peter Ndubuisi Mbahi&lt;/a&gt;-led Enugu State government has handed the two biggest sports fiestas in the state in the next two years to the management of the Nigeria’s foremost sports management company, Nilayo Sports Management Limited.
      </p>

      <p>
        Governor Mbah, a few days ago, handed over the Consultancy of the two events, the 2025 Enugu City International Marathon and 2026 National Sports Festival Hosting Bid to Nilayo Sports Limited.
      </p>

      <p>
        In his remarks, Enugu State Commissioner for Youth and Sports, Barrister Ikechukwu Ekweremmadu, expressed his confidence in Nilayo Sports to ensure Enugu City International Marathon becomes a World Athletics accredited marathon.
      </p>

      <p>
        While receiving the authorizing letter, the Managing Director of Nilayo Sports Limited, Chief Bukola Olopade, expressed optimism that his company would deliver a World Class Enugu City International Marathon in May 2025.
      </p>

      <p>
        The Enugu State Governor in his response, noted that, the state government will be most delighted if Nilayo Sports can ensure a World Athletics standard marathon in Enugu, with all the trappings of the Boston Marathon which is one of the best in the world in terms of spectatorship and presence of world class international runners competing on the streets of Enugu. &lt;div class=&quot;ad-container margin-top margin-bottom &quot;&gt; &lt;div class=&quot;ad-container-inner&quot;&gt; &lt;div class=&quot;AV642434b936aab9b0110cd985&quot;&gt; &lt;div id=&quot;aniBox&quot;&gt; &lt;div id=&quot;aniplayer_AV642434b936aab9b0110cd985-1718367399344&quot;&gt; &lt;div id=&quot;aniplayer_AV642434b936aab9b0110cd985-1718367399344Wrapper&quot; class=&quot;avp-floating-container avp-move-right-enter-done&quot; tabindex=&quot;0&quot;&gt;
      </p>

      <p>
        The Enugu State Governor through the State’s Commissioner of Youth and Sports also presented a second letter authorizing Nilayo Sports as Consultant to the Enugu State government on the 23rd National Sports Festival hosting bid.
      </p>

      <p>
        &lt;strong&gt;SOURCE:&lt;a href=&quot;https://thenationonlineng.net/nilayo-sports-ceo-olopade-eft-with-governor-mbah/&quot;&gt; THE NATION NEWSPAPER&lt;/a&gt;&lt;/strong&gt; &lt;div class=&quot;ad-container &quot;&gt;&lt;/div&gt; &lt;/div&gt; &lt;/div&gt; &lt;/div&gt; &lt;/div&gt; &lt;/div&gt; &lt;/div&gt;
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2024/06/WhatsApp-Image-2024-06-14-at-13.29.12.jpeg',
  '2024-06-14',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'lotus-bank-abeokuta-10km-race-nilayo-sports-febbs-premium-water-sign-partnership-deal',
  'Lotus Bank Abeokuta 10km Race: Nilayo Sports, Febbs Premium Water Sign Partnership Deal',
  'Organiser of Lotus Bank Abeokuta 10km Race, Nilayo Sports Management Limited (NISML) and Febbs Water has signed a partnership deal that sees the premium drinking water becoming official dehydration water of the marathon ',
  '<p class="intro">
        Organiser of Lotus Bank Abeokuta 10km Race, Nilayo Sports Management Limited (NISML) and Febbs Water has signed a partnership deal that sees the premium drinking water becoming official dehydration water of the marathon race which is scheduled to hold in the ancient city of Abeokuta, Ogun State, southwest Nigeria in September this year.
      </p>

      <p>
        The Chief Executive Office of Febbs, Olasegun Dada said the company sees the Lotus Bank Abeokuta 10km Race as a promising and good initiative that will have positive impacts of human development of the Nigerian youth, adding that the management of Febbs is glad to be on board with Nilayo Sports in the organisation of the marathon event.
      </p>

      <p>
        The Brand Strategic Manager of the company, Oluwatoyin Kayode while presenting a package to explain reasons behind Febbs Premium Drinking Water supporting the organisation of the race and what they will offer in this year’s event. &lt;div class=&quot;efzqxwh-container efzqxwh-type-custom_code &quot; data-adid=&quot;967073&quot; data-type=&quot;custom_code&quot;&gt;
      </p>

      <p>
        According to her, Febbs Premium Drinking Water will provide more hydration points during the race, while at each hydration centre, there will be volunteers to cheer the runners.
      </p>

      <p>
        Kayode reiterated that Febbs Water is coming on board for a period of renewable two years deal to add value to the organisation of the race, with the readiness to give the runners high quality experience.
      </p>

      <p>
        She assured that the spectators, most expecially the locals who will troop out in their thousands to be part of the event, will not be left out in the plans the company have for the smooth organisation of the race.
      </p>

      <p>
        Chief Bukola Olopade, CEO of Nilayo Sports, while commending the coming up board of Febbs Premium Drinking Water, he said he was impressed with the presentation of Febbs water highlighting how the company will partner Nilayo Sports for the success th the Abeokuta race.
      </p>

      <p>
        “This is an organization having an humane strategic to get the runners dehydrated during the race.”
      </p>

      <p>
        He said that Nilayo Sports is in the business of organizing marathon in the Nigerian sporting space to grow the country’s economy.
      </p>

      <p>
        “We also ensure that our partners use the partnership to grow their business, and of course, the Lotus Bank Abeokuta 10km Race is being organized to celebrate the birthday of Alake of Egbaland.
      </p>

      <p>
        “This year, we are inviting officials of World Athletics to witness this event, even as we are putting lots into the race to get Bronze Label Status in the next edition.
      </p>

      <p>
        “The race promises to be an exciting event and we are looking forward to seeing a perfect dehydrating system in this year’s event,” Olopade said.
      </p>

      <p>
        &lt;/div&gt;
      </p>

      <p>
        SOURCE:&lt;a href=&quot;https://independent.ng/lotus-bank-abeokuta-10km-race-nilayo-sports-febbs-premium-water-sign-partnership-deal/&quot;&gt; INDEPENDENT&lt;/a&gt;
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2024/06/Febbs-1010-1024x683-1.png',
  '2024-06-14',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'nilayo-partners-fmysd-to-deliver-cross-country-race-in-jos',
  'Nilayo Partners FMY&SD to Deliver Cross Country Race in Jos',
  'Nilayo Sports Management Limited is partnering the Federal Ministry of Youth and Sports Development (FMY&amp;SD) to deliver the first Cross Country event in Nigeria in over five decades.',
  '<p class="intro">
        Nilayo Sports Management Limited is partnering the Federal Ministry of Youth and Sports Development (FMY&amp;SD) to deliver the first Cross Country event in Nigeria in over five decades.
      </p>

      <p>
        The 10km race is scheduled to hold February 18 at the Rhino Golf Course in Jos, the capital of Plateau State.
      </p>

      <p>
        Nilayo Sports Management Limited is the marketing company that has delivered the first gold label marathon race in West Africa and have decided to co-sponsor the Cross Country event.
      </p>

      <p>
        The race is one of the plans the Sports Ministry, through the Sports Minister, Chief Sunday Dare has embarked on to develop long distance running in Nigeria.
      </p>

      <p>
        Project Consultant/Coordinator, Tony Osheku is delighted with the coming on board of Nilayo Sports Management Limited.
      </p>

      <p>
        “There cannot be a better sports management company to partner the FMYSD in this project than the company that has delivered the biggest marathon in Nigeria’s history and one of only two gold label 42 195km road races in Africa in 2023,” said Osheku, a former Nigeria 1500m champion.
      </p>

      <p>
        Osheku believes the road to producing long distance runners capable of achieving world class performances which will ultimately translate to podium appearances in international competitions and games in the not too distance future will be clearer with the institutionalisation of cross country running among Nigeria’s middle and long distance runners.
      </p>

      <p>
        “It is a known fact that training for and participating in cross country races help distance runners to be better because of the very difficult terrain they will have to navigate before reaching the finish line,” added the man who trained Falilat Ogunkoya to two Olympic medals and two African records; four straight World Championships finals in the 400m and the number one in the quarter mile in 1998.
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2023/02/PHOTO-2022-12-25-12-52-40.jpg',
  '2023-02-10',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'nilayo-sports-management-md-chief-yetunde-olopade-named-among-2025-top-50-most-influential-african-women-in-sports',
  'Nilayo Sports Management MD, Chief Yetunde Olopade Named Among 2025 Top 50 Most Influential African Women in Sports',
  '&lt;p data-start=&quot;195&quot; data-end=&quot;437&quot;&gt;Nilayo Sports Management Limited (NSML) is proud to announce that our Managing Director, &lt;strong data-start=&quot;284&quot; data-end=&quot;309&quot;&gt;Chie',
  '<p class="intro">
        &lt;p data-start=&quot;195&quot; data-end=&quot;437&quot;&gt;Nilayo Sports Management Limited (NSML) is proud to announce that our Managing Director, &lt;strong data-start=&quot;284&quot; data-end=&quot;309&quot;&gt;Chief Yetunde Olopade&lt;/strong&gt;, has been recognized as one of the &lt;strong data-start=&quot;345&quot; data-end=&quot;401&quot;&gt;2025 Top 50 Most Influential African Women in Sports&lt;/strong&gt; at an award ceremony held in Kenya.&lt;/p&gt; &lt;p data-start=&quot;439&quot; data-end=&quot;846&quot;&gt;The prestigious award celebrates women across the continent who are shaping the future of sports through leadership, innovation, advocacy, and impact. Chief Olopade’s recognition underscores her exceptional contributions to sports development in Nigeria and Africa, particularly in advancing large-scale sporting events, fostering youth participation, and driving strategic partnerships across the industry.&lt;/p&gt; &lt;p data-start=&quot;848&quot; data-end=&quot;1227&quot;&gt;With over two decade of experience in sports and events management, Chief Olopade has played pivotal roles in elevating the profile of major events including the &lt;strong data-start=&quot;997&quot; data-end=&quot;1032&quot;&gt;Access Bank Lagos City Marathon&lt;/strong&gt;, &lt;strong&gt;PremiumTrust Bank Abuja City International Half Marathon, Remo Ultra Race&lt;/strong&gt;, &lt;strong data-start=&quot;1055&quot; data-end=&quot;1077&quot;&gt;Abeokuta 10KM Race&lt;/strong&gt;, and other NSML-driven initiatives. Under her leadership, NSML continues to champion global standards in event management and sponsorship activation.&lt;/p&gt; &lt;p data-start=&quot;1229&quot; data-end=&quot;1423&quot;&gt;Speaking on the recognition, Chief Olopade expressed gratitude to the organizers and reaffirmed her commitment to using sports as a tool for national development and social impact across Africa.&lt;/p&gt;
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2025/11/WhatsApp-Image-2025-11-21-at-13.15.09.jpeg',
  '2025-11-21',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'nilayo-to-sign-multiple-multi-billion-naira-deals-for-abuja-city-international-marathon',
  'Nilayo to sign multiple multi-billion naira deals for Abuja City International Marathon',
  'The management of Nigeria&#x27;s premium Marathon races organisers, Nilayo Sports Management, led by its Managing Director, Chief Bukola Olopade, will on Thursday, February 9, 2023 at 2pm sign a multiple multi-billion na',
  '<p class="intro">
        The management of Nigeria&#x27;s premium Marathon races organisers, Nilayo Sports Management, led by its Managing Director, Chief Bukola Olopade, will on Thursday, February 9, 2023 at 2pm sign a multiple multi-billion naira contract deals that would see the birth of the Abuja City International Marathon.
      </p>

      <p>
        In the words of the Nigeria&#x27;s Marathon Generaliso,the Abuja City International Marathon, which is a full marathon of 42 kilometres, has been approved by the Athletics Federation of Nigeria, the Association of International Marathons and Distance Races (AIMS), the Federal Capital Territory, and the Ministry of Sports, Youths and Social development.
      </p>

      <p>
        Nilayo Sports Management, the biggest organisers of Marathon races in Africa and indeed in Nigeria, would sign the multi-billion naira contract to organise the Abuja City International Marathon on October 7, 2023, a date that had already been announced by the organisers.
      </p>

      <p>
        The Nilayo Sports Management Limited boss noted that, the target is to have a second Gold-Label status full marathon race in Nigeria especially in the beautiful city of Abuja, Nigeria&#x27;s federal capital city.
      </p>

      <p>
        The main sponsor of the race would also be unveiled in an elaborate ceremony, where the prize money, the class of runners, the route and other world class incentives will be announced for the Abuja City International Marathon.
      </p>

      <p>
        &quot;It would be most delightful to have one full marathon each in the Nigeria&#x27;s financial capital and the nations capital. By the time, the Abuja City International Marathon attains the Gold Label Marathon status, Nigeria would have become one of the very few countries in the world, with two Gold-Label races, in one country. This is a rare attribute in road running in the world. This would tremendously boost the status of Nigeria, and that is what we have been talking about.&quot;
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2023/02/20230203131759_MIK_0151-2-scaled.jpg',
  '2023-02-08',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'ogun-set-to-host-nsf-2024-olopade-adebajo-others-to-lead-16-man-committee',
  'Ogun set to host NSF 2024; Olopade, Adebajo, others to lead 16-man committee',
  'Ogun State government, on Tuesday, announced its readiness to hold the 2024 National Sports Festival (NSF) in fulfilment of an earlier promise made by the Governor, Prince Dapo Abiodun, as it unveiled leading professiona',
  '<p class="intro">
        Ogun State government, on Tuesday, announced its readiness to hold the 2024 National Sports Festival (NSF) in fulfilment of an earlier promise made by the Governor, Prince Dapo Abiodun, as it unveiled leading professionals in Nigeria&#x27;s sports sector as Local 9rganizing Committee (LOC) members.
      </p>

      <p>
        It would be recalled that the state government, earlier in May, had signed a Memorandum of Understanding (MOU) with the Federal Government, through the Federal Ministry of Sports and Youth Development.
      </p>

      <p>
        During that occasion, Governor Abiodun had told the Federal Government delegation led by the then Minister of Sports, Mr Sunday Dare, that Ogun State, being a state noted for recording many firsts in all spheres of human endeavour, was ready to host a world-class festival in 2024, asserting that the State had capable hands and sponsors ready to support the bid.
      </p>

      <p>
        To that end, the former minister, while expressing the FG&#x27;s acceptance of the proposal noted that the choice of Ogun State as the next host of the NSF was due to its culture of sports development, availability of sporting infrastructure and potential to host a befitting festival that would linger for a long time.
      </p>

      <p>
        Unveiling the 16-man local organizing committee in Abeokuta, the state capital on Tuesday, Secretary to the State Government (SSG), Mr. Tokunbo Talabi, in a statement listed former Commissioner for Youths and Sports in the state, Hon. Bukola Olapade as the chairman of the LOC alongside Mr Tilewa Adebayo, a sports enthusiast, as co-chairman.
      </p>

      <p>
        Olopade, the progenitor of the top-rated Access Bank-sponsored Lagos City Marathon, Abeokuta 10km Marathon race, and the Remo ultra-race, remains topnotch in the world of sports in Nigeria.
      </p>

      <p>
        Another member of the Committee, Chief (Mrs.) Falilat Ogunkoya-Omotayo (MON) who is regarded as the Queen of tracks is a 1998 World Cup of Athletics Champion, 400 metres grand prix champion, 200 metres world champion, two-time Olympic medalist and the first female Nigerian to win an individual Olympic medal and hold the African record in 400 metres till date.
      </p>

      <p>
        Others include the Secretary-general of the local organising committee (LOC) for the NSF Edo 2020, Dr Emmanuel Igbinosa, one of Nigeria&#x27;s notable sports administrators, ex-chairman of the Lagos State Sports Commission (LSSC); working committee of the African Table Tennis Federation (ATTF) and Chairman of the Youth Committee of African Table Tennis Federation, Dr Kweku Tandoh, as well as the renowned sports psychologist and head of the Department of Sports and Exercise Medical Sciences at University of Health and Allied Sciences, Ho, Ghana, Professor Oluwaseun Olanrewaju Omotayo,
      </p>

      <p>
        Also in the Committee are the Vice Chairman of Ijebu Ode Local Government, Hon Dare Alebiosu; Mr. Kunle Solaja, Olusegun Oyende, Mr Olatunji Onatolu, Mrs. Modele Sharafa Yussuf, Demola Are, Abiodun Jubril Elegbede, Ola Opedimeji Adisa, the former Commissioner for Youth &amp; Sports and the Permanent Secretary, Ministry of Youth &amp; Sports.
      </p>

      <p>
        The statement said that the LOC has been mandated to set up subcommittees, made up of various stakeholders to ensure a hitch-free exercise as well as deliver a sports festival that is second to none in the country.
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2023/07/345d0e61-c666-4cf0-8f75-da572d590436.jpg',
  '2023-07-11',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'premium-trust-bank-announced-as-official-banking-partner-of-athletics-federation-of-nigeria',
  'PREMIUM TRUST BANK ANNOUNCED AS OFFICIAL BANKING PARTNER OF ATHLETICS FEDERATION OF NIGERIA',
  '&lt;div class=&quot;page&quot; title=&quot;Page 1&quot;&gt; &lt;div class=&quot;layoutArea&quot;&gt; &lt;div class=&quot;column&quot;&gt;',
  '<p class="intro">
        &lt;div class=&quot;page&quot; title=&quot;Page 1&quot;&gt; &lt;div class=&quot;layoutArea&quot;&gt; &lt;div class=&quot;column&quot;&gt;
      </p>

      <p>
        &lt;strong&gt;29TH July 2022&lt;/strong&gt; – Nilayo Sports Management Limited, marketing consultants of the Athletics Federation of Nigeria has announced Premium Trust Bank as the official banking partner of the federation.
      </p>

      <p>
        Mr Ebidowei Oweifie, the Chief Operating Officer of Nilayo Sports Management Limited, said “We are pleased to announce Premium Trust Bank as the official banking partner of the Athletics Federation of Nigeria.
      </p>

      <p>
        “Premium Trust has demonstrated dedication to the growth and development of Athletics in the country. As a forward-thinking and fast-growing brand, we are very confident that this is a collaboration that will transform athletics in Nigeria.”
      </p>

      <p>
        Athletics Federation of Nigeria President, Hon. Tonobok Okowa added that “Athletics in Nigeria is evolving, we are churning out world-class athletes who are determined to push beyond the boundaries and set new standards. Having a bank like Premium Trust on board as the banking partner of AFN displays the commitment of the bank to Nigerian athletes, this partnership will also be very beneficial to all athletes as it is a way of securing their financial future.”
      </p>

      <p>
        Mr Oweifie added that the deal was concluded five weeks ago, however, Nilayo hesitated to make the announcement to ensure that there is enough concentration on the Athletics events happening in Oregon, Birmingham, and Cali as they are landmark events for Nigerian athletes.
      </p>

      <p>
        &lt;strong&gt;ATHLETICS FEDERATION OF NIGERIA&lt;/strong&gt; - The Athletics Federation of Nigeria (AFN) is the governing body for the sport of Athletics in Nigeria, an affiliated member of the Confederation of African Athletics (CAA) and World Athletics.
      </p>

      <p>
        &lt;strong&gt;PREMIUM TRUST BANK&lt;/strong&gt; – Premium Trust Bank is on a mission to provide solutions to peculiar financial challenges of customers through innovation and speed of execution, improving lives, communities, and businesses. This will be achieved through premium service delivery, human capital, and customer experience.
      </p>

      <p>
        &lt;/div&gt; &lt;/div&gt; &lt;/div&gt; &lt;div class=&quot;page&quot; title=&quot;Page 2&quot;&gt; &lt;div class=&quot;layoutArea&quot;&gt; &lt;div class=&quot;column&quot;&gt;
      </p>

      <p>
        &lt;strong&gt;NILAYO SPORTS MANAGEMENT LIMITED&lt;/strong&gt; - NILAYO Sports Management Ltd is a sports management and Sponsorship consulting company, working with some of the biggest names in the world of sports like WORLD ATHLETICS, CAA, AFN and NNL. Our aim is to promote sports from grass root level to international in Africa by bridging the gap between the sports industry and the Private sector, hereby forging alliances through sponsorship and value creation.
      </p>

      <p>
        &lt;/div&gt; &lt;/div&gt; &lt;/div&gt;
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2022/08/Screenshot-2022-08-01-at-09.25.01.jpg',
  '2022-08-01',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'premium-trust-bank-announced-as-official-banking-partner-of-national-sports-festival-delta-2022',
  'PREMIUM TRUST BANK ANNOUNCED AS OFFICIAL BANKING PARTNER OF NATIONAL SPORTS FESTIVAL, DELTA 2022',
  '&lt;div class=&quot;page&quot; title=&quot;Page 1&quot;&gt; &lt;div class=&quot;layoutArea&quot;&gt; &lt;div class=&quot;column&quot;&gt;',
  '<p class="intro">
        &lt;div class=&quot;page&quot; title=&quot;Page 1&quot;&gt; &lt;div class=&quot;layoutArea&quot;&gt; &lt;div class=&quot;column&quot;&gt;
      </p>

      <p>
        &lt;strong&gt;21st June 2022&lt;/strong&gt; - Nilayo Sports Management Limited, marketing consultants for National Sports Festival, Delta 2022 has announced Premium Trust Bank as the Official Banking Partner of the Festival.
      </p>

      <p>
        Mr Bukola Olopade, the Chief Executive Officer of Nilayo Sports while making the announcement said “This is a partnership of two premium brands. Premium Trust Bank, a bank committed to providing tailor-made solutions for customers and the National Sports Festival, Delta 2022, which is going to be the best in History.
      </p>

      <p>
        “This shows that Premium Trust Bank is committed to providing platforms for athletes to help them grow in their chosen field and impacting communities across the country; our young athletes will be motivated to continue their pursuit of excellence both in the country and on the international stage.”
      </p>

      <p>
        The 21st National Sports Festival is scheduled for November 2nd to 15th. Delta state will host athletes from across the country who will participate in 33 sports including eight Para Sports.
      </p>

      <p>
        &lt;strong&gt;NATIONAL SPORTS FESTIVAL&lt;/strong&gt; - The Nigerian National Sports Festival is a biennial multi-sport event organized by the Federal Government of Nigeria through the National Sports Commission for athletes from the 36 States of Nigeria. National Sports Festival started in 1973 as a unifying tool for the promotion of cross-cultural affiliation in Nigeria after the Civil War. The first National Sports Festival was held at the National Stadium, Surulere, Lagos. Delta State has been selected to host the 21st National Sports Festival tagged Delta 2022. Delta state is known for its proactive sports atmosphere. Delta won the highest medals in the 20th edition of the National Sports Festival held in Edo State and the state is ready to deliver the best National Sports Festival since inception.
      </p>

      <p>
        &lt;/div&gt; &lt;/div&gt; &lt;/div&gt; &lt;div class=&quot;page&quot; title=&quot;Page 2&quot;&gt; &lt;div class=&quot;layoutArea&quot;&gt; &lt;div class=&quot;column&quot;&gt;
      </p>

      <p>
        &lt;strong&gt;PREMIUM TRUST BANK&lt;/strong&gt; – Premium Trust Bank seeks to be the Bank of First Preference providing tailor-made solutions to peculiar financial challenges of customers through innovation and speed of execution, improving lives, communities, and businesses. This will be achieved by leveraging Technology, Premium Service Delivery, Human Capital and Customer Experience.
      </p>

      <p>
        &lt;/div&gt; &lt;/div&gt; &lt;div class=&quot;layoutArea&quot;&gt; &lt;div class=&quot;column&quot;&gt;
      </p>

      <p>
        &lt;strong&gt;NILAYO SPORTS MANAGEMENT LIMITED&lt;/strong&gt; - NILAYO Sports Management Ltd is a sports management and Sponsorship consulting company, working with some of the biggest names in the world of sports like WORLD ATHLETICS, CAA, AFN and NNL. Our aim is to promote sports from grass root level to international in Africa by bridging the gap between the sports industry and the Private sector, hereby forging alliances through sponsorship and value creation.
      </p>

      <p>
        &lt;/div&gt; &lt;/div&gt; &lt;/div&gt;
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2022/06/Delta-2022-logo-.jpg',
  '2022-06-21',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (
  'unveiling-of-the-21st-national-sports-festival-delta-2022-logo-mascot',
  'UNVEILING OF THE 21ST NATIONAL SPORTS FESTIVAL DELTA 2022 LOGO & MASCOT',
  '&lt;strong&gt;22nd July 2022-&lt;/strong&gt; Host Governor, Sen. Ifeanyi Okowa led in attendance other top sporting personalities including the Minister of Youth and Sports Development, Sunday Dare, NOC President, Eng. H',
  '<p class="intro">
        &lt;strong&gt;22nd July 2022-&lt;/strong&gt; Host Governor, Sen. Ifeanyi Okowa led in attendance other top sporting personalities including the Minister of Youth and Sports Development, Sunday Dare, NOC President, Eng. Habul Gumel, Speaker of the Delta House of Assembly, Rt Hon. Sherriff Oborevwori, former Delta State Commissioner Chief Solomon Ogba as well as members of the state’s executive and other top government officials. &lt;p style=&quot;text-align: left;&quot;&gt;&lt;strong&gt;NATIONAL SPORTS FESTIVAL&lt;/strong&gt;- In his opening remarks, the Chairman of the Local Organizing Committee (LOC) Chief Patrick Ukah who also doubles as the Secretary to the State Government, noted that unveiling of both the mascot and logo of Delta 2022, shows the readiness of the state towards hosting a successful event in November.&lt;/p&gt; Tagged ‘Uniting Nigeria, inspiring a new generation’, Ukah said Delta 2022 would go down in history as the best organised and economic viable with the array of sponsors that have already aligned with the event.
      </p>

      <p>
        &lt;strong&gt;MINISTRY OF YOUTH &amp; SPORTS-&lt;/strong&gt; Sports Minister Dare eulogized Governor Okowa for his total support for sport over the years, adding ‘Nigeria is finished should Delta State be taken out of her sporting equation.’
      </p>

      <p>
        Hon Sunday Dare said Delta has done so well for Nigerian sport over the years and hopefully the state would be ready to host the next edition of what he called Nigeria’s Mini- Olympics.
      </p>

      <p>
        &lt;strong&gt;PREMIUM TRUST BANK-&lt;/strong&gt; Speaking also, The Managing Director of PremiumTrust Bank, Mr Emmanuel Emefienim reiterated the Bank’s commitment to growth, impacting lives and communities.
      </p>

      <p>
        He also highlighted that as the official Banker of the National Sports Festival, Delta 2022, the bank aims to contribute to the development of sports in Nigeria and foster unity in our diversity as a nation.
      </p>

      <p>
        “In partnership with the National Sports Festival, Premium Trust Bank is committed to providing the needed platform to help athletes grow and excel in their chosen fields consistent with our tag line – “Together for Growth.”
      </p>

      <p>
        Mr Emmanuel, therefore, encouraged the youths to take advantage of the platform provided by this Sports Festival in their pursuit of excellence both within the country and on the international stage in sports.
      </p>

      <p>
        &lt;strong&gt;DELTA STATE-&lt;/strong&gt; Sen. Ifeanyi Okowa, said the state is happy to bring home the sporting extravaganza it has dominated over the years.
      </p>

      <p>
        ” Of course, we want competitors but we are sure of winning even here at home. We are going to put in our best, and at the end, we would show that truly, ‘Delta no dey Carry Last.
      </p>

      <!-- Tags (WP: the_tags()) -->
      ',
  'https://nilayosports.com/wp-content/uploads/2022/07/IMG_6612.jpeg',
  '2022-07-22',
  1
)
ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), body=VALUES(body), cover_image=VALUES(cover_image), published_at=VALUES(published_at);

