============================================================
  MY TECH BLOG - Practical-20
  Dynamic Blog Website with Admin Panel
  PHP + MySQL + Bootstrap 5
============================================================

DEFAULT LOGIN:
  URL:      http://localhost/my-tech-blog/login.php
  Username: admin
  Password: admin123

LOCAL SETUP (XAMPP):
  1. Copy "my-tech-blog" folder to: C:\xampp\htdocs\
  2. Start XAMPP: Apache + MySQL
  3. phpMyAdmin: http://localhost/phpmyadmin
  4. Create database: mytechblog
  5. Import: database.sql
  6. Open: http://localhost/my-tech-blog/

LIVE DEPLOYMENT:
  1. Update config.php with live server credentials
  2. Upload all files to public_html via FileZilla
  3. Import database.sql via cPanel phpMyAdmin

FILES:
  config.php         - Database credentials (change for live)
  index.php          - Homepage
  post.php           - Single post + comments
  login.php          - Admin login
  logout.php         - Admin logout
  header.php         - Common header
  footer.php         - Common footer
  css/style.css      - Custom styles
  admin/dashboard.php  - Admin panel
  admin/add_post.php   - Add post
  admin/edit_post.php  - Edit post
  admin/delete_post.php- Delete post
  database.sql       - Database setup
============================================================
