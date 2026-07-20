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

        .client-topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            padding: 16px 16px 0;
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
            <div class="client-topbar">
                <span class="client-user"><?= esc(session()->get('numero_telephone')) ?></span>
                <a class="client-logout" href="<?= site_url('logout') ?>">Déconnexion</a>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</body>
</html>
