# PRACTICAL - 20

---

## AIM

Deploy a Dynamic Website with Database Connectivity on a Live Server using FTP Tools and Hosting Control Panel.

**Project:** *My Tech Blog* — Dynamic Blog Website with Admin Panel (PHP + MySQL)

---

## INTRODUCTION

### Dynamic Website kya hai?
Ek **Dynamic Website** wo website hoti hai jisme content database se aata hai aur user ke actions ya requests ke hisaab se change hota hai. Jaise hamare blog mein posts aur comments database mein store hain.

Static website mein HTML fixed hota hai; Dynamic mein PHP database se data fetch karke HTML generate karta hai.

### Web Hosting kya hai?
**Web Hosting** ek service hai jisme ek company aapko apne web server par space deti hai taaki aapki website internet par accessible ho. Bina hosting ke aapki website sirf local computer par chalegi.

**Types of Hosting:**
- **Shared Hosting** — Multiple websites ek server share karti hain. Sasta aur beginners ke liye.
- **VPS Hosting** — Virtual Private Server. Dedicated resources milte hain.
- **Dedicated Hosting** — Poora server sirf apke liye.

### cPanel kya hai?
**cPanel** ek web-based hosting control panel hai jo Linux servers par use hota hai. Isme aap:
- Databases create/manage kar sakte ho
- Email accounts manage kar sakte ho
- File Manager se files upload/delete kar sakte ho
- phpMyAdmin se database GUI se manage kar sakte ho
- Domain, Subdomain, SSL manage kar sakte ho

### Plesk kya hai?
**Plesk** bhi cPanel jaisa ek hosting control panel hai jo Windows aur Linux dono servers par kaam karta hai. Interface slightly alag hota hai lekin functions same hain.

### FTP kya hai?
**FTP (File Transfer Protocol)** ek network protocol hai jo files ko local computer se remote server par transfer karne ke liye use hota hai.
- **Port:** 21 (default)
- **FTPS:** FTP over SSL (secure)
- **SFTP:** SSH File Transfer Protocol (aur zyada secure)

### FileZilla kya hai?
**FileZilla** ek free, open-source FTP client software hai jo Windows, Mac, aur Linux par available hai. Isme do panels hote hain — left mein local files, right mein server files. Drag-and-drop se files upload/download ho sakti hain.

---

## REQUIREMENTS

| Item | Description |
|------|-------------|
| Domain Name | Website ka address (e.g., mytechblog.com) |
| Web Hosting Account | cPanel ya Plesk hosting |
| cPanel / Plesk Access | Database aur file management ke liye |
| FTP Client (FileZilla) | Files server par upload karne ke liye |
| PHP & MySQL Server | Hosting par enabled hona chahiye |
| Dynamic Website Files | PHP project files (.php, .css, .sql) |
| SQL Database File (.sql) | Database structure aur data ke liye |

---

## SOFTWARE REQUIRED

| Software | Purpose | Download |
|----------|---------|----------|
| XAMPP / WAMP | Local PHP+MySQL Server | apachefriends.org |
| FileZilla FTP Client | Files server par upload karna | filezilla-project.org |
| cPanel Hosting | Live server control panel | (Hosting provider deta hai) |
| VS Code / Notepad++ | Code editor | code.visualstudio.com |
| Web Browser | Testing ke liye | (Already installed) |

---

## FOLDER STRUCTURE

```
my-tech-blog/
│
├── config.php          ← Database configuration (4 values only)
├── header.php          ← Common header (navbar, HTML head)
├── footer.php          ← Common footer (Bootstrap JS)
├── index.php           ← Homepage (all posts in cards)
├── post.php            ← Single post + comments
├── login.php           ← Admin login
├── logout.php          ← Admin logout (session destroy)
├── database.sql        ← Database setup file
│
├── css/
│   └── style.css       ← Custom CSS styles
│
└── admin/
    ├── auth_check.php  ← Login guard (included in all admin pages)
    ├── dashboard.php   ← Admin panel main page (posts table)
    ├── add_post.php    ← Add new blog post
    ├── edit_post.php   ← Edit existing post
    └── delete_post.php ← Delete post
```

