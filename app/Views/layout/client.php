<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Mobile Money') ?></title>
    <meta name="description" content="Interface client Mobile Money.">
    <style>
        :root {
            color-scheme: light;
            --teal-950: #0b2e2b;
            --teal-800: #12433f;
            --paper: #f6f3ec;
            --gold: #d9a441;
            --gold-dark: #b5842e;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            margin: 0;
            background: var(--teal-950);
            color: #1f2421;
            font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        a {
            color: inherit;
        }

        .client-shell {
            min-height: 100vh;
        }

        .client-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(8, 40, 38, 0.55);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .client-brand {
            display: grid;
            gap: 2px;
        }

        .client-brand strong {
            color: var(--paper);
            font-size: 15px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .client-brand span {
            color: rgba(246, 243, 236, 0.68);
            font-size: 12px;
        }

        .client-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
        }

        .client-nav a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid rgba(246, 243, 236, 0.16);
            color: var(--paper);
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            transition: background 0.12s ease, border-color 0.12s ease;
        }

        .client-nav a:hover {
            background: rgba(246, 243, 236, 0.08);
            border-color: rgba(246, 243, 236, 0.28);
        }

        .client-nav a.is-primary {
            background: var(--gold);
            color: #1f2421;
            border-color: transparent;
        }

        .client-nav a.is-primary:hover {
            background: var(--gold-dark);
        }

        .client-topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            padding: 12px 16px 0;
        }

        .client-user {
            color: rgba(246, 243, 236, 0.75);
            font-size: 13px;
        }

        .client-logout {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 16px;
            border-radius: 999px;
            background: var(--gold);
            color: #1f2421;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18);
            transition: transform 0.12s ease, background 0.12s ease;
        }

        .client-logout:hover {
            background: var(--gold-dark);
        }

        .client-logout:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>
    <div class="client-shell">
        <?php if (session()->get('numero_telephone')): ?>
            <header class="client-header">
                <div class="client-brand">
                    <strong>Mobile Money</strong>
                    <span><?= esc(session()->get('client_nom') ?: session()->get('numero_telephone')) ?></span>
                </div>

                <nav class="client-nav" aria-label="Navigation client">
                    <a href="<?= site_url('dashboard') ?>">Dashboard</a>
                    <a href="<?= site_url('transfert') ?>">Transfert</a>
                    <a href="<?= site_url('transfert/historique') ?>">Historique</a>
                    <a href="<?= site_url('retrait') ?>">Retrait</a>
                    <?php if (session()->get('client_role') === 'ADMIN'): ?>
                        <a href="<?= site_url('admin/baremes') ?>">Barèmes Frais</a>
                    <?php endif; ?>
                    <a class="is-primary" href="<?= site_url('logout') ?>">Déconnexion</a>
                </nav>
            </header>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</body>
</html>
