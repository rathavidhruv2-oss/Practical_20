-- ============================================================
-- My Tech Blog - Database Setup File
-- Practical-20: Deploy Dynamic Website with DB Connectivity
-- ============================================================

-- Step 1: Database banao
CREATE DATABASE IF NOT EXISTS mytechblog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Step 2: Use karo
USE mytechblog;

-- ============================================================
-- TABLE 1: admin (Admin login ke liye)
-- ============================================================
CREATE TABLE IF NOT EXISTS admin (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50)  NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,          -- bcrypt hashed password
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default Admin: username = admin | password = admin123
-- Password is bcrypt hash of "admin123"
INSERT INTO admin (username, password) VALUES (
    'admin',
    '$2y$10$z3TqjvPQhv6vxaMij0zKLO5pWNSo8K2y8pWA5sJyofpzcKdobLpk2'
);

-- ============================================================
-- TABLE 2: posts (Blog posts ke liye)
-- ============================================================
CREATE TABLE IF NOT EXISTS posts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255) NOT NULL,
    category   VARCHAR(100) NOT NULL DEFAULT 'General',
    author     VARCHAR(100) NOT NULL DEFAULT 'Admin',
    content    TEXT         NOT NULL,
    created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sample Blog Posts
INSERT INTO posts (title, category, author, content) VALUES
(
    'PHP Kya Hai? Beginners Guide',
    'PHP',
    'Admin',
    'PHP (Hypertext Preprocessor) ek server-side scripting language hai jo web development ke liye use hoti hai. Iska use dynamic web pages banane ke liye hota hai. PHP files ka extension .php hota hai aur ye HTML ke andar embed ho sakti hai. PHP free aur open-source hai. Iska use WordPress, Facebook jaise bade platforms ne kiya hai. Beginners ke liye PHP seekhna bahut easy hai kyunki iska syntax simple aur well-documented hai. Aap XAMPP install karke locally PHP practice kar sakte hain.'
),
(
    'MySQL Database Tutorial for Beginners',
    'Database',
    'Admin',
    'MySQL ek popular open-source relational database management system (RDBMS) hai. Iska use web applications mein data store karne ke liye hota hai. MySQL structured query language (SQL) use karta hai data ko manage karne ke liye. MySQL ke basic commands hain: CREATE, SELECT, INSERT, UPDATE, DELETE. MySQL ko PHP ke saath combine karke powerful dynamic websites banai ja sakti hain. phpMyAdmin ek GUI tool hai jo MySQL databases ko easily manage karne mein help karta hai.'
),
(
    'Bootstrap 5 Se Responsive Website Banao',
    'CSS',
    'Admin',
    'Bootstrap 5 ek popular CSS framework hai jo responsive websites banane mein help karta hai. Bootstrap use karke hum grid system, components aur utilities ka fayda utha sakte hain. Bootstrap 5 mein jQuery ki dependency nahi hai jo ise aur lightweight banata hai. Bootstrap ke grid system mein 12 columns hote hain. Responsive design ka matlab hai website mobile, tablet aur desktop sab par achhi tarah dikhni chahiye. Bootstrap ke ready-made components jaise Navbar, Cards, Buttons, Forms time bachate hain.'
),
(
    'XAMPP Install Karna Aur Local Server Setup',
    'Tools',
    'Admin',
    'XAMPP ek free cross-platform web server solution hai jo Apache, MySQL, PHP aur Perl include karta hai. XAMPP install karke aap apne computer par local server run kar sakte hain bina internet ke. XAMPP install karne ke baad Apache aur MySQL modules start karte hain. htdocs folder mein apna project rakhte hain aur browser mein localhost/project-name se access karte hain. XAMPP development ke liye best tool hai kyunki isse real hosting jaisa environment milta hai.'
),
(
    'FTP Se Website Deploy Karna - Complete Guide',
    'Deployment',
    'Admin',
    'FTP (File Transfer Protocol) ek standard network protocol hai jo files ko local computer se remote server par transfer karne ke liye use hota hai. FileZilla ek popular free FTP client hai. Website deploy karne ke liye pehle hosting account aur domain chahiye. cPanel hosting providers ka control panel hai jo server manage karne mein help karta hai. FileZilla mein host, username, password aur port 21 enter karke server se connect kar sakte hain. Files ko public_html folder mein upload karte hain.'
);

-- ============================================================
-- TABLE 3: comments (Visitor comments ke liye)
-- ============================================================
CREATE TABLE IF NOT EXISTS comments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    post_id    INT          NOT NULL,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    comment    TEXT         NOT NULL,
    created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Sample Comments
INSERT INTO comments (post_id, name, email, comment) VALUES
(1, 'Rahul Sharma',  'rahul@example.com',  'Bahut achha article hai! PHP mujhe samajh aa gaya.'),
(1, 'Priya Singh',   'priya@example.com',  'Thank you! Bahut helpful tha ye tutorial.'),
(2, 'Amit Kumar',    'amit@example.com',   'MySQL tutorial bilkul clear hai. Keep it up!'),
(3, 'Sneha Patel',   'sneha@example.com',  'Bootstrap 5 ka explanation bahut simple tha.'),
(5, 'Vikram Yadav',  'vikram@example.com', 'FTP deployment guide se meri problem solve ho gayi!');

-- ============================================================
-- End of database.sql
-- ============================================================