---

## DATABASE STRUCTURE

**Tables:** `admin`, `posts`, `comments`

```sql
-- admin table
id | username | password (bcrypt) | created_at

-- posts table
id | title | category | author | content | created_at | updated_at

-- comments table
id | post_id (FK) | name | email | comment | created_at
```

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

---

## CODE

> Saari files `my-tech-blog/` folder mein hain. Yahan key files ka code diya gaya hai.

---

### FILE 1: `config.php`
```php
<?php
// Database Configuration - 4 values change karo live server par
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // XAMPP default
define('DB_PASS', '');               // XAMPP default (empty)
define('DB_NAME', 'mytechblog');

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("DB Error: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

define('SITE_NAME', 'My Tech Blog');
define('SITE_URL',  'http://localhost/my-tech-blog');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
```

---

### FILE 2: `index.php` (Homepage - Posts Grid)
```php
<?php
$pageTitle = "Home";
require_once 'header.php';
$conn = getConnection();

// Posts + comments count fetch
$sql = "SELECT p.id, p.title, p.category, p.author, p.created_at,
               LEFT(p.content, 180) AS short_content,
               COUNT(c.id) AS comments_count
        FROM posts p
        LEFT JOIN comments c ON c.post_id = p.id
        GROUP BY p.id
        ORDER BY p.created_at DESC";

$result = $conn->query($sql);
?>
<!-- Blog cards loop -->
<?php while ($post = $result->fetch_assoc()): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card blog-card">
            <div class="card-header">
                <span class="badge-category"><?= htmlspecialchars($post['category']) ?></span>
            </div>
            <div class="card-body">
                <h5><?= htmlspecialchars($post['title']) ?></h5>
                <p class="post-meta">
                    by <?= htmlspecialchars($post['author']) ?> |
                    <?= date('d M Y', strtotime($post['created_at'])) ?> |
                    <?= $post['comments_count'] ?> Comments
                </p>
                <p><?= htmlspecialchars($post['short_content']) ?>...</p>
                <a href="post.php?id=<?= $post['id'] ?>" class="btn-read-more">Read More</a>
            </div>
        </div>
    </div>
<?php endwhile; ?>
```

---

### FILE 3: `post.php` (Single Post + Comments)
```php
<?php
require_once 'header.php';
$conn = getConnection();
$post_id = (int)($_GET['id'] ?? 0);

// Post fetch (prepared statement)
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

// Comment submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $comment = trim($_POST['comment']);
    // ... validation ...
    $stmt2 = $conn->prepare(
        "INSERT INTO comments (post_id, name, email, comment) VALUES (?,?,?,?)"
    );
    $stmt2->bind_param("isss", $post_id, $name, $email, $comment);
    $stmt2->execute();
}

// Full post + comment form display...
?>
```

---

### FILE 4: `login.php` (Admin Login)
```php
<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $conn = getConnection();
    $stmt = $conn->prepare(
        "SELECT id, username, password FROM admin WHERE username = ? LIMIT 1"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    // bcrypt verify
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username']  = $admin['username'];
        header("Location: admin/dashboard.php");
        exit;
    } else {
        $error = "Invalid username ya password!";
    }
}
?>
```

---

### FILE 5: `admin/dashboard.php` (Admin Panel)
```php
<?php
require_once 'auth_check.php';  // Login guard
$conn = getConnection();

$sql = "SELECT p.id, p.title, p.category, p.author, p.created_at,
               COUNT(c.id) AS comments_count
        FROM posts p
        LEFT JOIN comments c ON c.post_id = p.id
        GROUP BY p.id ORDER BY p.created_at DESC";
$result = $conn->query($sql);

// Table mein posts display + Edit/Delete buttons
?>
```

---

### FILE 6: `database.sql`
```sql
CREATE DATABASE IF NOT EXISTS mytechblog CHARACTER SET utf8mb4;
USE mytechblog;

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
-- Password: admin123 (bcrypt hashed)
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

CREATE TABLE posts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255) NOT NULL,
    category   VARCHAR(100) NOT NULL,
    author     VARCHAR(100) NOT NULL,
    content    TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE comments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    post_id    INT NOT NULL,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    comment    TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);
-- Sample data included in database.sql file
```

