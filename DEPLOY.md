# Deploying the NSML site to cPanel (with Git)

The site is static HTML/CSS/JS plus a small PHP/MySQL CMS. Requirements on the
host: **PHP 8.0+** and **MySQL/MariaDB** (standard on any cPanel plan).

Repo: `https://github.com/TheDanielOkpara/nsml-website.git` (public)

---

## Step 0 — Check what's currently on the domain (do this first)

1. cPanel → **File Manager** → open **public_html**.
2. Note what's there. If it's an old site you want to keep a copy of:
   - Select all → **Compress** → `Zip` → download the zip as a backup.
3. We will be putting the new site in `public_html`, so it must be empty
   before the Git clone. Move the old files into a `public_html/_old/` folder
   (or delete them once you have the backup zip).

---

## Step 1 — Create the database

1. cPanel → **MySQL® Databases**.
2. **Create New Database** — e.g. `nsml` (cPanel prefixes it, so the real name
   becomes something like `youracct_nsml`). Note the full name.
3. **Add New User** — pick a username and a strong password. Note both.
4. **Add User to Database** → select the user and the database → grant
   **ALL PRIVILEGES**.

## Step 2 — Import the schema and content

1. cPanel → **phpMyAdmin** → select your `youracct_nsml` database on the left.
2. **Import** tab → upload `cms/schema.sql` → **Go**. (Creates the tables.)
3. Import again with `cms/seed-data.sql` (team members + the 10 properties).
4. Import again with `cms/seed-blog.sql` (the 16 news articles).

> You can get those three .sql files from the repo (Step 3 puts them on the
> server), or download them from GitHub first and upload them in phpMyAdmin.

## Step 3 — Pull the code in with Git

1. cPanel → **Git™ Version Control** → **Create**.
2. **Clone URL:** `https://github.com/TheDanielOkpara/nsml-website.git`
3. **Repository Path:** `public_html` (must be empty — see Step 0).
4. **Create**. cPanel clones the whole site into `public_html`.

To pull future updates: Git Version Control → **Manage** → **Update from Remote**.

## Step 4 — Add your database credentials (kept out of the public repo)

1. File Manager → `public_html/cms/includes/`.
2. Copy `config.local.example.php` → `config.local.php` (right-click → Copy).
3. Edit `config.local.php` and fill in the real `DB_NAME`, `DB_USER`,
   `DB_PASS` from Step 1, and your contact email addresses.
4. Save. (`config.local.php` is gitignored, so future `git pull`s won't
   overwrite it.)

## Step 5 — Create your admin login

1. Visit `https://yourdomain.com/cms/admin/create-admin.php`.
2. Enter a username and a password (8+ characters) → **Create**.
3. **Delete `public_html/cms/admin/create-admin.php`** in File Manager
   (it has no login protection — remove it once your admin user exists).
4. Log in at `https://yourdomain.com/cms/admin/login.php`.

## Step 6 — Check it works

- `https://yourdomain.com/` — homepage
- `https://yourdomain.com/properties.php` — should list all 10 properties
- `https://yourdomain.com/news.php` — should list the 16 articles
- `https://yourdomain.com/about.php` — team section
- Submit the contact form and the newsletter form, then check
  **Admin → Messages** and **Admin → Subscribers**.

## Step 7 — Permissions for image uploads

So the admin panel can save uploaded images, make the uploads folder writable:
File Manager → `public_html/cms/uploads` → **Permissions** → `0755`
(or `0775` if your host runs PHP as a separate user).

---

## Notes

- **Contact emails:** Step 4's `CONTACT_NOTIFY_FROM` should be an address on
  your own domain (create a mailbox in cPanel → Email Accounts) so the
  notifications aren't flagged as spam.
- **Old `.html` URLs:** the news page and the individual article pages are now
  `news.php` and `article.php?slug=…`. If the old site's article URLs were
  indexed by Google, consider adding 301 redirects in `.htaccess` later.
- **Redeploys:** push to GitHub → Git Version Control → Update from Remote.
  Your `config.local.php` and uploaded images are preserved.
