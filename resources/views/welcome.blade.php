<!DOCTYPE html>
<html lang="en" dir="ltr" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CoreShop — The complete marketplace platform for Jordan. Connecting sellers, clients, and administrators in one seamless system.">
    <title>CoreShop — منصة التجارة الإلكترونية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
        --brand: #0A0A0A;
        --accent: #FF4D4F;
        --accent-light: #FFF1F1;
        --bg: #FAFAFA;
        --white: #FFFFFF;
        --secondary: #6B7280;
        --border: #E5E7EB;
        --shadow: 0 4px 24px rgba(0,0,0,0.07);
        --font-en: 'Manrope', sans-serif;
        --font-ar: 'IBM Plex Sans Arabic', sans-serif;
        --radius: 14px;
        --radius-lg: 22px;
        --max: 1160px;
        --ease: 0.25s ease;
    }
    html { scroll-behavior: smooth; }
    body { font-family: var(--font-en); background: var(--white); color: var(--brand); line-height: 1.6; overflow-x: hidden; -webkit-font-smoothing: antialiased; }
    [dir="rtl"] body { font-family: var(--font-ar); }
    a { text-decoration: none; color: inherit; }
    img { max-width: 100%; display: block; }

    .wrap { max-width: var(--max); margin: 0 auto; padding: 0 24px; }

    /* Typography */
    .h1 { font-size: clamp(36px, 5.5vw, 66px); font-weight: 800; line-height: 1.08; letter-spacing: -0.025em; }
    .h2 { font-size: clamp(28px, 4vw, 50px); font-weight: 800; line-height: 1.12; letter-spacing: -0.02em; }
    .h3 { font-size: clamp(20px, 2.5vw, 28px); font-weight: 700; line-height: 1.3; }
    .h4 { font-size: 17px; font-weight: 700; line-height: 1.4; }
    .lead { font-size: clamp(16px, 1.8vw, 19px); line-height: 1.75; color: var(--secondary); }
    .body { font-size: 15px; line-height: 1.75; color: var(--secondary); }
    .eyebrow { font-size: 12px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--accent); }

    /* Buttons */
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all var(--ease); border: none; font-family: inherit; white-space: nowrap; }
    .btn-dark { background: var(--brand); color: #fff; }
    .btn-dark:hover { background: #222; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,0.18); }
    .btn-red { background: var(--accent); color: #fff; }
    .btn-red:hover { background: #e63e40; transform: translateY(-1px); box-shadow: 0 8px 28px rgba(255,77,79,0.35); }
    .btn-ghost { background: transparent; color: var(--brand); border: 2px solid var(--border); }
    .btn-ghost:hover { border-color: var(--brand); background: var(--bg); }
    .btn-ghost-white { background: transparent; color: #fff; border: 2px solid rgba(255,255,255,0.22); }
    .btn-ghost-white:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.5); }

    /* Badge */
    .badge { display: inline-flex; align-items: center; gap: 7px; padding: 7px 16px; background: var(--accent-light); color: var(--accent); border-radius: 100px; font-size: 13px; font-weight: 600; }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent); flex-shrink: 0; }

    /* Sections */
    .section { padding: 100px 0; }
    .section-gray { background: var(--bg); }
    .section-dark { background: var(--brand); color: #fff; }
    .sec-head { text-align: center; max-width: 660px; margin: 0 auto 68px; }
    .sec-head .lead { margin-top: 16px; }

    /* ─── NAVBAR ─── */
    .nav {
        position: fixed; top: 0; left: 0; right: 0; z-index: 200;
        height: 70px; display: flex; align-items: center;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(0,0,0,0.06);
        transition: box-shadow var(--ease);
    }
    .nav .wrap { display: flex; align-items: center; justify-content: space-between; width: 100%; }
    .nav-logo { display: flex; align-items: center; gap: 10px; font-size: 21px; font-weight: 800; letter-spacing: -0.02em; }
    .nav-logo-box { width: 36px; height: 36px; background: var(--brand); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .nav-logo-box span { color: var(--accent); font-size: 17px; font-weight: 800; }
    .nav-links { display: flex; align-items: center; gap: 32px; list-style: none; }
    .nav-links a { font-size: 14px; font-weight: 600; color: var(--secondary); transition: color var(--ease); }
    .nav-links a:hover { color: var(--brand); }
    .nav-right { display: flex; align-items: center; gap: 12px; }
    .lang-toggle { display: flex; align-items: center; background: var(--bg); border-radius: 10px; padding: 3px; border: 1px solid var(--border); }
    .lang-btn { padding: 6px 14px; border-radius: 7px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all var(--ease); border: none; background: transparent; color: var(--secondary); font-family: inherit; }
    .lang-btn.active { background: #fff; color: var(--brand); box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
    .hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 8px; }
    .hamburger span { display: block; width: 22px; height: 2px; background: var(--brand); border-radius: 2px; transition: all var(--ease); }
    .mobile-nav { display: none; position: fixed; top: 70px; left: 0; right: 0; background: #fff; border-bottom: 1px solid var(--border); padding: 12px 16px; z-index: 199; flex-direction: column; gap: 2px; }
    .mobile-nav.open { display: flex; }
    .mobile-nav a { padding: 12px 16px; font-size: 15px; font-weight: 600; color: var(--secondary); border-radius: 10px; transition: all var(--ease); }
    .mobile-nav a:hover { background: var(--bg); color: var(--brand); }

    /* ─── HERO ─── */
    .hero { min-height: 100vh; display: flex; align-items: center; padding-top: 70px; overflow: hidden; position: relative; }
    .hero::before { content: ''; position: absolute; top: -180px; right: -180px; width: 560px; height: 560px; background: radial-gradient(circle, rgba(255,77,79,0.07) 0%, transparent 70%); border-radius: 50%; pointer-events: none; }
    .hero-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; padding: 80px 0 64px; }
    .hero-eyebrow { margin-bottom: 22px; }
    .hero-title { margin-bottom: 22px; }
    .hero-sub { margin-bottom: 40px; }
    .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }

    /* Phone mockup */
    .hero-visual { display: flex; justify-content: center; align-items: center; position: relative; height: 580px; }
    .phone { width: 252px; height: 506px; background: #111; border-radius: 40px; position: relative; box-shadow: 0 40px 80px rgba(0,0,0,0.32), 0 0 0 1px rgba(255,255,255,0.06); overflow: hidden; border: 7px solid #1C1C1E; z-index: 2; }
    .phone-notch { width: 90px; height: 22px; background: #111; border-radius: 0 0 14px 14px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index: 4; }
    .phone-screen { position: absolute; inset: 0; background: var(--bg); padding: 32px 10px 12px; display: flex; flex-direction: column; gap: 7px; overflow: hidden; }
    .ph-bar { background: #fff; border-radius: 10px; padding: 9px 10px; display: flex; align-items: center; gap: 7px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
    .ph-avatar { width: 26px; height: 26px; border-radius: 50%; background: var(--accent-light); flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; }
    .ph-lines { flex: 1; display: flex; flex-direction: column; gap: 4px; }
    .ph-line { height: 5px; border-radius: 3px; background: var(--border); }
    .ph-line.dark { background: rgba(10,10,10,0.12); width: 65%; }
    .ph-line.short { width: 40%; }
    .ph-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; }
    .ph-card { background: #fff; border-radius: 10px; overflow: hidden; }
    .ph-card-img { height: 58px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .ph-card-img.c1 { background: linear-gradient(135deg,#FFE4E4,#FFF1F1); }
    .ph-card-img.c2 { background: linear-gradient(135deg,#EDE9FE,#F5F3FF); }
    .ph-card-img.c3 { background: linear-gradient(135deg,#DCFCE7,#F0FDF4); }
    .ph-card-body { padding: 5px 7px 7px; display: flex; flex-direction: column; gap: 3px; }
    .ph-wide { background: #fff; border-radius: 10px; overflow: hidden; display: flex; gap: 8px; align-items: center; padding: 8px; }
    .ph-wide-img { width: 40px; height: 40px; border-radius: 8px; background: linear-gradient(135deg,#FEF3C7,#FFF7ED); display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .ph-stat-bar { background: var(--brand); border-radius: 10px; padding: 10px; display: flex; justify-content: space-around; }
    .ph-stat { display: flex; flex-direction: column; align-items: center; gap: 3px; }
    .ph-stat-n { height: 8px; width: 28px; border-radius: 4px; background: var(--accent); opacity: 0.85; }
    .ph-stat-l { height: 4px; width: 22px; border-radius: 2px; background: rgba(255,255,255,0.2); }

    /* Floating cards */
    .fc { position: absolute; background: #fff; border-radius: 16px; box-shadow: 0 16px 48px rgba(0,0,0,0.13); padding: 13px 17px; display: flex; align-items: center; gap: 11px; z-index: 5; white-space: nowrap; animation: bob 4s ease-in-out infinite; }
    .fc-1 { top: 14%; right: -16px; animation-delay: 0s; }
    .fc-2 { bottom: 18%; left: -28px; animation-delay: 1.6s; }
    .fc-3 { top: 56%; right: -36px; animation-delay: 0.9s; }
    .fc-icon { width: 38px; height: 38px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
    .fc-icon.red { background: var(--accent-light); }
    .fc-icon.green { background: #ECFDF5; }
    .fc-icon.blue { background: #EFF6FF; }
    .fc-p { font-size: 13px; font-weight: 700; color: var(--brand); }
    .fc-s { font-size: 11px; color: var(--secondary); }
    @keyframes bob { 0%,100%{ transform: translateY(0); } 50%{ transform: translateY(-9px); } }

    /* ─── STATS STRIP ─── */
    .strip { background: var(--brand); padding: 60px 0; }
    .strip-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 0; }
    .strip-item { text-align: center; padding: 0 24px; position: relative; }
    .strip-item:not(:last-child)::after { content:''; position:absolute; right:0; top:50%; transform:translateY(-50%); height:48px; width:1px; background:rgba(255,255,255,0.1); }
    [dir="rtl"] .strip-item:not(:last-child)::after { right:auto; left:0; }
    .strip-num { font-size: 44px; font-weight: 800; color: #fff; line-height: 1; margin-bottom: 6px; }
    .strip-num em { color: var(--accent); font-style: normal; }
    .strip-lbl { font-size: 14px; color: rgba(255,255,255,0.5); font-weight: 500; }

    /* ─── ABOUT / ROLES ─── */
    .roles-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 22px; }
    .role-card { border-radius: var(--radius-lg); padding: 36px 30px; transition: transform var(--ease); cursor: default; }
    .role-card:hover { transform: translateY(-5px); }
    .role-card.blue { background: #EFF6FF; }
    .role-card.red { background: #FFF1F0; }
    .role-card.green { background: #F0FDF4; }
    .role-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; font-size: 26px; }
    .role-icon.blue { background: #DBEAFE; }
    .role-icon.red { background: #FFE4E6; }
    .role-icon.green { background: #D1FAE5; }
    .role-card .h4 { margin-bottom: 10px; }

    /* ─── HOW IT WORKS ─── */
    .how-tabs { display: flex; background: var(--bg); border-radius: 13px; padding: 4px; gap: 4px; width: fit-content; margin: 0 auto 56px; border: 1px solid var(--border); }
    .how-tab { padding: 11px 26px; border-radius: 9px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all var(--ease); border: none; background: transparent; color: var(--secondary); font-family: inherit; }
    .how-tab.active { background: #fff; color: var(--brand); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .how-panel { display: none; }
    .how-panel.active { display: grid; grid-template-columns: repeat(3,1fr); gap: 28px; }
    .step { text-align: center; padding: 28px 20px; position: relative; }
    .step-num { width: 44px; height: 44px; border-radius: 13px; background: var(--brand); color: #fff; font-size: 18px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
    .step-emoji { font-size: 40px; margin-bottom: 16px; }
    .step .h4 { margin-bottom: 10px; }
    .how-panel.active .step:not(:last-child)::after { content:'→'; position:absolute; right:-18px; top:80px; color:var(--border); font-size:22px; }
    [dir="rtl"] .how-panel.active .step:not(:last-child)::after { content:'←'; right:auto; left:-18px; }

    /* ─── FEATURES ─── */
    .feat-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; }
    .feat-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 30px 26px; transition: all var(--ease); position: relative; overflow: hidden; }
    .feat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent); opacity:0; transition:opacity var(--ease); }
    .feat-card:hover { box-shadow: var(--shadow); transform: translateY(-2px); border-color: transparent; }
    .feat-card:hover::before { opacity:1; }
    .feat-icon { width: 50px; height: 50px; border-radius: 14px; background: var(--accent-light); display: flex; align-items: center; justify-content: center; margin-bottom: 18px; font-size: 24px; }
    .feat-card .h4 { margin-bottom: 8px; }

    /* ─── SELLERS DARK ─── */
    .sell-sec { background: var(--brand); padding: 100px 0; position: relative; overflow: hidden; }
    .sell-sec::after { content:''; position:absolute; bottom:-120px; right:-120px; width:420px; height:420px; background:radial-gradient(circle,rgba(255,77,79,0.14) 0%,transparent 70%); border-radius:50%; pointer-events:none; }
    .sell-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
    .sell-content .lead { color: rgba(255,255,255,0.6); margin: 18px 0 40px; }
    .sell-content .h2 { color: #fff; }
    .sell-benefits { list-style: none; display: flex; flex-direction: column; gap: 15px; }
    .sell-benefits li { display: flex; align-items: flex-start; gap: 13px; font-size: 15px; color: rgba(255,255,255,0.78); font-weight: 500; }
    .chk { width: 22px; height: 22px; min-width: 22px; border-radius: 50%; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; margin-top: 1px; }
    .sell-mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .sell-mini { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09); border-radius: var(--radius); padding: 26px 22px; }
    .sell-mini-num { font-size: 36px; font-weight: 800; color: #fff; line-height: 1; margin-bottom: 4px; }
    .sell-mini-num em { color: var(--accent); font-style: normal; }
    .sell-mini-lbl { font-size: 13px; color: rgba(255,255,255,0.45); }

    /* ─── CLIENTS ─── */
    .cli-sec { padding: 100px 0; background: var(--bg); }
    .cli-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
    .cli-steps { display: flex; flex-direction: column; gap: 16px; }
    .cli-step { display: flex; gap: 16px; padding: 20px; background: #fff; border-radius: var(--radius); border: 1px solid var(--border); transition: all var(--ease); }
    .cli-step:hover { box-shadow: var(--shadow); border-color: var(--accent); }
    .cli-step-n { width: 36px; height: 36px; min-width: 36px; border-radius: 10px; background: var(--accent); color: #fff; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 15px; }
    .cli-step-n.dark { background: var(--brand); }
    .cli-step-body .h4 { margin-bottom: 4px; }

    /* ─── ADMIN ─── */
    .admin-sec { padding: 100px 0; }
    .admin-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
    .admin-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .admin-card { background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px 20px; }
    .admin-card-icon { font-size: 28px; margin-bottom: 10px; }
    .admin-card .h4 { margin-bottom: 5px; font-size: 15px; }

    /* ─── FOOTER ─── */
    footer { background: var(--brand); color: #fff; padding: 64px 0 32px; }
    .foot-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 56px; padding-bottom: 48px; border-bottom: 1px solid rgba(255,255,255,0.07); }
    .foot-logo { display: flex; align-items: center; gap: 10px; font-size: 21px; font-weight: 800; margin-bottom: 14px; }
    .foot-logo-box { width: 36px; height: 36px; background: var(--accent); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .foot-desc { font-size: 14px; color: rgba(255,255,255,0.45); line-height: 1.75; max-width: 280px; }
    .foot-col-title { font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 14px; }
    .foot-links { list-style: none; display: flex; flex-direction: column; gap: 9px; }
    .foot-links a { font-size: 14px; color: rgba(255,255,255,0.55); transition: color var(--ease); }
    .foot-links a:hover { color: #fff; }
    .foot-bottom { display: flex; align-items: center; justify-content: space-between; margin-top: 30px; font-size: 13px; color: rgba(255,255,255,0.3); }

    /* ─── REVEAL ─── */
    .reveal { opacity: 0; transform: translateY(26px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .reveal.visible { opacity: 1; transform: none; }
    .d1 { transition-delay: 0.1s; }
    .d2 { transition-delay: 0.2s; }
    .d3 { transition-delay: 0.3s; }
    .d4 { transition-delay: 0.4s; }
    .d5 { transition-delay: 0.5s; }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 1024px) {
        .hero-inner,.sell-inner,.cli-inner,.admin-inner { grid-template-columns: 1fr; gap: 48px; }
        .hero-visual { height: 380px; }
        .phone { width: 210px; height: 420px; }
        .sell-mini-grid { grid-template-columns: repeat(4,1fr); }
        .foot-grid { grid-template-columns: 1fr 1fr; }
        .foot-brand-col { grid-column: 1 / -1; }
    }
    @media (max-width: 768px) {
        .section,.sell-sec,.cli-sec,.admin-sec { padding: 64px 0; }
        .nav-links { display: none; }
        .hamburger { display: flex; }
        .hero-visual { display: none; }
        .strip-grid { grid-template-columns: repeat(2,1fr); gap: 32px; }
        .strip-item::after { display: none !important; }
        .roles-grid,.feat-grid { grid-template-columns: 1fr; }
        .how-panel.active { grid-template-columns: 1fr; }
        .how-panel.active .step::after { display: none; }
        .sell-mini-grid { grid-template-columns: 1fr 1fr; }
        .admin-cards { grid-template-columns: 1fr; }
        .foot-grid { grid-template-columns: 1fr; gap: 32px; }
        .foot-bottom { flex-direction: column; gap: 8px; text-align: center; }
        .hero-actions { flex-direction: column; }
        .hero-actions .btn { width: 100%; justify-content: center; }
    }
    @media (max-width: 480px) {
        .strip-grid { grid-template-columns: 1fr 1fr; }
        .how-tabs { flex-direction: column; width: 100%; }
    }
    </style>
</head>
<body>

{{-- ═══ NAVBAR ═══ --}}
<nav class="nav" id="nav">
    <div class="wrap">
        <a href="#" class="nav-logo">
            <div class="nav-logo-box"><span>C</span></div>
            CoreShop
        </a>
        <ul class="nav-links">
            <li><a href="#about"><span class="e">About</span><span class="a" style="display:none">من نحن</span></a></li>
            <li><a href="#how"><span class="e">How It Works</span><span class="a" style="display:none">كيف يعمل</span></a></li>
            <li><a href="#features"><span class="e">Features</span><span class="a" style="display:none">المميزات</span></a></li>
            <li><a href="#sellers"><span class="e">For Sellers</span><span class="a" style="display:none">للبائعين</span></a></li>
            <li><a href="#clients"><span class="e">For Clients</span><span class="a" style="display:none">للعملاء</span></a></li>
        </ul>
        <div class="nav-right">
            <div class="lang-toggle">
                <button class="lang-btn active" id="btn-en" onclick="setLang('en')">EN</button>
                <button class="lang-btn" id="btn-ar" onclick="setLang('ar')">ع</button>
            </div>
            <div class="hamburger" id="hbg" onclick="toggleNav()">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
</nav>

<div class="mobile-nav" id="mob-nav">
    <a href="#about" onclick="toggleNav()"><span class="e">About</span><span class="a" style="display:none">من نحن</span></a>
    <a href="#how" onclick="toggleNav()"><span class="e">How It Works</span><span class="a" style="display:none">كيف يعمل</span></a>
    <a href="#features" onclick="toggleNav()"><span class="e">Features</span><span class="a" style="display:none">المميزات</span></a>
    <a href="#sellers" onclick="toggleNav()"><span class="e">For Sellers</span><span class="a" style="display:none">للبائعين</span></a>
    <a href="#clients" onclick="toggleNav()"><span class="e">For Clients</span><span class="a" style="display:none">للعملاء</span></a>
</div>

{{-- ═══ HERO ═══ --}}
<section class="hero" id="home">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="hero-eyebrow">
                    <div class="badge"><span class="badge-dot"></span><span class="e">Built for the Jordanian Market</span><span class="a" style="display:none">مبني للسوق الأردني</span></div>
                </div>
                <h1 class="h1 hero-title">
                    <span class="e">The Smart Marketplace<br>Connecting <em style="color:var(--accent);font-style:normal">Sellers</em> &amp; Buyers</span>
                    <span class="a" style="display:none">المنصة الذكية<br>تربط <em style="color:var(--accent);font-style:normal">البائعين</em> والمشترين</span>
                </h1>
                <p class="lead hero-sub">
                    <span class="e">CoreShop brings sellers, clients, and intelligent order management together in one seamless platform — fully bilingual, real-time, and built to scale.</span>
                    <span class="a" style="display:none">كورشوب يجمع البائعين والعملاء وإدارة الطلبات الذكية في منصة واحدة متكاملة — ثنائية اللغة، في الوقت الفعلي، ومبنية للنمو.</span>
                </p>
                <div class="hero-actions">
                    <a href="#sellers" class="btn btn-dark"><span class="e">Start Selling →</span><span class="a" style="display:none">ابدأ البيع ←</span></a>
                    <a href="#how" class="btn btn-ghost"><span class="e">How It Works</span><span class="a" style="display:none">كيف يعمل</span></a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="fc fc-1 reveal">
                    <div class="fc-icon red">🛍️</div>
                    <div><div class="fc-p"><span class="e">New Order!</span><span class="a" style="display:none">طلب جديد!</span></div><div class="fc-s"><span class="e">JOD 48.00 · 3 items</span><span class="a" style="display:none">٤٨ د.أ · ٣ منتجات</span></div></div>
                </div>
                <div class="phone">
                    <div class="phone-notch"></div>
                    <div class="phone-screen">
                        <div class="ph-bar">
                            <div class="ph-avatar">🏪</div>
                            <div class="ph-lines"><div class="ph-line dark"></div><div class="ph-line short"></div></div>
                        </div>
                        <div class="ph-grid">
                            <div class="ph-card"><div class="ph-card-img c1">👗</div><div class="ph-card-body"><div class="ph-line dark"></div><div class="ph-line" style="background:var(--accent);opacity:.45;width:40%"></div></div></div>
                            <div class="ph-card"><div class="ph-card-img c2">👟</div><div class="ph-card-body"><div class="ph-line dark"></div><div class="ph-line" style="background:var(--accent);opacity:.45;width:38%"></div></div></div>
                        </div>
                        <div class="ph-wide">
                            <div class="ph-wide-img">🧥</div>
                            <div class="ph-lines"><div class="ph-line dark"></div><div class="ph-line short" style="background:var(--accent);opacity:.4"></div></div>
                        </div>
                        <div class="ph-grid">
                            <div class="ph-card"><div class="ph-card-img c3">👜</div><div class="ph-card-body"><div class="ph-line dark"></div><div class="ph-line" style="background:var(--accent);opacity:.45;width:42%"></div></div></div>
                            <div class="ph-card" style="background:var(--brand);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:4px;padding:10px">
                                <div style="font-size:20px">📊</div>
                                <div style="height:4px;width:32px;border-radius:2px;background:var(--accent);opacity:.7"></div>
                                <div style="height:4px;width:22px;border-radius:2px;background:rgba(255,255,255,.2)"></div>
                            </div>
                        </div>
                        <div class="ph-stat-bar">
                            <div class="ph-stat"><div class="ph-stat-n"></div><div class="ph-stat-l"></div></div>
                            <div class="ph-stat"><div class="ph-stat-n" style="width:20px"></div><div class="ph-stat-l"></div></div>
                            <div class="ph-stat"><div class="ph-stat-n" style="width:22px"></div><div class="ph-stat-l"></div></div>
                        </div>
                    </div>
                </div>
                <div class="fc fc-2 reveal d2">
                    <div class="fc-icon blue">💬</div>
                    <div><div class="fc-p"><span class="e">Live Chat</span><span class="a" style="display:none">دردشة مباشرة</span></div><div class="fc-s"><span class="e">Client asked about size</span><span class="a" style="display:none">سأل عن المقاس</span></div></div>
                </div>
                <div class="fc fc-3 reveal d1">
                    <div class="fc-icon green">✅</div>
                    <div><div class="fc-p"><span class="e">Order Delivered</span><span class="a" style="display:none">تم التوصيل</span></div><div class="fc-s"><span class="e">Order #2041 completed</span><span class="a" style="display:none">الطلب #٢٠٤١ مكتمل</span></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ STATS STRIP ═══ --}}
<div class="strip">
    <div class="wrap">
        <div class="strip-grid">
            <div class="strip-item reveal"><div class="strip-num">3<em>+</em></div><div class="strip-lbl"><span class="e">Platform Roles</span><span class="a" style="display:none">أدوار المنصة</span></div></div>
            <div class="strip-item reveal d1"><div class="strip-num">10<em>+</em></div><div class="strip-lbl"><span class="e">Order Statuses</span><span class="a" style="display:none">حالات الطلب</span></div></div>
            <div class="strip-item reveal d2"><div class="strip-num">2</div><div class="strip-lbl"><span class="e">Languages — AR &amp; EN</span><span class="a" style="display:none">لغتان — عربي وإنجليزي</span></div></div>
            <div class="strip-item reveal d3"><div class="strip-num"><em>∞</em></div><div class="strip-lbl"><span class="e">Growth Potential</span><span class="a" style="display:none">إمكانية النمو</span></div></div>
        </div>
    </div>
</div>

{{-- ═══ ABOUT ═══ --}}
<section class="section" id="about">
    <div class="wrap">
        <div class="sec-head reveal">
            <div class="eyebrow"><span class="e">About CoreShop</span><span class="a" style="display:none">عن كورشوب</span></div>
            <h2 class="h2" style="margin-top:14px"><span class="e">A complete ecosystem for modern commerce</span><span class="a" style="display:none">منظومة متكاملة للتجارة الحديثة</span></h2>
            <p class="lead"><span class="e">CoreShop is a three-sided marketplace platform designed to connect sellers, clients, and administrators in one powerful system. From store creation to order delivery, every step is managed inside a single mobile app and web dashboard.</span><span class="a" style="display:none">كورشوب هو منصة سوق ثلاثية الأطراف مصممة لربط البائعين والعملاء والمشرفين في نظام واحد متكامل. من إنشاء المتجر إلى توصيل الطلب، كل خطوة تُدار داخل تطبيق موبايل واحد ولوحة تحكم ويب.</span></p>
        </div>
        <div class="roles-grid">
            <div class="role-card blue reveal d1">
                <div class="role-icon blue">🛒</div>
                <h4 class="h4"><span class="e">For Clients</span><span class="a" style="display:none">للعملاء</span></h4>
                <p class="body"><span class="e">Browse hundreds of products from local stores. Add to cart, choose your address, and track every update in real time.</span><span class="a" style="display:none">تصفح مئات المنتجات من المتاجر المحلية. أضف للسلة، اختر عنوانك، وتابع كل تحديث في الوقت الفعلي.</span></p>
            </div>
            <div class="role-card red reveal d2">
                <div class="role-icon red">🏪</div>
                <h4 class="h4"><span class="e">For Sellers</span><span class="a" style="display:none">للبائعين</span></h4>
                <p class="body"><span class="e">Set up your store in minutes. Manage products, incoming orders, customer chat, and analytics — all from your phone.</span><span class="a" style="display:none">أنشئ متجرك في دقائق. أدر المنتجات والطلبات والدردشة مع العملاء والتحليلات — كل ذلك من هاتفك.</span></p>
            </div>
            <div class="role-card green reveal d3">
                <div class="role-icon green">⚙️</div>
                <h4 class="h4"><span class="e">For Admins</span><span class="a" style="display:none">للمشرفين</span></h4>
                <p class="body"><span class="e">Full platform control from a powerful web dashboard. Approve stores, manage orders, users, analytics, and platform fees.</span><span class="a" style="display:none">تحكم كامل بالمنصة من لوحة تحكم ويب قوية. اعتمد المتاجر وأدر الطلبات والمستخدمين والتحليلات والرسوم.</span></p>
            </div>
        </div>
    </div>
</section>

{{-- ═══ HOW IT WORKS ═══ --}}
<section class="section section-gray" id="how">
    <div class="wrap">
        <div class="sec-head reveal">
            <div class="eyebrow"><span class="e">How It Works</span><span class="a" style="display:none">كيف يعمل</span></div>
            <h2 class="h2" style="margin-top:14px"><span class="e">Simple for everyone involved</span><span class="a" style="display:none">بسيط للجميع</span></h2>
            <p class="lead"><span class="e">Whether you're a buyer or a seller, CoreShop gives you a clear and guided experience from start to finish.</span><span class="a" style="display:none">سواء كنت مشتريًا أو بائعًا، يمنحك كورشوب تجربة واضحة وموجَّهة من البداية إلى النهاية.</span></p>
        </div>
        <div class="how-tabs">
            <button class="how-tab active" id="tc" onclick="tab('c')"><span class="e">🛒 For Clients</span><span class="a" style="display:none">🛒 للعملاء</span></button>
            <button class="how-tab" id="ts" onclick="tab('s')"><span class="e">🏪 For Sellers</span><span class="a" style="display:none">🏪 للبائعين</span></button>
        </div>
        <div class="how-panel active" id="pc">
            <div class="step reveal"><div class="step-emoji">🔍</div><div class="step-num">1</div><h4 class="h4"><span class="e">Browse &amp; Discover</span><span class="a" style="display:none">تصفح واكتشف</span></h4><p class="body"><span class="e">Explore stores, categories, flash deals, and featured products. Search and filter to find exactly what you need.</span><span class="a" style="display:none">استكشف المتاجر والفئات والعروض والمنتجات المميزة. ابحث وصفّ للعثور على ما تحتاجه.</span></p></div>
            <div class="step reveal d2"><div class="step-emoji">📦</div><div class="step-num">2</div><h4 class="h4"><span class="e">Add to Cart &amp; Order</span><span class="a" style="display:none">أضف وأطلب</span></h4><p class="body"><span class="e">Pick your variants, add to cart, choose your saved address, apply a coupon, and place your order with one tap.</span><span class="a" style="display:none">اختر المتغيرات، أضف للسلة، اختر عنوانك، طبّق كوبون، وضع طلبك بنقرة واحدة.</span></p></div>
            <div class="step reveal d4"><div class="step-emoji">🚚</div><div class="step-num">3</div><h4 class="h4"><span class="e">Track &amp; Receive</span><span class="a" style="display:none">تتبع واستلم</span></h4><p class="body"><span class="e">Follow your order through live status updates. Get push notifications at every stage and chat with the seller anytime.</span><span class="a" style="display:none">تابع طلبك عبر تحديثات الحالة المباشرة. احصل على إشعارات فورية في كل مرحلة وتواصل مع البائع في أي وقت.</span></p></div>
        </div>
        <div class="how-panel" id="ps">
            <div class="step reveal"><div class="step-emoji">🏪</div><div class="step-num">1</div><h4 class="h4"><span class="e">Create Your Store</span><span class="a" style="display:none">أنشئ متجرك</span></h4><p class="body"><span class="e">Set up your store with a logo, banner, GPS location, and working hours — all from the mobile app in minutes.</span><span class="a" style="display:none">أنشئ متجرك بالشعار والغلاف وموقع GPS وأوقات العمل — كل ذلك من التطبيق في دقائق.</span></p></div>
            <div class="step reveal d2"><div class="step-emoji">🏷️</div><div class="step-num">2</div><h4 class="h4"><span class="e">List Your Products</span><span class="a" style="display:none">أضف منتجاتك</span></h4><p class="body"><span class="e">Upload up to 7 images per product, add size and color variants, set stock levels, and publish for review.</span><span class="a" style="display:none">ارفع حتى 7 صور لكل منتج، أضف متغيرات المقاس واللون، حدد المخزون، وانشر للمراجعة.</span></p></div>
            <div class="step reveal d4"><div class="step-emoji">📊</div><div class="step-num">3</div><h4 class="h4"><span class="e">Manage &amp; Grow</span><span class="a" style="display:none">أدر ونمِّ</span></h4><p class="body"><span class="e">Accept orders, update statuses, chat with customers, and monitor revenue, top products, and growth analytics.</span><span class="a" style="display:none">اقبل الطلبات، حدّث الحالات، تواصل مع العملاء، وراقب الإيرادات وأفضل المنتجات ونمو الأعمال.</span></p></div>
        </div>
    </div>
</section>

{{-- ═══ FEATURES ═══ --}}
<section class="section" id="features">
    <div class="wrap">
        <div class="sec-head reveal">
            <div class="eyebrow"><span class="e">Platform Features</span><span class="a" style="display:none">مميزات المنصة</span></div>
            <h2 class="h2" style="margin-top:14px"><span class="e">Everything you need, built in</span><span class="a" style="display:none">كل ما تحتاجه، مدمج</span></h2>
            <p class="lead"><span class="e">CoreShop ships with every feature a marketplace needs — no third-party apps required.</span><span class="a" style="display:none">يأتي كورشوب مع جميع ميزات السوق الإلكتروني جاهزة — لا حاجة لتطبيقات خارجية.</span></p>
        </div>
        <div class="feat-grid">
            <div class="feat-card reveal"><div class="feat-icon">💬</div><h4 class="h4"><span class="e">Real-time Chat</span><span class="a" style="display:none">دردشة فورية</span></h4><p class="body"><span class="e">Clients and sellers communicate directly in-app. Share product cards and order details inside conversations.</span><span class="a" style="display:none">يتواصل العملاء والبائعون مباشرة داخل التطبيق. شارك بطاقات المنتجات وتفاصيل الطلبات في المحادثات.</span></p></div>
            <div class="feat-card reveal d1"><div class="feat-icon">🔔</div><h4 class="h4"><span class="e">Push Notifications</span><span class="a" style="display:none">إشعارات فورية</span></h4><p class="body"><span class="e">Instant heads-up notifications for every order status change and new messages — even when the app is closed.</span><span class="a" style="display:none">إشعارات لحظية لكل تغيير في حالة الطلب والرسائل الجديدة — حتى عند إغلاق التطبيق.</span></p></div>
            <div class="feat-card reveal d2"><div class="feat-icon">📍</div><h4 class="h4"><span class="e">GPS Address Detection</span><span class="a" style="display:none">تحديد العنوان بـ GPS</span></h4><p class="body"><span class="e">Clients pin their exact delivery location on an interactive map. Reverse geocoding fills the address automatically.</span><span class="a" style="display:none">يثبّت العملاء موقع التوصيل الدقيق على خريطة تفاعلية. الترميز الجغرافي العكسي يملأ العنوان تلقائيًا.</span></p></div>
            <div class="feat-card reveal d1"><div class="feat-icon">🌐</div><h4 class="h4"><span class="e">Full Bilingual RTL</span><span class="a" style="display:none">ثنائي اللغة RTL</span></h4><p class="body"><span class="e">Arabic and English with complete RTL layout support. Fonts, direction, and all UI elements switch seamlessly.</span><span class="a" style="display:none">عربي وإنجليزي مع دعم كامل لتخطيط RTL. الخطوط والاتجاه وجميع عناصر الواجهة تتبدل بسلاسة.</span></p></div>
            <div class="feat-card reveal d2"><div class="feat-icon">📈</div><h4 class="h4"><span class="e">Seller Analytics</span><span class="a" style="display:none">تحليلات البائع</span></h4><p class="body"><span class="e">Revenue charts, top-selling products, order trends, and customer counts — visible to sellers inside the app.</span><span class="a" style="display:none">مخططات الإيرادات والمنتجات الأكثر مبيعًا واتجاهات الطلبات — مرئية للبائعين في التطبيق.</span></p></div>
            <div class="feat-card reveal d3"><div class="feat-icon">🌙</div><h4 class="h4"><span class="e">Dark Mode</span><span class="a" style="display:none">الوضع الليلي</span></h4><p class="body"><span class="e">Full dark mode across every screen. Follows system preference automatically or can be set manually by the user.</span><span class="a" style="display:none">وضع ليلي كامل في كل شاشة. يتبع تفضيلات النظام تلقائيًا أو يضبطه المستخدم يدويًا.</span></p></div>
        </div>
    </div>
</section>

{{-- ═══ SELLERS ═══ --}}
<section class="sell-sec" id="sellers">
    <div class="wrap">
        <div class="sell-inner">
            <div class="sell-content">
                <div class="eyebrow" style="color:var(--accent);margin-bottom:18px"><span class="e">For Sellers</span><span class="a" style="display:none">للبائعين</span></div>
                <h2 class="h2 reveal"><span class="e">Your store. Your rules. In your pocket.</span><span class="a" style="display:none">متجرك. قواعدك. في جيبك.</span></h2>
                <p class="lead sell-content reveal d1"><span class="e">CoreShop gives sellers a complete business toolkit inside a mobile app — from the first product listing to monthly revenue reports.</span><span class="a" style="display:none">يمنح كورشوب البائعين مجموعة أدوات أعمال متكاملة داخل تطبيق موبايل — من قائمة المنتج الأولى إلى تقارير الإيرادات الشهرية.</span></p>
                <ul class="sell-benefits reveal d2">
                    <li><div class="chk"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="#FF4D4F" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div><span class="e">Store setup wizard with logo, banner, and GPS location</span><span class="a" style="display:none">معالج إعداد المتجر مع الشعار والغلاف وموقع GPS</span></li>
                    <li><div class="chk"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="#FF4D4F" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div><span class="e">Product catalog with up to 7 images, variants, and stock control</span><span class="a" style="display:none">كتالوج المنتجات مع ما يصل إلى 7 صور ومتغيرات والتحكم في المخزون</span></li>
                    <li><div class="chk"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="#FF4D4F" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div><span class="e">Order inbox with status management and push notifications</span><span class="a" style="display:none">صندوق الطلبات مع إدارة الحالات والإشعارات الفورية</span></li>
                    <li><div class="chk"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="#FF4D4F" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div><span class="e">Direct chat with clients — share product cards and orders</span><span class="a" style="display:none">دردشة مباشرة مع العملاء — شارك بطاقات المنتجات والطلبات</span></li>
                    <li><div class="chk"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="#FF4D4F" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div><span class="e">Revenue, orders, and top-products analytics dashboard</span><span class="a" style="display:none">لوحة تحليلات الإيرادات والطلبات وأفضل المنتجات</span></li>
                    <li><div class="chk"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="#FF4D4F" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div><span class="e">Open/close toggle — manage your availability anytime</span><span class="a" style="display:none">تبديل الفتح/الإغلاق — أدر توفرك في أي وقت</span></li>
                </ul>
                <div style="margin-top:40px" class="reveal d3">
                    <a href="#" class="btn btn-red"><span class="e">Become a Seller →</span><span class="a" style="display:none">كن بائعًا ←</span></a>
                </div>
            </div>
            <div class="sell-mini-grid reveal d1">
                <div class="sell-mini"><div class="sell-mini-num">7</div><div class="sell-mini-lbl"><span class="e">Product images per listing</span><span class="a" style="display:none">صور لكل منتج</span></div></div>
                <div class="sell-mini"><div class="sell-mini-num">10<em>+</em></div><div class="sell-mini-lbl"><span class="e">Order lifecycle stages</span><span class="a" style="display:none">مراحل دورة الطلب</span></div></div>
                <div class="sell-mini"><div class="sell-mini-num">3</div><div class="sell-mini-lbl"><span class="e">Analytics chart types</span><span class="a" style="display:none">أنواع مخططات التحليل</span></div></div>
                <div class="sell-mini"><div class="sell-mini-num"><em>∞</em></div><div class="sell-mini-lbl"><span class="e">Products you can list</span><span class="a" style="display:none">منتجات يمكنك إضافتها</span></div></div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ CLIENTS ═══ --}}
<section class="cli-sec" id="clients">
    <div class="wrap">
        <div class="cli-inner">
            <div class="reveal">
                <div class="eyebrow" style="margin-bottom:18px"><span class="e">For Clients</span><span class="a" style="display:none">للعملاء</span></div>
                <h2 class="h2" style="margin-bottom:18px"><span class="e">Shop local, delivered to your door</span><span class="a" style="display:none">تسوق محليًا، يصلك لبابك</span></h2>
                <p class="lead" style="margin-bottom:40px"><span class="e">CoreShop makes shopping from local Jordanian stores effortless — with real-time order tracking, saved addresses, and instant seller chat.</span><span class="a" style="display:none">يجعل كورشوب التسوق من المتاجر الأردنية المحلية أمرًا سهلًا — مع تتبع الطلبات الفوري والعناوين المحفوظة والدردشة الفورية مع البائع.</span></p>
                <a href="#" class="btn btn-dark"><span class="e">Start Shopping →</span><span class="a" style="display:none">ابدأ التسوق ←</span></a>
            </div>
            <div class="cli-steps">
                <div class="cli-step reveal d1"><div class="cli-step-n">1</div><div class="cli-step-body"><h4 class="h4"><span class="e">Discover local stores</span><span class="a" style="display:none">اكتشف المتاجر المحلية</span></h4><p class="body"><span class="e">Browse by category, search by name, or explore curated deals and top stores on the home feed.</span><span class="a" style="display:none">تصفح حسب الفئة أو ابحث بالاسم أو استكشف العروض المنتقاة وأفضل المتاجر.</span></p></div></div>
                <div class="cli-step reveal d2"><div class="cli-step-n">2</div><div class="cli-step-body"><h4 class="h4"><span class="e">Pin your location on the map</span><span class="a" style="display:none">ثبّت موقعك على الخريطة</span></h4><p class="body"><span class="e">Use GPS auto-detect or drag the pin to your exact spot. Save multiple addresses for future orders.</span><span class="a" style="display:none">استخدم الكشف التلقائي أو اسحب الدبوس إلى موقعك الدقيق. احفظ عناوين متعددة للطلبات المستقبلية.</span></p></div></div>
                <div class="cli-step reveal d3"><div class="cli-step-n dark">3</div><div class="cli-step-body"><h4 class="h4"><span class="e">Order, track, and chat</span><span class="a" style="display:none">اطلب وتتبع وتواصل</span></h4><p class="body"><span class="e">Place your order with Cash on Delivery or CliQ. Get push alerts on every update, and message the seller directly.</span><span class="a" style="display:none">ضع طلبك بالدفع عند الاستلام أو CliQ. احصل على تنبيهات فورية، وراسل البائع مباشرة.</span></p></div></div>
                <div class="cli-step reveal d4"><div class="cli-step-n dark">4</div><div class="cli-step-body"><h4 class="h4"><span class="e">Rate your experience</span><span class="a" style="display:none">قيّم تجربتك</span></h4><p class="body"><span class="e">After delivery, leave a review for the product and store. Your feedback helps the whole community shop smarter.</span><span class="a" style="display:none">بعد التوصيل، اترك تقييمًا للمنتج والمتجر. ملاحظاتك تساعد المجتمع على التسوق بشكل أذكى.</span></p></div></div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ ADMIN SECTION ═══ --}}
<section class="admin-sec">
    <div class="wrap">
        <div class="admin-inner">
            <div>
                <div class="eyebrow" style="margin-bottom:18px"><span class="e">Admin Dashboard</span><span class="a" style="display:none">لوحة التحكم</span></div>
                <h2 class="h2 reveal" style="margin-bottom:18px"><span class="e">Total control from one web dashboard</span><span class="a" style="display:none">تحكم كامل من لوحة ويب واحدة</span></h2>
                <p class="lead reveal d1" style="margin-bottom:16px"><span class="e">Admins manage the entire platform from a powerful React-based web dashboard — approvals, analytics, users, coupons, banners, and more.</span><span class="a" style="display:none">يدير المشرفون المنصة بأكملها من لوحة تحكم ويب مبنية بـ React — الموافقات والتحليلات والمستخدمون والكوبونات واللافتات والمزيد.</span></p>
            </div>
            <div class="admin-cards">
                <div class="admin-card reveal"><div class="admin-card-icon">🏪</div><h4 class="h4"><span class="e">Store Approvals</span><span class="a" style="display:none">موافقات المتاجر</span></h4><p class="body" style="font-size:13px"><span class="e">Review and approve seller stores before they go live.</span><span class="a" style="display:none">راجع واعتمد متاجر البائعين قبل إطلاقها.</span></p></div>
                <div class="admin-card reveal d1"><div class="admin-card-icon">📦</div><h4 class="h4"><span class="e">Order Management</span><span class="a" style="display:none">إدارة الطلبات</span></h4><p class="body" style="font-size:13px"><span class="e">Oversee all platform orders and update statuses.</span><span class="a" style="display:none">أشرف على جميع طلبات المنصة وحدّث حالاتها.</span></p></div>
                <div class="admin-card reveal d2"><div class="admin-card-icon">📊</div><h4 class="h4"><span class="e">Platform Analytics</span><span class="a" style="display:none">تحليلات المنصة</span></h4><p class="body" style="font-size:13px"><span class="e">6 live charts — revenue, users, orders, top sellers.</span><span class="a" style="display:none">٦ مخططات مباشرة — الإيرادات والمستخدمون والطلبات وأفضل البائعين.</span></p></div>
                <div class="admin-card reveal d3"><div class="admin-card-icon">🎟️</div><h4 class="h4"><span class="e">Coupons &amp; Banners</span><span class="a" style="display:none">الكوبونات واللافتات</span></h4><p class="body" style="font-size:13px"><span class="e">Create discount codes and manage home screen banners.</span><span class="a" style="display:none">أنشئ رموز الخصم وأدر لافتات الشاشة الرئيسية.</span></p></div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ FOOTER ═══ --}}
<footer>
    <div class="wrap">
        <div class="foot-grid">
            <div class="foot-brand-col">
                <div class="foot-logo">
                    <div class="foot-logo-box">
                        <svg width="18" height="18" fill="white" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0"/></svg>
                    </div>
                    CoreShop
                </div>
                <p class="foot-desc"><span class="e">The complete marketplace platform connecting sellers, clients, and administrators — built for the Jordanian market.</span><span class="a" style="display:none">منصة السوق الإلكتروني المتكاملة التي تربط البائعين والعملاء والمشرفين — مبنية للسوق الأردني.</span></p>
            </div>
            <div>
                <div class="foot-col-title"><span class="e">Platform</span><span class="a" style="display:none">المنصة</span></div>
                <ul class="foot-links">
                    <li><a href="#about"><span class="e">About</span><span class="a" style="display:none">من نحن</span></a></li>
                    <li><a href="#how"><span class="e">How It Works</span><span class="a" style="display:none">كيف يعمل</span></a></li>
                    <li><a href="#features"><span class="e">Features</span><span class="a" style="display:none">المميزات</span></a></li>
                    <li><a href="#sellers"><span class="e">For Sellers</span><span class="a" style="display:none">للبائعين</span></a></li>
                    <li><a href="#clients"><span class="e">For Clients</span><span class="a" style="display:none">للعملاء</span></a></li>
                </ul>
            </div>
            <div>
                <div class="foot-col-title"><span class="e">Tech Stack</span><span class="a" style="display:none">التقنيات</span></div>
                <ul class="foot-links">
                    <li><a>Laravel 13</a></li>
                    <li><a>Expo / React Native</a></li>
                    <li><a>React + Vite</a></li>
                    <li><a>Mapbox</a></li>
                    <li><a>Expo Push API</a></li>
                </ul>
            </div>
        </div>
        <div class="foot-bottom">
            <span>© 2026 CoreShop. <span class="e">All rights reserved.</span><span class="a" style="display:none">جميع الحقوق محفوظة.</span></span>
            <span><span class="e">Made with ❤️ for Jordan</span><span class="a" style="display:none">صُنع بـ ❤️ للأردن</span></span>
        </div>
    </div>
</footer>

<script>
// ─── Language ───────────────────────────────
function setLang(l) {
    const html = document.getElementById('html-root');
    html.lang = l; html.dir = l==='ar'?'rtl':'ltr';
    html.style.fontFamily = l==='ar'?"'IBM Plex Sans Arabic',sans-serif":"'Manrope',sans-serif";
    document.querySelectorAll('.e').forEach(el => el.style.display = l==='en'?'':'none');
    document.querySelectorAll('.a').forEach(el => el.style.display = l==='ar'?'':'none');
    document.getElementById('btn-en').classList.toggle('active',l==='en');
    document.getElementById('btn-ar').classList.toggle('active',l==='ar');
    localStorage.setItem('cslang',l);
}
const sl = localStorage.getItem('cslang');
if(sl && sl!=='en') setLang(sl);

// ─── How tabs ───────────────────────────────
function tab(t) {
    document.getElementById('tc').classList.toggle('active',t==='c');
    document.getElementById('ts').classList.toggle('active',t==='s');
    document.getElementById('pc').classList.toggle('active',t==='c');
    document.getElementById('ps').classList.toggle('active',t==='s');
}

// ─── Reveal on scroll ───────────────────────
const obs = new IntersectionObserver(entries=>{
    entries.forEach(e=>{ if(e.isIntersecting) e.target.classList.add('visible'); });
},{threshold:0.1});
document.querySelectorAll('.reveal').forEach(el=>obs.observe(el));

// ─── Navbar shadow ──────────────────────────
window.addEventListener('scroll',()=>{
    document.getElementById('nav').style.boxShadow = window.scrollY>10?'0 2px 20px rgba(0,0,0,0.08)':'none';
});

// ─── Mobile nav ─────────────────────────────
function toggleNav(){ document.getElementById('mob-nav').classList.toggle('open'); }
</script>
</body>
</html>
