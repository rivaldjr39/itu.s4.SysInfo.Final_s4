<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Mobile Money') ?></title>
    <meta name="description" content="Interface client Mobile Money.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --primary-light: #ccfbf1;
            --accent: #d97706;
            --accent-dark: #b45309;
            --accent-light: #fef3c7;
            --bg: #f1f5f9;
            --bg-card: #ffffff;
            --text: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
            --radius: 12px;
            --radius-lg: 16px;
            --transition: all 0.2s ease;
        }
        * { box-sizing: border-box; }
        html, body { min-height: 100%; margin: 0; padding: 0; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            display: flex;
        }
        a { color: inherit; text-decoration: none; }

        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-card);
            border-right: 1px solid var(--border);
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            overflow-y: auto;
            box-shadow: 4px 0 12px rgba(0,0,0,0.04);
        }
        .sidebar-header {
            padding: 20px 20px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-logo {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 18px; font-weight: 800;
            flex-shrink: 0;
        }
        .sidebar-brand h2 {
            margin: 0;
            font-size: 16px; font-weight: 700;
            color: var(--text); letter-spacing: -0.02em;
        }
        .sidebar-brand span {
            display: block; font-size: 11px;
            color: var(--text-muted); margin-top: 1px;
        }
        .sidebar-profile {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #14b8a6);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 15px;
            flex-shrink: 0;
        }
        .sidebar-user-info strong {
            display: block; font-size: 14px; color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-user-info span {
            display: block; font-size: 12px; color: var(--text-muted);
            font-family: 'IBM Plex Mono', monospace;
        }
        .sidebar-nav {
            flex: 1; padding: 12px;
            display: flex; flex-direction: column; gap: 2px;
        }
        .sidebar-nav-label {
            font-size: 10px; text-transform: uppercase;
            letter-spacing: 0.1em; color: var(--text-muted);
            font-weight: 700; padding: 12px 12px 6px;
        }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-radius: var(--radius);
            font-size: 14px; font-weight: 500;
            color: var(--text-secondary); transition: var(--transition);
        }
        .sidebar-nav a i { width: 20px; text-align: center; font-size: 15px; flex-shrink: 0; }
        .sidebar-nav a:hover { background: var(--bg); color: var(--text); }
        .sidebar-nav a.active { background: var(--primary-light); color: var(--primary); font-weight: 600; }
        .sidebar-nav a.active i { color: var(--primary); }
        .sidebar-footer {
            padding: 12px; border-top: 1px solid var(--border);
        }
        .sidebar-footer a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-radius: var(--radius);
            font-size: 14px; font-weight: 500; color: #ef4444;
            transition: var(--transition);
        }
        .sidebar-footer a i { width: 20px; text-align: center; }
        .sidebar-footer a:hover { background: #fef2f2; }
        .main-content { flex: 1; margin-left: var(--sidebar-width); min-height: 100vh; transition: var(--transition); }
        .sidebar-toggle {
            display: none; position: fixed; top: 12px; left: 12px; z-index: 200;
            width: 44px; height: 44px; border-radius: var(--radius);
            background: var(--bg-card); border: 1px solid var(--border);
            color: var(--text); font-size: 18px; cursor: pointer;
            align-items: center; justify-content: center; box-shadow: var(--shadow-lg);
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.3); z-index: 99; }
        @media (max-width: 768px) {
            .sidebar-toggle { display: flex; }
            .sidebar { transform: translateX(-100%); z-index: 100; box-shadow: none; }
            .sidebar.open { transform: translateX(0); box-shadow: 20px 0 40px rgba(0,0,0,0.12); }
            .sidebar-overlay.open { display: block; }
            .main-content { margin-left: 0; }
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
    </style>
</head>
<body>
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Menu"><i class="fas fa-bars"></i></button>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">M</div>
            <div class="sidebar-brand">
                <h2>Mobile Money</h2>
                <span>Gestion de transactions</span>
            </div>
        </div>
        <?php if (session()->get('numero_telephone')): ?>
        <div class="sidebar-profile">
            <div class="sidebar-avatar"><?= strtoupper(substr(esc(session()->get('client_nom') ?: session()->get('numero_telephone')), 0, 1)) ?></div>
            <div class="sidebar-user-info">
                <strong><?= esc(session()->get('client_nom') ?: 'Client') ?></strong>
                <span><?= esc(session()->get('numero_telephone')) ?></span>
            </div>
        </div>
        <nav class="sidebar-nav" aria-label="Navigation principale">
            <div class="sidebar-nav-label">Navigation</div>
            <a href="<?= site_url('dashboard') ?>"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="<?= site_url('transfert') ?>"><i class="fas fa-paper-plane"></i> Transfert</a>
            <a href="<?= site_url('depot') ?>"><i class="fas fa-arrow-down"></i> Dépôt</a>
            <a href="<?= site_url('retrait') ?>"><i class="fas fa-arrow-up"></i> Retrait</a>
            <a href="<?= site_url('transfert/historique') ?>"><i class="fas fa-clock-rotate"></i> Historique</a>
            <?php if (session()->get('client_role') === 'ADMIN'): ?>
            <div class="sidebar-nav-label" style="margin-top: 8px;">Administration</div>
            <a href="<?= site_url('admin/baremes') ?>"><i class="fas fa-table-list"></i> Barèmes Frais</a>
            <a href="<?= site_url('admin/commissions') ?>"><i class="fas fa-percent"></i> Commissions</a>
            <a href="<?= site_url('admin/prefixes') ?>"><i class="fas fa-hashtag"></i> Préfixes</a>
            <a href="<?= site_url('admin/gains-frais') ?>"><i class="fas fa-chart-line"></i> Gains Frais</a>
            <a href="<?= site_url('admin/promotions') ?>"><i class="fas fa-tag"></i> Promotions</a>
            <?php endif; ?>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= site_url('logout') ?>"><i class="fas fa-right-from-bracket"></i> Déconnexion</a>
        </div>
        <?php endif; ?>
    </aside>
    <main class="main-content"><?= $this->renderSection('content') ?></main>
    <?php if (session()->get('numero_telephone')): ?>
    <script>
    (function() {
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        function open() { sidebar.classList.add('open'); overlay.classList.add('open'); document.body.style.overflow = 'hidden'; }
        function close() { sidebar.classList.remove('open'); overlay.classList.remove('open'); document.body.style.overflow = ''; }
        if (toggle) toggle.addEventListener('click', open);
        if (overlay) overlay.addEventListener('click', close);
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && sidebar.classList.contains('open')) close(); });
    })();
    </script>
    <?php endif; ?>
</body>
</html>