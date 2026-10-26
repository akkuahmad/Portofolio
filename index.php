<?php
require_once 'db.php';
$pdo = getDB();

$profile     = $pdo->query("SELECT * FROM profile WHERE id = 1")->fetch();
$skills      = $pdo->query("SELECT * FROM skills ORDER BY category, level DESC")->fetchAll();
$projects    = $pdo->query("SELECT * FROM projects ORDER BY sort_order")->fetchAll();
$featured    = array_filter($projects, fn($p) => $p['featured']);
$experiences = $pdo->query("SELECT * FROM experiences ORDER BY sort_order")->fetchAll();

$skillsByCategory = [];
foreach ($skills as $s) {
    $skillsByCategory[$s['category']][] = $s;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profile['name']) ?> — Full Stack Developer</title>
    <meta name="description" content="<?= htmlspecialchars(substr($profile['bio'], 0, 160)) ?>">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-primary: #0D1117;
            --bg-secondary: #161B22;
            --bg-card: #1C2128;
            --bg-hover: #21262D;
            --accent: #2979FF;
            --accent-glow: rgba(41, 121, 255, 0.18);
            --accent-dim: #1A4FBB;
            --text-primary: #E6EDF3;
            --text-secondary: #8B949E;
            --text-muted: #6E7681;
            --border: #30363D;
            --border-light: #21262D;
            --success: #3FB950;
            --warning: #D29922;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            line-height: 1.65;
            scroll-behavior: smooth;
        }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-primary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        /* ── NAVBAR ── */
        .navbar {
            background: rgba(13, 17, 23, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
            transition: background .3s;
        }

        .navbar-brand {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            color: var(--text-primary) !important;
            letter-spacing: -.02em;
        }

        .navbar-brand span {
            color: var(--accent);
        }

        .nav-link {
            color: var(--text-secondary) !important;
            font-size: .875rem;
            font-weight: 500;
            padding: 6px 14px !important;
            border-radius: 6px;
            transition: color .2s, background .2s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--text-primary) !important;
            background: var(--bg-hover);
        }

        .navbar-toggler {
            border-color: var(--border);
        }

        .navbar-toggler-icon {
            filter: invert(1);
        }

        /* ── HERO ── */
        #hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 120px 0 80px;
        }

        .hero-grid-bg {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(41, 121, 255, .04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(41, 121, 255, .04) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
        }

        .hero-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(41, 121, 255, .12) 0%, transparent 70%);
            top: -100px;
            right: -100px;
            pointer-events: none;
        }

        .hero-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(63, 185, 80, .08);
            border: 1px solid rgba(63, 185, 80, .25);
            color: var(--success);
            font-size: .78rem;
            font-family: 'JetBrains Mono', monospace;
            padding: 5px 14px;
            border-radius: 100px;
            margin-bottom: 28px;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .4;
                transform: scale(.8);
            }
        }

        .hero-name {
            font-size: clamp(2.4rem, 5vw, 4rem);
            font-weight: 700;
            letter-spacing: -.04em;
            line-height: 1.1;
            margin-bottom: 10px;
        }

        .hero-role {
            font-family: 'JetBrains Mono', monospace;
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: var(--accent);
            margin-bottom: 24px;
            font-weight: 400;
        }

        .hero-role::before {
            content: '> ';
            opacity: .4;
        }

        .hero-bio {
            color: var(--text-secondary);
            font-size: .95rem;
            max-width: 520px;
            margin-bottom: 36px;
        }

        .btn-primary-custom {
            background: var(--accent);
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: .875rem;
            padding: 11px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background .2s, transform .15s, box-shadow .2s;
        }

        .btn-primary-custom:hover {
            background: #3D8BFF;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(41, 121, 255, .35);
        }

        .btn-outline-custom {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-primary);
            font-weight: 500;
            font-size: .875rem;
            padding: 10px 22px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: border-color .2s, background .2s;
        }

        .btn-outline-custom:hover {
            border-color: var(--accent);
            background: var(--accent-glow);
            color: var(--text-primary);
        }

        .hero-avatar {
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #7C3AED);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7rem;
            position: relative;
            margin: 0 auto;
        }

        .hero-avatar::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #7C3AED);
            z-index: -1;
            filter: blur(16px);
            opacity: .5;
        }

        .hero-stats {
            display: flex;
            gap: 32px;
            margin-top: 40px;
            padding-top: 32px;
            border-top: 1px solid var(--border);
        }

        .stat-item .num {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
        }

        .stat-item .num span {
            color: var(--accent);
        }

        .stat-item .label {
            font-size: .75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        /* ── SECTIONS ── */
        section {
            padding: 100px 0;
        }

        .section-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: .78rem;
            color: var(--accent);
            letter-spacing: .12em;
            margin-bottom: 10px;
            display: block;
        }

        .section-title {
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 700;
            letter-spacing: -.03em;
            margin-bottom: 14px;
        }

        .section-desc {
            color: var(--text-secondary);
            font-size: .95rem;
            max-width: 560px;
        }

        .section-divider {
            width: 40px;
            height: 3px;
            background: var(--accent);
            border-radius: 2px;
            margin-bottom: 16px;
        }

        /* ── ABOUT ── */
        #about {
            background: var(--bg-secondary);
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 18px 0;
            border-bottom: 1px solid var(--border-light);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            background: var(--accent-glow);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            flex-shrink: 0;
            font-size: .95rem;
        }

        .info-label {
            font-size: .75rem;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .info-value {
            font-size: .9rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        /* ── SKILLS ── */
        .skill-category-title {
            font-family: 'JetBrains Mono', monospace;
            font-size: .8rem;
            color: var(--text-muted);
            letter-spacing: .1em;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
        }

        .skill-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            font-size: .8rem;
            font-weight: 500;
            padding: 7px 14px;
            border-radius: 8px;
            margin: 4px;
            transition: border-color .2s, color .2s, background .2s;
        }

        .skill-tag:hover {
            border-color: var(--accent);
            color: var(--text-primary);
            background: var(--accent-glow);
        }

        .skill-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .level-high {
            background: var(--success);
        }

        .level-mid {
            background: var(--warning);
        }

        .level-low {
            background: var(--accent);
        }

        /* ── PROJECTS ── */
        #projects {
            background: var(--bg-secondary);
        }

        .project-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 28px;
            height: 100%;
            transition: border-color .2s, transform .2s, box-shadow .2s;
            position: relative;
            overflow: hidden;
        }

        .project-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent), #7C3AED);
            opacity: 0;
            transition: opacity .2s;
        }

        .project-card:hover {
            border-color: var(--accent);
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(41, 121, 255, .12);
        }

        .project-card:hover::before {
            opacity: 1;
        }

        .project-card.featured {
            border-color: rgba(41, 121, 255, .3);
        }

        .project-badge {
            font-size: .7rem;
            font-family: 'JetBrains Mono', monospace;
            background: var(--accent-glow);
            color: var(--accent);
            border: 1px solid rgba(41, 121, 255, .2);
            padding: 3px 10px;
            border-radius: 100px;
            margin-bottom: 16px;
            display: inline-block;
        }

        .project-title {
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-primary);
        }

        .project-desc {
            font-size: .855rem;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 18px;
        }

        .tech-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 20px;
        }

        .tech-chip {
            font-family: 'JetBrains Mono', monospace;
            font-size: .72rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            color: var(--text-muted);
            padding: 3px 10px;
            border-radius: 5px;
        }

        .project-links {
            display: flex;
            gap: 10px;
        }

        .proj-link {
            font-size: .8rem;
            font-weight: 500;
            color: var(--text-secondary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            transition: color .2s, border-color .2s;
        }

        .proj-link:hover {
            color: var(--accent);
            border-color: var(--accent);
        }

        .proj-link.disabled {
            opacity: .35;
            pointer-events: none;
        }

        /* ── EXPERIENCE ── */
        .timeline {
            position: relative;
            padding-left: 28px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 8px;
            bottom: 0;
            width: 1px;
            background: var(--border);
        }

        .timeline-item {
            position: relative;
            padding-bottom: 40px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -24px;
            top: 6px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--accent);
            border: 3px solid var(--bg-primary);
            box-shadow: 0 0 0 2px var(--accent);
        }

        .timeline-role {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .timeline-company {
            color: var(--accent);
            font-size: .875rem;
            font-weight: 500;
        }

        .timeline-period {
            font-family: 'JetBrains Mono', monospace;
            font-size: .75rem;
            color: var(--text-muted);
            background: var(--bg-card);
            border: 1px solid var(--border);
            padding: 2px 10px;
            border-radius: 100px;
            display: inline-block;
            margin: 6px 0 10px;
        }

        .timeline-desc {
            font-size: .875rem;
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* ── CONTACT ── */
        #contact {
            background: var(--bg-secondary);
        }

        .contact-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px;
        }

        .form-control,
        .form-select {
            background: var(--bg-secondary) !important;
            border: 1px solid var(--border) !important;
            color: var(--text-primary) !important;
            font-size: .875rem;
            padding: 12px 16px;
            border-radius: 8px;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 3px rgba(41, 121, 255, .2) !important;
            border-color: var(--accent) !important;
            outline: none;
        }

        .form-control::placeholder {
            color: var(--text-muted) !important;
        }

        .form-label {
            font-size: .82rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .social-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: border-color .2s, color .2s, background .2s;
            font-size: .875rem;
            margin-bottom: 12px;
        }

        .social-link:hover {
            border-color: var(--accent);
            color: var(--text-primary);
            background: var(--accent-glow);
        }

        .social-link .si {
            font-size: 1.2rem;
            color: var(--accent);
            width: 22px;
        }

        .social-link-label {
            font-weight: 500;
        }

        .social-link-sub {
            font-size: .75rem;
            color: var(--text-muted);
        }

        .btn-send {
            background: var(--accent);
            color: #fff;
            border: none;
            font-weight: 600;
            font-size: .875rem;
            padding: 13px 28px;
            border-radius: 8px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: background .2s, box-shadow .2s;
        }

        .btn-send:hover {
            background: #3D8BFF;
            box-shadow: 0 6px 24px rgba(41, 121, 255, .3);
        }

        .btn-send:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .alert-custom {
            padding: 12px 18px;
            border-radius: 8px;
            font-size: .85rem;
            margin-top: 14px;
        }

        .alert-success-custom {
            background: rgba(63, 185, 80, .1);
            border: 1px solid rgba(63, 185, 80, .25);
            color: var(--success);
        }

        .alert-error-custom {
            background: rgba(248, 81, 73, .1);
            border: 1px solid rgba(248, 81, 73, .25);
            color: #F85149;
        }

        /* ── FOOTER ── */
        footer {
            background: var(--bg-primary);
            border-top: 1px solid var(--border);
            padding: 32px 0;
            text-align: center;
        }

        footer .mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: .8rem;
            color: var(--text-muted);
        }

        /* ── BACK TO TOP ── */
        #backToTop {
            position: fixed;
            bottom: 28px;
            right: 28px;
            width: 42px;
            height: 42px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: background .2s, transform .2s;
            box-shadow: 0 4px 16px rgba(41, 121, 255, .4);
        }

        #backToTop.show {
            display: flex;
        }

        #backToTop:hover {
            background: #3D8BFF;
            transform: translateY(-2px);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .about-grid {
                grid-template-columns: 1fr;
            }

            .hero-stats {
                gap: 20px;
            }

            .contact-card {
                padding: 24px;
            }

            #hero {
                padding-top: 100px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><span>&lt;</span><?= strtolower(explode(' ', $profile['name'])[0]) ?><span>/&gt;</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto gap-1">
                    <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#skills">Skills</a></li>
                    <li class="nav-item"><a class="nav-link" href="#projects">Proyek</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Kontak</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section id="hero">
        <div class="hero-grid-bg"></div>
        <div class="hero-glow"></div>
        <div class="container position-relative">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="hero-status">
                        <span class="status-dot"></span>
                        Open to opportunities
                    </div>
                    <h1 class="hero-name"><?= htmlspecialchars($profile['name']) ?></h1>
                    <p class="hero-role"><?= htmlspecialchars($profile['title']) ?></p>
                    <p class="hero-bio"><?= htmlspecialchars($profile['bio']) ?></p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#projects" class="btn-primary-custom">
                            <i class="bi bi-grid-3x3-gap-fill"></i> Lihat Proyek
                        </a>
                        <a href="#contact" class="btn-outline-custom">
                            <i class="bi bi-send"></i> Hubungi Saya
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="num">3<span>+</span></div>
                            <div class="label">Tahun Pengalaman</div>
                        </div>
                        <div class="stat-item">
                            <div class="num"><?= count($projects) ?><span>+</span></div>
                            <div class="label">Proyek Selesai</div>
                        </div>
                        <div class="stat-item">
                            <div class="num"><?= count($skills) ?><span>+</span></div>
                            <div class="label">Tech Stack</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <div class="hero-avatar">👨‍💻</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section id="about">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <span class="section-label">// tentang saya</span>
                    <div class="section-divider"></div>
                    <h2 class="section-title">Siapa Ahmad Izzuddin?</h2>
                    <p class="section-desc"><?= htmlspecialchars($profile['bio']) ?></p>
                    <div class="mt-4">
                        <a href="mailto:<?= htmlspecialchars($profile['email']) ?>" class="btn-primary-custom d-inline-flex">
                            <i class="bi bi-envelope-fill"></i> Kirim Email
                        </a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-person-badge"></i></div>
                        <div>
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value"><?= htmlspecialchars($profile['name']) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-briefcase"></i></div>
                        <div>
                            <div class="info-label">Posisi</div>
                            <div class="info-value"><?= htmlspecialchars($profile['title']) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-envelope"></i></div>
                        <div>
                            <div class="info-label">Email</div>
                            <div class="info-value"><?= htmlspecialchars($profile['email']) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <div class="info-label">Lokasi</div>
                            <div class="info-value"><?= htmlspecialchars($profile['location']) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-github"></i></div>
                        <div>
                            <div class="info-label">GitHub</div>
                            <div class="info-value"><?= htmlspecialchars($profile['github']) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-linkedin"></i></div>
                        <div>
                            <div class="info-label">LinkedIn</div>
                            <div class="info-value"><?= htmlspecialchars($profile['linkedin']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SKILLS -->
    <section id="skills">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">// tech stack</span>
                <div class="section-divider mx-auto"></div>
                <h2 class="section-title">Keahlian & Teknologi</h2>
                <p class="section-desc mx-auto">Teknologi yang saya gunakan untuk membangun produk digital yang handal dan scalable.</p>
            </div>
            <div class="row g-4">
                <?php foreach ($skillsByCategory as $cat => $catSkills): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="skill-category-title"><i class="bi bi-code-slash me-2"></i><?= htmlspecialchars($cat) ?></div>
                        <div>
                            <?php foreach ($catSkills as $sk):
                                $dot = $sk['level'] >= 85 ? 'level-high' : ($sk['level'] >= 75 ? 'level-mid' : 'level-low');
                            ?>
                                <span class="skill-tag">
                                    <span class="skill-dot <?= $dot ?>"></span>
                                    <?= htmlspecialchars($sk['name']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-4 text-center">
                <small style="color:var(--text-muted); font-family:'JetBrains Mono',monospace; font-size:.72rem;">
                    <span style="color:var(--success)">●</span> Mahir &nbsp;
                    <span style="color:var(--warning)">●</span> Menengah &nbsp;
                    <span style="color:var(--accent)">●</span> Sedang Belajar
                </small>
            </div>
        </div>
    </section>

    <!-- PROJECTS -->
    <section id="projects">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">// portofolio</span>
                <div class="section-divider mx-auto"></div>
                <h2 class="section-title">Proyek Pilihan</h2>
                <p class="section-desc mx-auto">Beberapa proyek yang pernah saya bangun — dari produk SaaS hingga sistem enterprise.</p>
            </div>
            <div class="row g-4">
                <?php foreach ($projects as $proj): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="project-card <?= $proj['featured'] ? 'featured' : '' ?>">
                            <?php if ($proj['featured']): ?>
                                <div class="project-badge"><i class="bi bi-star-fill me-1"></i>Featured</div>
                            <?php endif; ?>
                            <div class="project-title"><?= htmlspecialchars($proj['title']) ?></div>
                            <div class="project-desc"><?= htmlspecialchars($proj['description']) ?></div>
                            <div class="tech-chips">
                                <?php foreach (explode(', ', $proj['tech_stack']) as $tech): ?>
                                    <span class="tech-chip"><?= htmlspecialchars(trim($tech)) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="project-links">
                                <?php if (!empty($proj['github_url'])): ?>
                                    <a href="https://<?= htmlspecialchars($proj['github_url']) ?>" class="proj-link" target="_blank">
                                        <i class="bi bi-github"></i> Code
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($proj['live_url'])): ?>
                                    <a href="<?= htmlspecialchars($proj['live_url']) ?>" class="proj-link" target="_blank">
                                        <i class="bi bi-box-arrow-up-right"></i> Demo
                                    </a>
                                <?php else: ?>
                                    <span class="proj-link disabled"><i class="bi bi-box-arrow-up-right"></i> Demo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- CONTACT -->
    <section id="contact">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">// mari berkolaborasi</span>
                <div class="section-divider mx-auto"></div>
                <h2 class="section-title">Hubungi Saya</h2>
                <p class="section-desc mx-auto">Punya proyek menarik atau sekadar ingin ngobrol soal teknologi? Jangan sungkan untuk menghubungi.</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-7">
                    <div class="contact-card">
                        <h5 class="mb-4" style="color:var(--text-primary); font-weight:600;">Kirim Pesan</h5>
                        <form id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama</label>
                                    <input type="text" class="form-control" name="name" placeholder="Nama Anda" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="email@domain.com" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Subjek</label>
                                    <input type="text" class="form-control" name="subject" placeholder="Topik pesan">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Pesan</label>
                                    <textarea class="form-control" name="message" rows="5" placeholder="Ceritakan lebih detail..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-send" id="submitBtn">
                                        <i class="bi bi-send-fill"></i>
                                        <span id="btnText">Kirim Pesan</span>
                                    </button>
                                    <div id="formAlert"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="mb-4" style="color:var(--text-primary); font-weight:600;">Temukan Saya</h5>
                    <a href="mailto:<?= htmlspecialchars($profile['email']) ?>" class="social-link">
                        <i class="bi bi-envelope-fill si"></i>
                        <div>
                            <div class="social-link-label">Email</div>
                            <div class="social-link-sub"><?= htmlspecialchars($profile['email']) ?></div>
                        </div>
                        <i class="bi bi-arrow-right ms-auto"></i>
                    </a>
                    <a href="https://<?= htmlspecialchars($profile['github']) ?>" class="social-link" target="_blank">
                        <i class="bi bi-github si"></i>
                        <div>
                            <div class="social-link-label">GitHub</div>
                            <div class="social-link-sub"><?= htmlspecialchars($profile['github']) ?></div>
                        </div>
                        <i class="bi bi-arrow-right ms-auto"></i>
                    </a>
                    <a href="https://<?= htmlspecialchars($profile['linkedin']) ?>" class="social-link" target="_blank">
                        <i class="bi bi-linkedin si"></i>
                        <div>
                            <div class="social-link-label">LinkedIn</div>
                            <div class="social-link-sub"><?= htmlspecialchars($profile['linkedin']) ?></div>
                        </div>
                        <i class="bi bi-arrow-right ms-auto"></i>
                    </a>
                    <div class="social-link" style="cursor:default">
                        <i class="bi bi-geo-alt-fill si"></i>
                        <div>
                            <div class="social-link-label">Lokasi</div>
                            <div class="social-link-sub"><?= htmlspecialchars($profile['location']) ?></div>
                        </div>
                    </div>

                    <div class="mt-4 p-4" style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;">
                        <div style="font-family:'JetBrains Mono',monospace;font-size:.75rem;color:var(--text-muted);">Status Ketersediaan</div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="status-dot"></span>
                            <span style="color:var(--success);font-size:.875rem;font-weight:500;">Tersedia untuk proyek baru</span>
                        </div>
                        <p style="font-size:.78rem;color:var(--text-muted);margin-top:8px;margin-bottom:0;">Biasanya membalas dalam 24 jam kerja.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="mono mb-2">
                &lt;?php echo "Built with ♥ by <?= htmlspecialchars($profile['name']) ?>"; ?&gt;
            </div>
            <div style="font-size:.75rem; color:var(--text-muted);">
                © <?= date('Y') ?> <?= htmlspecialchars($profile['name']) ?> · PHP + SQLite + Bootstrap 5
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button id="backToTop" title="Ke atas"><i class="bi bi-chevron-up"></i></button>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ── NAVBAR ACTIVE on scroll ──
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-link');
        window.addEventListener('scroll', () => {
            const y = window.scrollY + 100;
            sections.forEach(sec => {
                if (y >= sec.offsetTop && y < sec.offsetTop + sec.offsetHeight) {
                    navLinks.forEach(l => l.classList.remove('active'));
                    const link = document.querySelector('.nav-link[href="#' + sec.id + '"]');
                    if (link) link.classList.add('active');
                }
            });
            // Back to top
            const btn = document.getElementById('backToTop');
            btn.classList.toggle('show', window.scrollY > 400);
        });

        document.getElementById('backToTop').addEventListener('click', () => window.scrollTo({
            top: 0,
            behavior: 'smooth'
        }));

        // ── CONTACT FORM ──
        document.getElementById('contactForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const txt = document.getElementById('btnText');
            const alertEl = document.getElementById('formAlert');

            btn.disabled = true;
            txt.textContent = 'Mengirim...';
            alertEl.innerHTML = '';

            try {
                const res = await fetch('contact.php', {
                    method: 'POST',
                    body: new FormData(this)
                });
                const data = await res.json();
                alertEl.innerHTML = `<div class="alert-custom ${data.success ? 'alert-success-custom' : 'alert-error-custom'}">
            <i class="bi bi-${data.success ? 'check-circle' : 'exclamation-triangle'}-fill me-2"></i>${data.message}
        </div>`;
                if (data.success) this.reset();
            } catch {
                alertEl.innerHTML = `<div class="alert-custom alert-error-custom">
            <i class="bi bi-wifi-off me-2"></i>Koneksi gagal. Coba lagi.
        </div>`;
            } finally {
                btn.disabled = false;
                txt.textContent = 'Kirim Pesan';
            }
        });

        // ── SMOOTH SCROLL for all anchor links ──
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const target = document.querySelector(a.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu
                    const menu = document.getElementById('navMenu');
                    if (menu.classList.contains('show')) {
                        new bootstrap.Collapse(menu).hide();
                    }
                }
            });
        });
    </script>

</body>

</html>