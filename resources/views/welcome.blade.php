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

    .wrap { max-width: var(--max); margin: 0 auto; padding: 0 24px; }

    .h1 { font-size: clamp(36px, 5.5vw, 66px); font-weight: 800; line-height: 1.08; letter-spacing: -0.025em; }
    .h2 { font-size: clamp(28px, 4vw, 50px); font-weight: 800; line-height: 1.12; letter-spacing: -0.02em; }
    .h3 { font-size: clamp(20px, 2.5vw, 28px); font-weight: 700; line-height: 1.3; }
    .h4 { font-size: 17px; font-weight: 700; line-height: 1.4; }
    .lead { font-size: clamp(16px, 1.8vw, 19px); line-height: 1.75; color: var(--secondary); }
    .body { font-size: 15px; line-height: 1.75; color: var(--secondary); }
    .eyebrow { font-size: 12px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--accent); }

    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all var(--ease); border: none; font-family: inherit; white-space: nowrap; }
    .btn-dark { background: var(--brand); color: #fff; }
    .btn-dark:hover { background: #222; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,0.18); }
    .btn-red { background: var(--accent); color: #fff; }
    .btn-red:hover { background: #e63e40; transform: translateY(-1px); box-shadow: 0 8px 28px rgba(255,77,79,0.35); }
    .btn-ghost { background: transparent; color: var(--brand); border: 2px solid var(--border); }
    .btn-ghost:hover { border-color: var(--brand); background: var(--bg); }

    .badge { display: inline-flex; align-items: center; gap: 7px; padding: 7px 16px; background: var(--accent-light); color: var(--accent); border-radius: 100px; font-size: 13px; font-weight: 600; }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent); flex-shrink: 0; }

    .section { padding: 100px 0; }
    .section-gray { background: var(--bg); }
    .sec-head { text-align: center; max-width: 660px; margin: 0 auto 68px; }
    .sec-head .lead { margin-top: 16px; }

    /* ─── NAVBAR ─── */
    .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 200; height: 70px; display: flex; align-items: center; background: rgba(255,255,255,0.9); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid rgba(0,0,0,0.06); transition: box-shadow var(--ease); }
    .nav .wrap { display: flex; align-items: center; justify-content: space-between; width: 100%; }
    .nav-logo { display: flex; align-items: center; gap: 10px; font-size: 21px; font-weight: 800; letter-spacing: -0.02em; }
    .nav-logo-box img { height: 38px; width: 38px; object-fit: contain; display: block; }
    .nav-links { display: flex; align-items: center; gap: 32px; list-style: none; }
    .nav-links a { font-size: 14px; font-weight: 600; color: var(--secondary); transition: color var(--ease); }
    .nav-links a:hover { color: var(--brand); }
    .nav-right { display: flex; align-items: center; gap: 12px; }
    .lang-toggle { display: flex; align-items: center; background: var(--bg); border-radius: 10px; padding: 3px; border: 1px solid var(--border); }
    .lang-btn { padding: 6px 14px; border-radius: 7px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all var(--ease); border: none; background: transparent; color: var(--secondary); font-family: inherit; }
    .lang-btn.active { background: #fff; color: var(--brand); box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
    .hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 8px; }
    .hamburger span { display: block; width: 22px; height: 2px; background: var(--brand); border-radius: 2px; }
    .mobile-nav { display: none; position: fixed; top: 70px; left: 0; right: 0; background: #fff; border-bottom: 1px solid var(--border); padding: 12px 16px; z-index: 199; flex-direction: column; gap: 2px; }
    .mobile-nav.open { display: flex; }
    .mobile-nav a { padding: 12px 16px; font-size: 15px; font-weight: 600; color: var(--secondary); border-radius: 10px; }
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
    .ph-avatar { width: 26px; height: 26px; border-radius: 50%; background: var(--accent-light); flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: var(--accent); }
    .ph-lines { flex: 1; display: flex; flex-direction: column; gap: 4px; }
    .ph-line { height: 5px; border-radius: 3px; background: var(--border); }
    .ph-line.dark { background: rgba(10,10,10,0.12); width: 65%; }
    .ph-line.short { width: 40%; }
    .ph-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; }
    .ph-card { background: #fff; border-radius: 10px; overflow: hidden; }
    .ph-card-img { height: 58px; display: flex; align-items: center; justify-content: center; }
    .ph-card-img.c1 { background: linear-gradient(135deg,#FFE4E4,#FFF1F1); color: var(--accent); }
    .ph-card-img.c2 { background: linear-gradient(135deg,#EDE9FE,#F5F3FF); color: #7C3AED; }
    .ph-card-img.c3 { background: linear-gradient(135deg,#DCFCE7,#F0FDF4); color: #16A34A; }
    .ph-card-body { padding: 5px 7px 7px; display: flex; flex-direction: column; gap: 3px; }
    .ph-wide { background: #fff; border-radius: 10px; overflow: hidden; display: flex; gap: 8px; align-items: center; padding: 8px; }
    .ph-wide-img { width: 40px; height: 40px; border-radius: 8px; background: linear-gradient(135deg,#FEF3C7,#FFF7ED); display: flex; align-items: center; justify-content: center; color: #D97706; flex-shrink: 0; }
    .ph-stat-bar { background: var(--brand); border-radius: 10px; padding: 10px; display: flex; justify-content: space-around; }
    .ph-stat { display: flex; flex-direction: column; align-items: center; gap: 3px; }
    .ph-stat-n { height: 8px; width: 28px; border-radius: 4px; background: var(--accent); opacity: 0.85; }
    .ph-stat-l { height: 4px; width: 22px; border-radius: 2px; background: rgba(255,255,255,0.2); }

    /* Floating cards */
    .fc { position: absolute; background: #fff; border-radius: 16px; box-shadow: 0 16px 48px rgba(0,0,0,0.13); padding: 13px 17px; display: flex; align-items: center; gap: 11px; z-index: 5; white-space: nowrap; animation: bob 4s ease-in-out infinite; }
    .fc-1 { top: 14%; right: -16px; animation-delay: 0s; }
    .fc-2 { bottom: 18%; left: -28px; animation-delay: 1.6s; }
    .fc-3 { top: 56%; right: -36px; animation-delay: 0.9s; }
    .fc-icon { width: 38px; height: 38px; border-radius: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .fc-icon.red { background: var(--accent-light); color: var(--accent); }
    .fc-icon.green { background: #ECFDF5; color: #16A34A; }
    .fc-icon.blue { background: #EFF6FF; color: #2563EB; }
    .fc-p { font-size: 13px; font-weight: 700; color: var(--brand); }
    .fc-s { font-size: 11px; color: var(--secondary); }
    @keyframes bob { 0%,100%{ transform: translateY(0); } 50%{ transform: translateY(-9px); } }

    /* ─── STATS STRIP ─── */
    .strip { background: var(--brand); padding: 60px 0; }
    .strip-grid { display: grid; grid-template-columns: repeat(4,1fr); }
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
    .role-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
    .role-icon.blue { background: #DBEAFE; color: #2563EB; }
    .role-icon.red { background: #FFE4E6; color: var(--accent); }
    .role-icon.green { background: #D1FAE5; color: #16A34A; }
    .role-card .h4 { margin-bottom: 10px; }

    /* ─── HOW IT WORKS ─── */
    .how-tabs { display: flex; background: var(--bg); border-radius: 13px; padding: 4px; gap: 4px; width: fit-content; margin: 0 auto 56px; border: 1px solid var(--border); }
    .how-tab { padding: 11px 26px; border-radius: 9px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all var(--ease); border: none; background: transparent; color: var(--secondary); font-family: inherit; }
    .how-tab.active { background: #fff; color: var(--brand); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .how-panel { display: none; }
    .how-panel.active { display: grid; grid-template-columns: repeat(3,1fr); gap: 28px; }
    .step { text-align: center; padding: 28px 20px; position: relative; }
    .step-num { width: 44px; height: 44px; border-radius: 13px; background: var(--brand); color: #fff; font-size: 18px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
    .step-icon-wrap { width: 56px; height: 56px; border-radius: 16px; background: var(--bg); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--brand); }
    .step .h4 { margin-bottom: 10px; }
    .how-panel.active .step:not(:last-child)::after { content:'→'; position:absolute; right:-18px; top:56px; color:var(--border); font-size:22px; }
    [dir="rtl"] .how-panel.active .step:not(:last-child)::after { content:'←'; right:auto; left:-18px; }

    /* ─── FEATURES ─── */
    .feat-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; }
    .feat-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 30px 26px; transition: all var(--ease); position: relative; overflow: hidden; }
    .feat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent); opacity:0; transition:opacity var(--ease); }
    .feat-card:hover { box-shadow: var(--shadow); transform: translateY(-2px); border-color: transparent; }
    .feat-card:hover::before { opacity:1; }
    .feat-icon { width: 50px; height: 50px; border-radius: 14px; background: var(--accent-light); display: flex; align-items: center; justify-content: center; margin-bottom: 18px; color: var(--accent); }
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
    .admin-card-icon { margin-bottom: 10px; color: var(--brand); display: flex; }
    .admin-card .h4 { margin-bottom: 5px; font-size: 15px; }

    /* ─── FOOTER ─── */
    footer { background: var(--brand); color: #fff; padding: 64px 0 32px; }
    .foot-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 56px; padding-bottom: 48px; border-bottom: 1px solid rgba(255,255,255,0.07); }
    .foot-logo { display: flex; align-items: center; gap: 10px; font-size: 21px; font-weight: 800; margin-bottom: 14px; }
    .foot-logo-box { width: 40px; height: 40px; background: #fff; border-radius: 10px; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
    .foot-logo-box img { width: 34px; height: 34px; object-fit: contain; display: block; }
    .foot-desc { font-size: 14px; color: rgba(255,255,255,0.45); line-height: 1.75; max-width: 280px; }
    .foot-col-title { font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 14px; }
    .foot-links { list-style: none; display: flex; flex-direction: column; gap: 9px; }
    .foot-links a { font-size: 14px; color: rgba(255,255,255,0.55); transition: color var(--ease); }
    .foot-links a:hover { color: #fff; }
    .foot-bottom { display: flex; align-items: center; justify-content: space-between; margin-top: 30px; font-size: 13px; color: rgba(255,255,255,0.3); }

    /* ─── REVEAL ─── */
    .reveal { opacity: 0; transform: translateY(26px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .reveal.visible { opacity: 1; transform: none; }
    .d1 { transition-delay: 0.1s; } .d2 { transition-delay: 0.2s; } .d3 { transition-delay: 0.3s; } .d4 { transition-delay: 0.4s; }

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

{{-- ═══ SVG SPRITE ═══ --}}
<svg style="display:none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <symbol id="i-bag" viewBox="0 0 24 24" fill="none">
    <path d="M3.32352 13.0113C3.6739 10.009 4.18586 7.75784 4.66063 6.15851C5.04994 4.84711 5.24459 4.19141 6.04283 3.5957C6.84107 3 7.65697 3 9.28876 3H14.7113C16.3431 3 17.159 3 17.9572 3.5957C18.7554 4.19141 18.9501 4.84711 19.3394 6.15851C19.8142 7.75784 20.3261 10.009 20.6765 13.0113C21.0895 16.5497 21.2959 18.3189 20.1027 19.6594C18.9095 21 16.9758 21 13.1084 21H10.8916C7.02422 21 5.09052 21 3.89731 19.6594C2.70411 18.3189 2.91058 16.5497 3.32352 13.0113Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M9 7C9 8.65685 10.3431 10 12 10C13.6569 10 15 8.65685 15 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-cart" viewBox="0 0 24 24" fill="none">
    <path d="M10.5 20.25C10.5 20.6642 10.1642 21 9.75 21C9.33579 21 9 20.6642 9 20.25C9 19.8358 9.33579 19.5 9.75 19.5C10.1642 19.5 10.5 19.8358 10.5 20.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M19 20.25C19 20.6642 18.6642 21 18.25 21C17.8358 21 17.5 20.6642 17.5 20.25C17.5 19.8358 17.8358 19.5 18.25 19.5C18.6642 19.5 19 19.8358 19 20.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M2 3H2.20664C3.53124 3 4.19354 3 4.6255 3.40221C5.05746 3.80441 5.10464 4.46503 5.19902 5.78626L5.45035 9.30496C5.5924 11.2936 5.66342 12.2879 5.96476 13.0961C6.62531 14.8677 8.08229 16.2244 9.89648 16.757C10.7241 17 11.7267 17 13.7317 17C15.8373 17 16.89 17 17.7417 16.7416C19.6593 16.1599 21.1599 14.6593 21.7416 12.7417C22 11.89 22 10.8433 22 8.75C22 8.05222 22 7.70333 21.9139 7.41943C21.72 6.78023 21.2198 6.28002 20.5806 6.08612C20.2967 6 19.9478 6 19.25 6H5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M16 10V13M11 10V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-store" viewBox="0 0 24 24" fill="none">
    <path d="M3.50002 10V15C3.50002 17.8284 3.50002 19.2426 4.37869 20.1213C5.25737 21 6.67159 21 9.50002 21H14.5C17.3284 21 18.7427 21 19.6213 20.1213C20.5 19.2426 20.5 17.8284 20.5 15V10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M17 7.50184C17 8.88255 15.8807 9.99997 14.5 9.99997C13.1193 9.99997 12 8.88068 12 7.49997C12 8.88068 10.8807 9.99997 9.50002 9.99997C8.1193 9.99997 7.00002 8.88068 7.00002 7.49997C7.00002 8.88068 5.82655 9.99997 4.37901 9.99997C3.59984 9.99997 2.90008 9.67567 2.42 9.16087C1.59462 8.2758 2.12561 6.97403 2.81448 5.98842L3.20202 5.45851C4.08386 4.2527 4.52478 3.6498 5.16493 3.32494C5.80508 3.00008 6.55201 3.00018 8.04587 3.00038L15.9551 3.00143C17.4485 3.00163 18.1952 3.00173 18.8351 3.32658C19.475 3.65143 19.9158 4.25414 20.7974 5.45957L21.1855 5.99029C21.8744 6.97589 22.4054 8.27766 21.58 9.16273C21.0999 9.67754 20.4002 10.0018 19.621 10.0018C18.1734 10.0018 17 8.88255 17 7.50184Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M14.9971 17C14.3133 17.6072 13.2247 18 11.9985 18C10.7723 18 9.68376 17.6072 9 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-message" viewBox="0 0 24 24" fill="none">
    <path d="M8.5 14.5H15.5M8.5 9.5H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M14.1706 20.8905C18.3536 20.6125 21.6856 17.2332 21.9598 12.9909C22.0134 12.1607 22.0134 11.3009 21.9598 10.4707C21.6856 6.22838 18.3536 2.84913 14.1706 2.57107C12.7435 2.47621 11.2536 2.47641 9.8294 2.57107C5.64639 2.84913 2.31441 6.22838 2.04024 10.4707C1.98659 11.3009 1.98659 12.1607 2.04024 12.9909C2.1401 14.536 2.82343 15.9666 3.62791 17.1746C4.09501 18.0203 3.78674 19.0758 3.30021 19.9978C2.94941 20.6626 2.77401 20.995 2.91484 21.2351C3.05568 21.4752 3.37026 21.4829 3.99943 21.4982C5.24367 21.5285 6.08268 21.1757 6.74868 20.6846C7.1264 20.4061 7.31527 20.2668 7.44544 20.2508C7.5756 20.2348 7.83177 20.3403 8.34401 20.5513C8.8044 20.7409 9.33896 20.8579 9.8294 20.8905C11.2536 20.9852 12.7435 20.9854 14.1706 20.8905Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-check" viewBox="0 0 24 24" fill="none">
    <path d="M22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12Z" stroke="currentColor" stroke-width="1.5"/>
    <path d="M8 12.75C8 12.75 9.6 13.6625 10.4 15C10.4 15 12.8 9.75 16 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-bell" viewBox="0 0 24 24" fill="none">
    <path d="M15.5 18C15.5 19.933 13.933 21.5 12 21.5C10.067 21.5 8.5 19.933 8.5 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M19.2311 18H4.76887C3.79195 18 3 17.208 3 16.2311C3 15.762 3.18636 15.3121 3.51809 14.9803L4.12132 14.3771C4.68393 13.8145 5 13.0514 5 12.2558V9.5C5 5.63401 8.13401 2.5 12 2.5C15.866 2.5 19 5.634 19 9.5V12.2558C19 13.0514 19.3161 13.8145 19.8787 14.3771L20.4819 14.9803C20.8136 15.3121 21 15.762 21 16.2311C21 17.208 20.208 18 19.2311 18Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-location" viewBox="0 0 24 24" fill="none">
    <path d="M13.6177 21.367C13.1841 21.773 12.6044 22 12.0011 22C11.3978 22 10.8182 21.773 10.3845 21.367C6.41302 17.626 1.09076 13.4469 3.68627 7.37966C5.08963 4.09916 8.45834 2 12.0011 2C15.5439 2 18.9126 4.09916 20.316 7.37966C22.9082 13.4393 17.599 17.6389 13.6177 21.367Z" stroke="currentColor" stroke-width="1.5"/>
    <path d="M15.5 11C15.5 12.933 13.933 14.5 12 14.5C10.067 14.5 8.5 12.933 8.5 11C8.5 9.067 10.067 7.5 12 7.5C13.933 7.5 15.5 9.067 15.5 11Z" stroke="currentColor" stroke-width="1.5"/>
  </symbol>
  <symbol id="i-global" viewBox="0 0 24 24" fill="none">
    <path d="M22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12Z" stroke="currentColor" stroke-width="1.5"/>
    <path d="M20 5.69899C19.0653 5.76636 17.8681 6.12824 17.0379 7.20277C15.5385 9.14361 14.039 9.30556 13.0394 8.65861C11.5399 7.6882 12.8 6.11636 11.0401 5.26215C9.89313 4.70542 9.73321 3.19045 10.3716 2" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
    <path d="M2 11C2.7625 11.6621 3.83046 12.2682 5.08874 12.2682C7.68843 12.2682 8.20837 12.7649 8.20837 14.7518C8.20837 16.7387 8.20837 16.7387 8.72831 18.2288C9.06651 19.1981 9.18472 20.1674 8.5106 21" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
    <path d="M22 13.4523C21.1129 12.9411 20 12.7308 18.8734 13.5405C16.7177 15.0898 15.2314 13.806 14.5619 15.0889C13.5765 16.9775 17.0957 17.5711 14 22" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-moon" viewBox="0 0 24 24" fill="none">
    <path d="M21.0985 7.84477C20.458 8.55417 19.5311 9 18.5 9C16.567 9 15 7.433 15 5.5C15 4.46895 15.4458 3.54203 16.1552 2.90149M16.1552 2.90149C18.3384 3.90018 20.0998 5.66155 21.0985 7.84477C21.6774 9.11025 22 10.5174 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C13.4826 2 14.8898 2.32262 16.1552 2.90149Z" stroke="currentColor" stroke-width="1.5"/>
    <path d="M16 16C16 17.1046 15.1046 18 14 18C12.8954 18 12 17.1046 12 16C12 14.8954 12.8954 14 14 14C15.1046 14 16 14.8954 16 16Z" stroke="currentColor" stroke-width="1.5"/>
    <path d="M7.13086 14H7.00586M7.25586 14C7.25586 14.1381 7.14393 14.25 7.00586 14.25C6.86779 14.25 6.75586 14.1381 6.75586 14C6.75586 13.8619 6.86779 13.75 7.00586 13.75C7.14393 13.75 7.25586 13.8619 7.25586 14Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M10.1309 8H10.0059M10.2559 8C10.2559 8.13807 10.1439 8.25 10.0059 8.25C9.86779 8.25 9.75586 8.13807 9.75586 8C9.75586 7.86193 9.86779 7.75 10.0059 7.75C10.1439 7.75 10.2559 7.86193 10.2559 8Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-search" viewBox="0 0 24 24" fill="none">
    <path d="M17 17L21 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19C15.4183 19 19 15.4183 19 11Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-box" viewBox="0 0 24 24" fill="none">
    <path d="M2.5 7.5V13.5C2.5 17.2712 2.5 19.1569 3.67157 20.3284C4.84315 21.5 6.72876 21.5 10.5 21.5H13.5C17.2712 21.5 19.1569 21.5 20.3284 20.3284C21.5 19.1569 21.5 17.2712 21.5 13.5V7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M3.86909 5.31461L2.5 7.5H21.5L20.2478 5.41303C19.3941 3.99021 18.9673 3.2788 18.2795 2.8894C17.5918 2.5 16.7621 2.5 15.1029 2.5H8.95371C7.32998 2.5 6.51812 2.5 5.84013 2.8753C5.16215 3.2506 4.73113 3.93861 3.86909 5.31461Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M12 7.5V2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M10 10.5H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-truck" viewBox="0 0 24 24" fill="none">
    <path d="M19.5 17.5C19.5 18.8807 18.3807 20 17 20C15.6193 20 14.5 18.8807 14.5 17.5C14.5 16.1193 15.6193 15 17 15C18.3807 15 19.5 16.1193 19.5 17.5Z" stroke="currentColor" stroke-width="1.5"/>
    <path d="M9.5 17.5C9.5 18.8807 8.38071 20 7 20C5.61929 20 4.5 18.8807 4.5 17.5C4.5 16.1193 5.61929 15 7 15C8.38071 15 9.5 16.1193 9.5 17.5Z" stroke="currentColor" stroke-width="1.5"/>
    <path d="M14.5 17.5H9.5M19.5 17.5H20.2632C20.4831 17.5 20.5931 17.5 20.6855 17.4885C21.3669 17.4036 21.9036 16.8669 21.9885 16.1855C22 16.0931 22 15.9831 22 15.7632V13C22 9.41015 19.0899 6.5 15.5 6.5M2 4H12C13.4142 4 14.1213 4 14.5607 4.43934C15 4.87868 15 5.58579 15 7V15.5M2 12.75V15C2 15.9346 2 16.4019 2.20096 16.75C2.33261 16.978 2.52197 17.1674 2.75 17.299C3.09808 17.5 3.56538 17.5 4.5 17.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M2 7H8M2 10H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-tag" viewBox="0 0 24 24" fill="none">
    <path d="M6.98633 3.7002C9.78335 6.79476 14.3961 0.115903 17.1255 2.53974C18.696 3.93439 18.1995 7.01373 16.1607 9.01999" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <path d="M13.3927 12.5722C13.2353 12.4282 13.0201 12.2983 12.7272 12.1951C11.6788 11.8256 10.391 13.0623 11.302 14.1944C11.7917 14.803 12.1692 14.9901 12.1337 15.6812C12.1087 16.1673 11.6311 16.6752 11.0018 16.8686C10.4551 17.0367 9.85198 16.8142 9.47052 16.3879C9.00476 15.8675 9.0518 15.3769 9.04782 15.163M13.3927 12.5722C13.8075 13.0373 13.9014 13.6494 13.7897 13.9839M13.3927 12.5722L13.9668 11.998M9.51204 16.4528L8.9668 16.998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M18.2726 6.63305C19.1981 6.8108 19.4057 7.39525 19.682 9.01703C19.9309 10.4776 20.0007 12.2304 20.0007 12.9765C19.9753 13.2515 19.8625 13.5081 19.682 13.7174C17.7469 15.7455 13.9064 19.5753 11.9681 21.4778C11.2074 22.1569 10.0597 22.1716 9.25241 21.5482C7.59928 20.0612 6.01095 18.3803 4.45501 16.8625C3.82993 16.0574 3.84458 14.9129 4.52567 14.1544C6.57621 12.0272 10.2867 8.38602 12.3813 6.3745C12.5913 6.19455 12.8486 6.08199 13.1243 6.05672C13.5943 6.0566 14.4005 6.11977 15.1859 6.1653" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
  </symbol>
  <symbol id="i-analytics" viewBox="0 0 24 24" fill="none">
    <path d="M7 17L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <path d="M12 17L12 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <path d="M17 17L17 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <path d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-settings" viewBox="0 0 24 24" fill="none">
    <path d="M21.3175 7.14139L20.8239 6.28479C20.4506 5.63696 20.264 5.31305 19.9464 5.18388C19.6288 5.05472 19.2696 5.15664 18.5513 5.36048L17.3311 5.70418C16.8725 5.80994 16.3913 5.74994 15.9726 5.53479L15.6357 5.34042C15.2766 5.11043 15.0004 4.77133 14.8475 4.37274L14.5136 3.37536C14.294 2.71534 14.1842 2.38533 13.9228 2.19657C13.6615 2.00781 13.3143 2.00781 12.6199 2.00781H11.5051C10.8108 2.00781 10.4636 2.00781 10.2022 2.19657C9.94085 2.38533 9.83106 2.71534 9.61149 3.37536L9.27753 4.37274C9.12465 4.77133 8.84845 5.11043 8.48937 5.34042L8.15249 5.53479C7.73374 5.74994 7.25259 5.80994 6.79398 5.70418L5.57375 5.36048C4.85541 5.15664 4.49625 5.05472 4.17867 5.18388C3.86109 5.31305 3.67445 5.63696 3.30115 6.28479L2.80757 7.14139C2.45766 7.74864 2.2827 8.05227 2.31666 8.37549C2.35061 8.69871 2.58483 8.95918 3.05326 9.48012L4.0843 10.6328C4.3363 10.9518 4.51521 11.5078 4.51521 12.0077C4.51521 12.5078 4.33636 13.0636 4.08433 13.3827L3.05326 14.5354C2.58483 15.0564 2.35062 15.3168 2.31666 15.6401C2.2827 15.9633 2.45766 16.2669 2.80757 16.8741L3.30114 17.7307C3.67443 18.3785 3.86109 18.7025 4.17867 18.8316C4.49625 18.9608 4.85542 18.8589 5.57377 18.655L6.79394 18.3113C7.25263 18.2055 7.73387 18.2656 8.15267 18.4808L8.4895 18.6752C8.84851 18.9052 9.12464 19.2442 9.2775 19.6428L9.61149 20.6403C9.83106 21.3003 9.94085 21.6303 10.2022 21.8191C10.4636 22.0078 10.8108 22.0078 11.5051 22.0078H12.6199C13.3143 22.0078 13.6615 22.0078 13.9228 21.8191C14.1842 21.6303 14.294 21.3003 14.5136 20.6403L14.8476 19.6428C15.0004 19.2442 15.2765 18.9052 15.6356 18.6752L15.9724 18.4808C16.3912 18.2656 16.8724 18.2055 17.3311 18.3113L18.5513 18.655C19.2696 18.8589 19.6288 18.9608 19.9464 18.8316C20.264 18.7025 20.4506 18.3785 20.8239 17.7307L21.3175 16.8741C21.6674 16.2669 21.8423 15.9633 21.8084 15.6401C21.7744 15.3168 21.5402 15.0564 21.0718 14.5354L20.0407 13.3827C19.7887 13.0636 19.6098 12.5078 19.6098 12.0077C19.6098 11.5078 19.7888 10.9518 20.0407 10.6328L21.0718 9.48012C21.5402 8.95918 21.7744 8.69871 21.8084 8.37549C21.8423 8.05227 21.6674 7.74864 21.3175 7.14139Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <path d="M15.5195 12C15.5195 13.933 13.9525 15.5 12.0195 15.5C10.0865 15.5 8.51953 13.933 8.51953 12C8.51953 10.067 10.0865 8.5 12.0195 8.5C13.9525 8.5 15.5195 10.067 15.5195 12Z" stroke="currentColor" stroke-width="1.5"/>
  </symbol>
  <symbol id="i-coupon" viewBox="0 0 24 24" fill="none">
    <path d="M15 7.99805L9 13.998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M14 1.99805H10C7.17157 1.99805 5.75736 1.99805 4.87868 2.87673C4 3.75541 4 5.16962 4 7.99805V19.5741C4 20.4277 4 20.8545 4.16333 21.104C4.34401 21.38 4.64917 21.5491 4.97898 21.5561C5.27712 21.5623 5.63906 21.3361 6.36294 20.8837C6.9209 20.535 7.19989 20.3606 7.49648 20.2835C7.82667 20.1976 8.17333 20.1976 8.50352 20.2835C8.80011 20.3606 9.0791 20.535 9.63706 20.8837L10 21.1105C10.9126 21.6809 11.3689 21.9661 11.8736 21.998C11.9578 22.0034 12.0422 22.0034 12.1264 21.998C12.6311 21.9661 13.0874 21.6809 14 21.1105L14.3629 20.8837C14.9209 20.535 15.1999 20.3606 15.4965 20.2835C15.8267 20.1976 16.1733 20.1976 16.5035 20.2835C16.8001 20.3606 17.0791 20.535 17.6371 20.8837C18.3609 21.3361 18.7229 21.5623 19.021 21.5561C19.3508 21.5491 19.656 21.38 19.8367 21.104C20 20.8545 20 20.4277 20 19.5741V7.99805C20 5.16962 20 3.75541 19.1213 2.87673C18.2426 1.99805 16.8284 1.99805 14 1.99805Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M9.375 8.24805H9.25M9.5 8.24805C9.5 8.38612 9.38807 8.49805 9.25 8.49805C9.11193 8.49805 9 8.38612 9 8.24805C9 8.10998 9.11193 7.99805 9.25 7.99805C9.38807 7.99805 9.5 8.10998 9.5 8.24805Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M14.875 13.748H14.75M15 13.748C15 13.8861 14.8881 13.998 14.75 13.998C14.6119 13.998 14.5 13.8861 14.5 13.748C14.5 13.61 14.6119 13.498 14.75 13.498C14.8881 13.498 15 13.61 15 13.748Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-hanger" viewBox="0 0 24 24" fill="none">
    <path d="M4.12572 15.3668L10.1284 11.9903C10.7234 11.6556 11.3252 11.5 12 11.5C12.6748 11.5 13.2766 11.6556 13.8716 11.9903L19.8743 15.3668C20.5697 15.7579 21 16.4937 21 17.2916C21 18.5113 20.0113 19.5 18.7916 19.5H5.20841C3.98874 19.5 3 18.5113 3 17.2916C3 16.4937 3.43034 15.7579 4.12572 15.3668Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M10 6.40476C10 5.35279 10.8954 4.5 12 4.5C13.1046 4.5 14 5.35279 14 6.40476C14 7.12453 13.5808 7.75106 12.9623 8.07498C12.473 8.33119 12 8.75724 12 9.30952V11.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
  </symbol>
  <symbol id="i-dress" viewBox="0 0 24 24" fill="none">
    <path d="M7.61 2.1479L7.80042 2.10885C8.28808 2.00885 8.53191 1.95884 8.6895 2.04202C8.84709 2.1252 8.97976 2.43749 9.24511 3.06207C9.72042 4.18086 10.8245 4.96673 12 4.96673C13.1755 4.96673 14.2796 4.18086 14.7549 3.06207C15.0202 2.43749 15.1529 2.1252 15.3105 2.04202C15.4681 1.95884 15.7119 2.00885 16.1996 2.10885L16.39 2.1479C17.6491 2.37803 17.6688 2.3895 18.5326 3.39285C19.2624 4.2406 20.3084 5.10689 20.8298 6.1272C21.2138 6.87865 20.8865 7.52289 20.4556 8.11988C19.9173 8.86555 19.2184 9.58171 18.272 9.00152C17.6678 8.63104 17.1859 7.84916 16.7173 7.30825C16.7173 7.30825 17 10.9246 16 11.9315C16.9077 12.5863 18.3424 13.858 19.4805 16.4642C19.8862 17.3933 20.4317 18.4519 19.8937 19.4437C18.0646 22.8154 5.97514 22.8887 4.10625 19.4437C3.56824 18.452 4.11378 17.3933 4.51948 16.4642C5.65756 13.858 7.0923 12.5863 8 11.9315C7 10.9246 7.2827 7.30825 7.2827 7.30825C6.81411 7.84916 6.33223 8.63104 5.72796 9.00152C4.78163 9.58171 4.08274 8.86554 3.54444 8.11988C3.11346 7.52289 2.78615 6.87864 3.17016 6.1272C3.69156 5.10689 4.73757 4.2406 5.46741 3.39285C6.33123 2.38948 6.35093 2.37803 7.61 2.1479Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M8 12H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
  <symbol id="i-chart-up" viewBox="0 0 24 24" fill="none">
    <path d="M21 21H10C6.70017 21 5.05025 21 4.02513 19.9749C3 18.9497 3 17.2998 3 14V3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <path d="M7.99707 16.999C11.5286 16.999 18.9122 15.5348 18.6979 6.43269M16.4886 8.04302L18.3721 6.14612C18.5656 5.95127 18.8798 5.94981 19.0751 6.14286L20.9971 8.04302" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
</svg>

{{-- ═══ NAVBAR ═══ --}}
<nav class="nav" id="nav">
    <div class="wrap">
        <a href="#" class="nav-logo">
            <img src="/logo.png" alt="CoreShop" class="nav-logo-box">
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
    <a href="#how" onclick="toggleNav()"><span class="e">How It Works</span><span class="a" style="display:none">كيف يعمل </span></a>
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
                    <div class="fc-icon red"><svg width="20" height="20" viewBox="0 0 24 24"><use href="#i-bag"/></svg></div>
                    <div><div class="fc-p"><span class="e">New Order!</span><span class="a" style="display:none">طلب جديد!</span></div><div class="fc-s"><span class="e">JOD 48.00 · 3 items</span><span class="a" style="display:none">٤٨ د.أ · ٣ منتجات</span></div></div>
                </div>
                <div class="phone">
                    <div class="phone-notch"></div>
                    <div class="phone-screen">
                        <div class="ph-bar">
                            <div class="ph-avatar"><svg width="13" height="13" viewBox="0 0 24 24"><use href="#i-store"/></svg></div>
                            <div class="ph-lines"><div class="ph-line dark"></div><div class="ph-line short"></div></div>
                        </div>
                        <div class="ph-grid">
                            <div class="ph-card"><div class="ph-card-img c1"><svg width="26" height="26" viewBox="0 0 24 24"><use href="#i-dress"/></svg></div><div class="ph-card-body"><div class="ph-line dark"></div><div class="ph-line" style="background:var(--accent);opacity:.45;width:40%"></div></div></div>
                            <div class="ph-card"><div class="ph-card-img c2"><svg width="26" height="26" viewBox="0 0 24 24"><use href="#i-hanger"/></svg></div><div class="ph-card-body"><div class="ph-line dark"></div><div class="ph-line" style="background:var(--accent);opacity:.45;width:38%"></div></div></div>
                        </div>
                        <div class="ph-wide">
                            <div class="ph-wide-img"><svg width="20" height="20" viewBox="0 0 24 24"><use href="#i-tag"/></svg></div>
                            <div class="ph-lines"><div class="ph-line dark"></div><div class="ph-line short" style="background:var(--accent);opacity:.4"></div></div>
                        </div>
                        <div class="ph-grid">
                            <div class="ph-card"><div class="ph-card-img c3"><svg width="26" height="26" viewBox="0 0 24 24"><use href="#i-bag"/></svg></div><div class="ph-card-body"><div class="ph-line dark"></div><div class="ph-line" style="background:var(--accent);opacity:.45;width:42%"></div></div></div>
                            <div class="ph-card" style="background:var(--brand);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:4px;padding:10px;color:var(--accent)">
                                <svg width="20" height="20" viewBox="0 0 24 24"><use href="#i-analytics"/></svg>
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
                    <div class="fc-icon blue"><svg width="20" height="20" viewBox="0 0 24 24"><use href="#i-message"/></svg></div>
                    <div><div class="fc-p"><span class="e">Live Chat</span><span class="a" style="display:none">دردشة مباشرة</span></div><div class="fc-s"><span class="e">Client asked about size</span><span class="a" style="display:none">سأل عن المقاس</span></div></div>
                </div>
                <div class="fc fc-3 reveal d1">
                    <div class="fc-icon green"><svg width="20" height="20" viewBox="0 0 24 24"><use href="#i-check"/></svg></div>
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
                <div class="role-icon blue"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-cart"/></svg></div>
                <h4 class="h4"><span class="e">For Clients</span><span class="a" style="display:none">للعملاء</span></h4>
                <p class="body"><span class="e">Browse hundreds of products from local stores. Add to cart, choose your address, and track every update in real time.</span><span class="a" style="display:none">تصفح مئات المنتجات من المتاجر المحلية. أضف للسلة، اختر عنوانك، وتابع كل تحديث في الوقت الفعلي.</span></p>
            </div>
            <div class="role-card red reveal d2">
                <div class="role-icon red"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-store"/></svg></div>
                <h4 class="h4"><span class="e">For Sellers</span><span class="a" style="display:none">للبائعين</span></h4>
                <p class="body"><span class="e">Set up your store in minutes. Manage products, incoming orders, customer chat, and analytics — all from your phone.</span><span class="a" style="display:none">أنشئ متجرك في دقائق. أدر المنتجات والطلبات والدردشة مع العملاء والتحليلات — كل ذلك من هاتفك.</span></p>
            </div>
            <div class="role-card green reveal d3">
                <div class="role-icon green"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-settings"/></svg></div>
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
            <button class="how-tab active" id="tc" onclick="tab('c')"><span class="e">For Clients</span><span class="a" style="display:none">للعملاء</span></button>
            <button class="how-tab" id="ts" onclick="tab('s')"><span class="e">For Sellers</span><span class="a" style="display:none">للبائعين</span></button>
        </div>
        <div class="how-panel active" id="pc">
            <div class="step reveal">
                <div class="step-icon-wrap"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-search"/></svg></div>
                <div class="step-num">1</div>
                <h4 class="h4"><span class="e">Browse &amp; Discover</span><span class="a" style="display:none">تصفح واكتشف</span></h4>
                <p class="body"><span class="e">Explore stores, categories, flash deals, and featured products. Search and filter to find exactly what you need.</span><span class="a" style="display:none">استكشف المتاجر والفئات والعروض والمنتجات المميزة. ابحث وصفّ للعثور على ما تحتاجه.</span></p>
            </div>
            <div class="step reveal d2">
                <div class="step-icon-wrap"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-box"/></svg></div>
                <div class="step-num">2</div>
                <h4 class="h4"><span class="e">Add to Cart &amp; Order</span><span class="a" style="display:none">أضف وأطلب</span></h4>
                <p class="body"><span class="e">Pick your variants, add to cart, choose your saved address, apply a coupon, and place your order with one tap.</span><span class="a" style="display:none">اختر المتغيرات، أضف للسلة، اختر عنوانك، طبّق كوبون، وضع طلبك بنقرة واحدة.</span></p>
            </div>
            <div class="step reveal d4">
                <div class="step-icon-wrap"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-truck"/></svg></div>
                <div class="step-num">3</div>
                <h4 class="h4"><span class="e">Track &amp; Receive</span><span class="a" style="display:none">تتبع واستلم</span></h4>
                <p class="body"><span class="e">Follow your order through live status updates. Get push notifications at every stage and chat with the seller anytime.</span><span class="a" style="display:none">تابع طلبك عبر تحديثات الحالة المباشرة. احصل على إشعارات فورية في كل مرحلة وتواصل مع البائع في أي وقت.</span></p>
            </div>
        </div>
        <div class="how-panel" id="ps">
            <div class="step reveal">
                <div class="step-icon-wrap"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-store"/></svg></div>
                <div class="step-num">1</div>
                <h4 class="h4"><span class="e">Create Your Store</span><span class="a" style="display:none">أنشئ متجرك</span></h4>
                <p class="body"><span class="e">Set up your store with a logo, banner, GPS location, and working hours — all from the mobile app in minutes.</span><span class="a" style="display:none">أنشئ متجرك بالشعار والغلاف وموقع GPS وأوقات العمل — كل ذلك من التطبيق في دقائق.</span></p>
            </div>
            <div class="step reveal d2">
                <div class="step-icon-wrap"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-tag"/></svg></div>
                <div class="step-num">2</div>
                <h4 class="h4"><span class="e">List Your Products</span><span class="a" style="display:none">أضف منتجاتك</span></h4>
                <p class="body"><span class="e">Upload up to 7 images per product, add size and color variants, set stock levels, and publish for review.</span><span class="a" style="display:none">ارفع حتى 7 صور لكل منتج، أضف متغيرات المقاس واللون، حدد المخزون، وانشر للمراجعة.</span></p>
            </div>
            <div class="step reveal d4">
                <div class="step-icon-wrap"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-analytics"/></svg></div>
                <div class="step-num">3</div>
                <h4 class="h4"><span class="e">Manage &amp; Grow</span><span class="a" style="display:none">أدر ونمِّ</span></h4>
                <p class="body"><span class="e">Accept orders, update statuses, chat with customers, and monitor revenue, top products, and growth analytics.</span><span class="a" style="display:none">اقبل الطلبات، حدّث الحالات، تواصل مع العملاء، وراقب الإيرادات وأفضل المنتجات ونمو الأعمال.</span></p>
            </div>
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
            <div class="feat-card reveal"><div class="feat-icon"><svg width="24" height="24" viewBox="0 0 24 24"><use href="#i-message"/></svg></div><h4 class="h4"><span class="e">Real-time Chat</span><span class="a" style="display:none">دردشة فورية</span></h4><p class="body"><span class="e">Clients and sellers communicate directly in-app. Share product cards and order details inside conversations.</span><span class="a" style="display:none">يتواصل العملاء والبائعون مباشرة داخل التطبيق. شارك بطاقات المنتجات وتفاصيل الطلبات في المحادثات.</span></p></div>
            <div class="feat-card reveal d1"><div class="feat-icon"><svg width="24" height="24" viewBox="0 0 24 24"><use href="#i-bell"/></svg></div><h4 class="h4"><span class="e">Push Notifications</span><span class="a" style="display:none">إشعارات فورية</span></h4><p class="body"><span class="e">Instant heads-up notifications for every order status change and new messages — even when the app is closed.</span><span class="a" style="display:none">إشعارات لحظية لكل تغيير في حالة الطلب والرسائل الجديدة — حتى عند إغلاق التطبيق.</span></p></div>
            <div class="feat-card reveal d2"><div class="feat-icon"><svg width="24" height="24" viewBox="0 0 24 24"><use href="#i-location"/></svg></div><h4 class="h4"><span class="e">GPS Address Detection</span><span class="a" style="display:none">تحديد العنوان بـ GPS</span></h4><p class="body"><span class="e">Clients pin their exact delivery location on an interactive map. Reverse geocoding fills the address automatically.</span><span class="a" style="display:none">يثبّت العملاء موقع التوصيل الدقيق على خريطة تفاعلية. الترميز الجغرافي العكسي يملأ العنوان تلقائيًا.</span></p></div>
            <div class="feat-card reveal d1"><div class="feat-icon"><svg width="24" height="24" viewBox="0 0 24 24"><use href="#i-global"/></svg></div><h4 class="h4"><span class="e">Full Bilingual RTL</span><span class="a" style="display:none">ثنائي اللغة RTL</span></h4><p class="body"><span class="e">Arabic and English with complete RTL layout support. Fonts, direction, and all UI elements switch seamlessly.</span><span class="a" style="display:none">عربي وإنجليزي مع دعم كامل لتخطيط RTL. الخطوط والاتجاه وجميع عناصر الواجهة تتبدل بسلاسة.</span></p></div>
            <div class="feat-card reveal d2"><div class="feat-icon"><svg width="24" height="24" viewBox="0 0 24 24"><use href="#i-chart-up"/></svg></div><h4 class="h4"><span class="e">Seller Analytics</span><span class="a" style="display:none">تحليلات البائع</span></h4><p class="body"><span class="e">Revenue charts, top-selling products, order trends, and customer counts — visible to sellers inside the app.</span><span class="a" style="display:none">مخططات الإيرادات والمنتجات الأكثر مبيعًا واتجاهات الطلبات — مرئية للبائعين في التطبيق.</span></p></div>
            <div class="feat-card reveal d3"><div class="feat-icon"><svg width="24" height="24" viewBox="0 0 24 24"><use href="#i-moon"/></svg></div><h4 class="h4"><span class="e">Dark Mode</span><span class="a" style="display:none">الوضع الليلي</span></h4><p class="body"><span class="e">Full dark mode across every screen. Follows system preference automatically or can be set manually by the user.</span><span class="a" style="display:none">وضع ليلي كامل في كل شاشة. يتبع تفضيلات النظام تلقائيًا أو يضبطه المستخدم يدويًا.</span></p></div>
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
                <div class="admin-card reveal"><div class="admin-card-icon"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-store"/></svg></div><h4 class="h4"><span class="e">Store Approvals</span><span class="a" style="display:none">موافقات المتاجر</span></h4><p class="body" style="font-size:13px"><span class="e">Review and approve seller stores before they go live.</span><span class="a" style="display:none">راجع واعتمد متاجر البائعين قبل إطلاقها.</span></p></div>
                <div class="admin-card reveal d1"><div class="admin-card-icon"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-box"/></svg></div><h4 class="h4"><span class="e">Order Management</span><span class="a" style="display:none">إدارة الطلبات</span></h4><p class="body" style="font-size:13px"><span class="e">Oversee all platform orders and update statuses.</span><span class="a" style="display:none">أشرف على جميع طلبات المنصة وحدّث حالاتها.</span></p></div>
                <div class="admin-card reveal d2"><div class="admin-card-icon"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-analytics"/></svg></div><h4 class="h4"><span class="e">Platform Analytics</span><span class="a" style="display:none">تحليلات المنصة</span></h4><p class="body" style="font-size:13px"><span class="e">6 live charts — revenue, users, orders, top sellers.</span><span class="a" style="display:none">٦ مخططات مباشرة — الإيرادات والمستخدمون والطلبات وأفضل البائعين.</span></p></div>
                <div class="admin-card reveal d3"><div class="admin-card-icon"><svg width="28" height="28" viewBox="0 0 24 24"><use href="#i-coupon"/></svg></div><h4 class="h4"><span class="e">Coupons &amp; Banners</span><span class="a" style="display:none">الكوبونات واللافتات</span></h4><p class="body" style="font-size:13px"><span class="e">Create discount codes and manage home screen banners.</span><span class="a" style="display:none">أنشئ رموز الخصم وأدر لافتات الشاشة الرئيسية.</span></p></div>
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
                        <img src="/logo.png" alt="CoreShop">
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
            <span><span class="e">Made with ♥ for Jordan</span><span class="a" style="display:none">صُنع بـ ♥ للأردن</span></span>
        </div>
    </div>
</footer>

<script>
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

function tab(t) {
    document.getElementById('tc').classList.toggle('active',t==='c');
    document.getElementById('ts').classList.toggle('active',t==='s');
    document.getElementById('pc').classList.toggle('active',t==='c');
    document.getElementById('ps').classList.toggle('active',t==='s');
}

const obs = new IntersectionObserver(entries=>{
    entries.forEach(e=>{ if(e.isIntersecting) e.target.classList.add('visible'); });
},{threshold:0.1});
document.querySelectorAll('.reveal').forEach(el=>obs.observe(el));

window.addEventListener('scroll',()=>{
    document.getElementById('nav').style.boxShadow = window.scrollY>10?'0 2px 20px rgba(0,0,0,0.08)':'none';
});

function toggleNav(){ document.getElementById('mob-nav').classList.toggle('open'); }
</script>
</body>
</html>
