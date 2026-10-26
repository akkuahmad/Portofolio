<?php
function getDB(): PDO
{
    $dbPath = __DIR__ . '/portfolio.db';
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    initDB($pdo);
    return $pdo;
}

function initDB(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS profile (
            id INTEGER PRIMARY KEY,
            name TEXT NOT NULL,
            title TEXT NOT NULL,
            bio TEXT NOT NULL,
            email TEXT NOT NULL,
            github TEXT,
            linkedin TEXT,
            location TEXT,
            avatar_url TEXT,
            resume_url TEXT
        );

        CREATE TABLE IF NOT EXISTS skills (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            category TEXT NOT NULL,
            level INTEGER DEFAULT 80
        );

        CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            tech_stack TEXT NOT NULL,
            github_url TEXT,
            live_url TEXT,
            image_url TEXT,
            featured INTEGER DEFAULT 0,
            sort_order INTEGER DEFAULT 0
        );

        CREATE TABLE IF NOT EXISTS experiences (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company TEXT NOT NULL,
            role TEXT NOT NULL,
            period TEXT NOT NULL,
            description TEXT NOT NULL,
            sort_order INTEGER DEFAULT 0
        );

        CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            is_read INTEGER DEFAULT 0
        );
    ");

    // Seed data if empty
    $count = $pdo->query("SELECT COUNT(*) FROM profile")->fetchColumn();
    if ($count == 0) {
        seedData($pdo);
    }
}

function seedData(PDO $pdo): void
{
    // Profile
    $pdo->exec("INSERT INTO profile (id, name, title, bio, email, github, linkedin, location) VALUES (
        1,
        'Ahmad Izzuddin',
        'Full Stack Developer',
        'Seorang Full Stack Developer dengan passion dalam membangun produk digital yang scalable dan berdampak. Berpengalaman dalam mengembangkan aplikasi web end-to-end, dari desain database hingga antarmuka pengguna yang intuitif. Saya percaya bahwa kode yang baik adalah kode yang mudah dibaca, dipelihara, dan di-test.',
        'akkuahmad23@email.com',
        'github.com/akkuahmad',
        'linkedin.com/in/akkuahmad',
        'Indonesia'
    )");

    // Skills
    $skills = [
        ['PHP', 'Backend', 90],
        ['Laravel', 'Backend', 60],
        ['Node.js', 'Backend', 80],
        ['Python', 'Backend', 75],
        ['MySQL', 'Database', 88],
        ['SQLite', 'Database', 85],
        ['React.js', 'Frontend', 85],
        ['JavaScript', 'Frontend', 90],

    ];
    $stmt = $pdo->prepare("INSERT INTO skills (name, category, level) VALUES (?, ?, ?)");
    foreach ($skills as $s) $stmt->execute($s);

    // Projects
    $projects = [
        ['E-Commerce Platform', 'Platform e-commerce full-featured dengan sistem pembayaran terintegrasi, manajemen inventori real-time, dan dashboard analytics untuk merchant. Mendukung ribuan transaksi per hari.', 'Laravel, Vue.js, MySQL, Redis, Midtrans', 'github.com/ahmadizzuddin/ecommerce', 'https://demo.example.com', 1, 1],
        ['Task Management SaaS', 'Aplikasi manajemen proyek berbasis SaaS dengan fitur kolaborasi tim real-time, Kanban board, time tracking, dan laporan produktivitas otomatis.', 'React.js, Node.js, PostgreSQL, Socket.io', 'github.com/ahmadizzuddin/taskmanager', '', 1, 2],
        ['API Gateway Service', 'Microservice API Gateway yang menangani autentikasi, rate limiting, dan routing untuk arsitektur microservices perusahaan. Mampu menangani 10k+ req/s.', 'PHP, Docker, Nginx, Redis', 'github.com/ahmadizzuddin/api-gateway', '', 1, 3],
        ['Blog CMS', 'Content Management System dengan editor markdown, SEO tools bawaan, sistem komentar, dan multi-author support. Dibangun dengan pendekatan headless CMS.', 'PHP Native, SQLite, Vanilla JS', 'github.com/ahmadizzuddin/blog-cms', '', 0, 4],
        ['Inventory System', 'Sistem manajemen inventori untuk UMKM dengan fitur barcode scanning, laporan stok, notifikasi reorder point, dan integrasi marketplace.', 'Laravel, MySQL, Bootstrap, jQuery', 'github.com/ahmadizzuddin/inventory', '', 0, 5],
        ['Weather Dashboard', 'Dashboard cuaca interaktif dengan visualisasi data real-time, prakiraan 7 hari, dan peta radar hujan. Menggunakan multiple weather API sumber.', 'Vue.js, Python, Chart.js', 'github.com/ahmadizzuddin/weather-dash', 'https://weather.example.com', 0, 6],
    ];
    $stmt = $pdo->prepare("INSERT INTO projects (title, description, tech_stack, github_url, live_url, featured, sort_order) VALUES (?,?,?,?,?,?,?)");
    foreach ($projects as $p) $stmt->execute($p);

    // Experiences
    $exps = [
        ['PT. Digital Nusantara', 'Senior Full Stack Developer', 'Jan 2022 – Sekarang', 'Memimpin pengembangan platform e-commerce B2B yang melayani 500+ merchant. Merancang arsitektur microservices dan mengoptimasi performa sistem hingga 3x lebih cepat. Mentoring 4 junior developer dalam tim.', 1],
        ['Startup Teknologi XYZ', 'Full Stack Developer', 'Mar 2020 – Des 2021', 'Mengembangkan aplikasi SaaS dari nol hingga product-market fit dengan 2.000+ pengguna aktif. Bertanggung jawab atas backend API, database design, dan sebagian besar fitur frontend.', 2],
        ['CV. Web Kreatif', 'Junior Web Developer', 'Jun 2019 – Feb 2020', 'Membangun dan memelihara website klien menggunakan PHP, Laravel, dan WordPress. Belajar best practices dalam pengembangan web dan version control dengan Git.', 3],
    ];
    $stmt = $pdo->prepare("INSERT INTO experiences (company, role, period, description, sort_order) VALUES (?,?,?,?,?)");
    foreach ($exps as $e) $stmt->execute($e);
}