---

## LOCAL SETUP (XAMPP)

### Step 1: XAMPP Install Karo
1. [apachefriends.org](https://www.apachefriends.org) se XAMPP download karo
2. Install karo (default settings theek hain)
3. XAMPP Control Panel open karo
4. **Apache** aur **MySQL** ke Start buttons click karo
5. Dono ka status **Running** (green) hona chahiye

### Step 2: Project Copy Karo
1. `my-tech-blog` folder ko copy karo
2. `C:\xampp\htdocs\` mein paste karo
3. Final path: `C:\xampp\htdocs\my-tech-blog\`

### Step 3: Database Setup
1. Browser mein `http://localhost/phpmyadmin` open karo
2. Left sidebar mein **New** click karo
3. Database name: `mytechblog` → **Create** click karo
4. Upar **Import** tab click karo
5. **Choose File** → `my-tech-blog/database.sql` select karo
6. Scroll down → **Go / Import** click karo
7. "Import has been successfully finished" message aana chahiye

### Step 4: Website Test Karo
Browser mein open karo: `http://localhost/my-tech-blog/`

- **Homepage:** `http://localhost/my-tech-blog/index.php`
- **Admin Login:** `http://localhost/my-tech-blog/login.php`
  - Username: `admin` | Password: `admin123`
- **Admin Dashboard:** `http://localhost/my-tech-blog/admin/dashboard.php`

---

## LIVE DEPLOYMENT STEPS (Step-by-Step, Beginner Friendly)

### Step 1: Domain aur Web Hosting Account
- Hosting provider se domain + hosting plan kharido
- **Free Option:** [InfinityFree](https://infinityfree.net) — Free hosting with PHP + MySQL
  - Sign up karo → Create Account → Domain select karo (free subdomain milega)
  - cPanel access mil jayega

### Step 2: cPanel Login
- Hosting provider ka URL: `http://yourdomain.com/cpanel` ya email mein link milega
- cPanel username aur password se login karo

### Step 3: MySQL Database Create Karo
1. cPanel mein **MySQL Databases** icon click karo
2. **Create New Database** section mein naam likho: `mytechblog`
3. **Create Database** click karo
4. Neeche **MySQL Users** section mein:
   - Username: `bloguser`
   - Password: Strong password dale aur note karo
   - **Create User** click karo
5. **Add User to Database** section mein:
   - User: `bloguser`, Database: `mytechblog` select karo
   - **Add** click karo
6. Permissions mein **ALL PRIVILEGES** checkbox tick karo → **Make Changes**

> **Note:** cPanel mein actual database name hogi: `cpanelusername_mytechblog`
> Aur username hoga: `cpanelusername_bloguser`
> Ye full names config.php mein use karne hain!

### Step 4: phpMyAdmin se Database Import Karo
1. cPanel mein **phpMyAdmin** click karo
2. Left sidebar mein `cpanelusername_mytechblog` database click karo
3. Upar **Import** tab click karo
4. **Choose File** → `database.sql` select karo
5. **Go** click karo — tables create ho jayengi

### Step 5: config.php Update Karo (IMPORTANT!)
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'cpanelusername_bloguser');  // cPanel ka actual username
define('DB_PASS', 'yourDatabasePassword');      // Jo aapne Step 3 mein rakha
define('DB_NAME', 'cpanelusername_mytechblog'); // cPanel ka actual DB name
define('SITE_URL', 'http://yourdomain.com');    // Apna actual domain
```

### Step 6: FileZilla se FTP Connect Karo
1. FileZilla download aur install karo: [filezilla-project.org](https://filezilla-project.org)
2. FileZilla open karo
3. Upar **Host** field mein: `ftp.yourdomain.com` ya server IP
4. **Username:** FTP username (cPanel se milega)
5. **Password:** FTP password
6. **Port:** `21`
7. **Quickconnect** button click karo
8. Right panel mein server files dikhenge

### Step 7: Files public_html Mein Upload Karo
1. FileZilla ke right panel mein `public_html` folder double-click karo
2. Left panel mein local `my-tech-blog` folder navigate karo
3. Saari files select karo (Ctrl+A)
4. Right-click → **Upload**
5. Ya drag-and-drop karo left se right panel mein
6. **Important:** `config.php` bhi upload ho gaya hona chahiye with live credentials

### Step 8: Website Test Karo
Browser mein apna domain open karo:
- `http://yourdomain.com` — Homepage
- `http://yourdomain.com/login.php` — Admin Login
- `http://yourdomain.com/admin/dashboard.php` — Dashboard

### Step 9: Common Errors aur Solutions

| Error | Cause | Solution |
|-------|-------|---------|
| **DB Connection Error** | Wrong credentials in config.php | DB_HOST, DB_USER, DB_PASS, DB_NAME check karo. cPanel mein full names use karo |
| **500 Internal Server Error** | PHP syntax error ya permission issue | Error log check karo. File permissions 644 honi chahiye, folders 755 |
| **Blank White Page** | PHP error reporting off, fatal error | config.php ke top par `error_reporting(E_ALL); ini_set('display_errors', 1);` temporarily add karo |
| **404 Not Found** | Files public_html mein nahi hain | FileZilla se check karo files sahi folder mein hain |
| **Images/CSS nahi dikh raha** | SITE_URL galat hai | config.php mein SITE_URL apna actual domain karo |
| **Login kaam nahi kar raha** | Session ya password issue | PHP version 7.x+ use karo. cPanel mein PHP version check karo |
| **File Permission Error** | Wrong file permissions | FileZilla se right-click → File Permissions → 644 (files) / 755 (folders) |

---

## FREE HOSTING (InfinityFree) - Steps

1. [infinityfree.net](https://infinityfree.net) par jaao → **Sign Up Free**
2. Account verify karo
3. **Create Account** → Free subdomain choose karo (e.g., `mytechblog.epizy.com`)
4. cPanel access mil jayega
5. **MySQL Databases** se database create karo
6. **FTP Accounts** se FTP credentials dekho
7. FileZilla se connect karo aur `htdocs` folder mein upload karo
8. config.php update karo

> **Note:** InfinityFree par `DB_HOST` sometimes `sql200.epizy.com` jaisa hota hai, `localhost` nahi. cPanel se correct host check karo.

---

## OUTPUT (Screenshots Jo Lene Hain)

1. **XAMPP Control Panel** — Apache aur MySQL Running (green status)
2. **phpMyAdmin** — `mytechblog` database ke 3 tables (admin, posts, comments)
3. **Homepage** — `http://localhost/my-tech-blog/` — Blog posts cards grid
4. **Single Post Page** — Post content + comments section
5. **Comment Submit** — Visitor ne comment dala, success message aaya
6. **Admin Login Page** — Login form
7. **Admin Dashboard** — Posts table with Edit/Delete buttons
8. **Add New Post** — Form with all fields
9. **Post Added** — Dashboard mein naya post dikh raha hai
10. **Edit Post** — Existing post form pre-filled
11. **FileZilla** — FTP connection to live server, files uploading
12. **cPanel** — MySQL Databases page
13. **Live Website** — Domain par website live chal rahi hai

---

## CONCLUSION

Is practical mein humne ek complete dynamic blog website banai — **My Tech Blog** — PHP aur MySQL use karke. Website mein homepage par database se blog posts cards mein dikhte hain, single post page par full content aur visitor comments hain, aur admin panel mein complete CRUD operations hain.

Humne XAMPP par local development ki, database.sql se tables banaye, aur phir FileZilla FTP client use karke website ko live server (cPanel hosting) par deploy kiya. config.php mein sirf 4 database credentials change karne se website live ho gayi.

Is practical se hum yeh sikhte hain: PHP se dynamic web pages banana, MySQL database connectivity, session-based authentication, SQL injection se bachne ke liye prepared statements, Bootstrap 5 se responsive design, aur FTP se professional website deployment.

---

## 10 VIVA QUESTIONS WITH ANSWERS

**Q1. FTP kya hai? Iska full form kya hai?**
> **Ans:** FTP ka full form **File Transfer Protocol** hai. Ye ek standard network protocol hai jo local computer se remote server par files transfer karne ke liye use hota hai. Default port 21 use karta hai.

**Q2. FileZilla kya hai? Iska use kyon karte hain?**
> **Ans:** FileZilla ek **free, open-source FTP client** software hai. Iska use website ki files ko local computer se web server (hosting) par upload karne ke liye karte hain. Isme left panel mein local files aur right panel mein server files dikhte hain. Drag-and-drop se files transfer ho sakti hain.

**Q3. cPanel kya hai? Iske main features kya hain?**
> **Ans:** cPanel ek **web-based hosting control panel** hai jo Linux servers par use hota hai. Iske main features hain: MySQL Databases, phpMyAdmin, File Manager, Email Accounts, Domain Management, SSL/TLS, FTP Accounts, aur Error Logs. Ye server management ko easy banata hai.

**Q4. public_html folder kya hota hai?**
> **Ans:** `public_html` web server ka **document root folder** hota hai. Isme jo bhi files rakhi jati hain wo internet par accessible hoti hain. Agar aap `yourdomain.com` open karte ho to server `public_html` folder mein `index.php` ya `index.html` dhundhta hai. Is folder ke bahar raki files browser se directly access nahi hoti.

**Q5. Web Hosting aur Domain Name mein kya fark hai?**
> **Ans:** **Domain Name** website ka address hota hai jise log browser mein type karte hain (jaise `mytechblog.com`). **Web Hosting** wo service hai jo server par space provide karti hai jahan website ki actual files aur database store hoti hain. Domain aur hosting alag alag purchase hoti hain lekin domain ko hosting server se connect karna padta hai DNS settings se.

**Q6. phpMyAdmin kya hai? Iska use kab karte hain?**
> **Ans:** phpMyAdmin ek **web-based GUI tool** hai jo MySQL databases ko browser se manage karne ke liye use hota hai. Isme hum databases create/delete kar sakte hain, tables create kar sakte hain, SQL queries run kar sakte hain, aur `.sql` files import/export kar sakte hain. Deployment mein hum iska use database.sql file import karne ke liye karte hain.

**Q7. Database connection PHP mein kaise hoti hai? Prepared statements kyon use karte hain?**
> **Ans:** PHP mein `new mysqli(host, user, pass, db)` se database connection hoti hai. **Prepared Statements** SQL injection attacks se bachne ke liye use karte hain. Isme pehle query ka structure define hota hai (`?` placeholder ke saath), phir actual values bind hoti hain. Isse user ka malicious input SQL query ko affect nahi kar sakta.

**Q8. Session kya hota hai? Admin login mein iska use kaise hota hai?**
> **Ans:** **Session** server par temporarily data store karne ka tarika hai. PHP mein `session_start()` se session start hoti hai aur `$_SESSION` array mein data save hota hai. Admin login mein jab password sahi hota hai to `$_SESSION['admin_logged_in'] = true` set karte hain. Har admin page par ye check karte hain — agar set nahi hai to login page redirect karte hain.

**Q9. config.php alag kyun rakhte hain? Live deployment mein kya change karna padta hai?**
> **Ans:** `config.php` alag isliye rakhte hain taaki database credentials ek jagah ho aur har jagah change na karna pade. Live server par deploy karte waqt sirf **4 values** change karni hoti hain: `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` — ye cPanel mein create ki gayi database ki values hoti hain. `SITE_URL` bhi actual domain se update karna padta hai.

**Q10. 500 Internal Server Error aur Blank Page ka kya karan hota hai? Kaise fix karte hain?**
> **Ans:** **500 Internal Server Error** generally PHP syntax error, wrong file permissions, ya `.htaccess` issue se aata hai. **Blank White Page** tab aata hai jab PHP mein fatal error hoti hai lekin error reporting off hoti hai. Fix karne ke liye: (1) `error_reporting(E_ALL); ini_set('display_errors', 1);` temporarily add karo, (2) File permissions check karo — PHP files 644, folders 755 hone chahiye, (3) cPanel mein Error Logs check karo exact error jaanne ke liye.

---

*Practical-20 Complete | My Tech Blog — PHP + MySQL + Bootstrap 5 + FTP Deployment*
