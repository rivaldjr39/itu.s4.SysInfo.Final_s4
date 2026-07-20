<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Mobile Money</title>
    <meta name="description" content="Connexion client par numéro de téléphone pour le système Mobile Money.">
    <style>
        :root {
            --teal-950: #082826;
            --teal-800: #103a37;
            --paper: #f5f1e8;
            --ink: #1d2421;
            --ink-soft: #5b6761;
            --gold: #d6a246;
            --gold-deep: #b3822a;
            --line: #d7cfbf;
            --error: #be4b31;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(214, 162, 70, 0.14), transparent 36%),
                radial-gradient(circle at bottom right, rgba(16, 58, 55, 0.35), transparent 42%),
                linear-gradient(135deg, var(--teal-950), #0e3b38 58%, #0c2e2c);
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .login-shell {
            width: 100%;
            max-width: 1040px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 20px;
            align-items: stretch;
        }

        .hero, .panel {
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.34);
        }

        .hero {
            padding: 34px;
            background:
                linear-gradient(160deg, rgba(8, 40, 38, 0.94), rgba(16, 58, 55, 0.90)),
                radial-gradient(circle at top right, rgba(214, 162, 70, 0.18), transparent 35%);
            color: var(--paper);
            position: relative;
        }

        .eyebrow {
            margin: 0 0 14px;
            font-size: 12px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(245, 241, 232, 0.62);
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(34px, 5vw, 56px);
            line-height: 0.96;
            letter-spacing: -0.04em;
        }

        .hero p {
            max-width: 520px;
            margin: 18px 0 0;
            font-size: 16px;
            line-height: 1.65;
            color: rgba(245, 241, 232, 0.84);
        }

        .feature-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 28px;
        }

        .feature {
            padding: 16px 16px 15px;
            border: 1px solid rgba(245, 241, 232, 0.12);
            border-radius: 18px;
            background: rgba(245, 241, 232, 0.06);
            backdrop-filter: blur(8px);
        }

        .feature strong {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            letter-spacing: 0.01em;
        }

        .feature span {
            display: block;
            font-size: 13px;
            line-height: 1.45;
            color: rgba(245, 241, 232, 0.74);
        }

        .panel {
            background: var(--paper);
            display: flex;
            flex-direction: column;
        }

        .panel-top {
            padding: 28px 28px 18px;
            background: linear-gradient(180deg, rgba(255,255,255,0.72), rgba(255,255,255,0));
            border-bottom: 1px solid rgba(0,0,0,0.04);
        }

        .panel-top h2 {
            margin: 0 0 6px;
            font-size: 28px;
            letter-spacing: -0.03em;
        }

        .panel-top p {
            margin: 0;
            color: var(--ink-soft);
            line-height: 1.5;
        }

        .panel-body { padding: 26px 28px 30px; }

        .flash {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 14px;
            line-height: 1.45;
        }

        .flash.success {
            color: #2d6a44;
            background: rgba(79, 122, 92, 0.10);
            border: 1px solid rgba(79, 122, 92, 0.22);
        }

        .flash.error {
            color: var(--error);
            background: rgba(190, 75, 49, 0.10);
            border: 1px solid rgba(190, 75, 49, 0.22);
        }

        form { display: grid; gap: 18px; }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--ink-soft);
        }

        input[type="tel"] {
            width: 100%;
            border: 1.5px solid var(--line);
            border-radius: 14px;
            padding: 16px;
            font-size: 18px;
            font-weight: 600;
            color: var(--ink);
            background: #fff;
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }

        input[type="tel"]:focus {
            border-color: var(--gold-deep);
            box-shadow: 0 0 0 4px rgba(214, 162, 70, 0.18);
            transform: translateY(-1px);
        }

        .hint {
            margin-top: 8px;
            color: var(--ink-soft);
            font-size: 13px;
            line-height: 1.45;
        }

        .actions { display: grid; gap: 12px; margin-top: 2px; }

        button, .secondary {
            border: 0;
            border-radius: 14px;
            padding: 15px 18px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: transform .12s ease, background-color .12s ease, border-color .12s ease;
        }

        .primary {
            background: var(--gold);
            color: #1d241f;
        }

        .primary:hover { background: var(--gold-deep); }

        .secondary {
            background: transparent;
            border: 1px solid var(--line);
            color: var(--ink);
            text-decoration: none;
            text-align: center;
        }

        .secondary:hover { border-color: var(--gold-deep); }

        button:active,
        .secondary:active { transform: scale(0.99); }

        .footer-note {
            margin-top: 18px;
            font-size: 12.5px;
            color: var(--ink-soft);
            line-height: 1.5;
        }

        @media (max-width: 900px) {
            .login-shell { grid-template-columns: 1fr; }
            .hero { padding: 28px; }
            .panel-top,
            .panel-body { padding-left: 22px; padding-right: 22px; }
        }

        @media (max-width: 640px) {
            body { padding: 16px; }
            .hero h1 { font-size: 30px; }
            .feature-list { grid-template-columns: 1fr; }
            .panel-top h2 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        

        <section class="panel">
            <div class="panel-top">
                <h2>Se connecter</h2>
                <p>Entrez le numéro d’un client déjà enregistré dans la base de données.</p>
            </div>

            <div class="panel-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="flash success"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="flash error"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <form action="<?= site_url('login') ?>" method="post">
                    <?= csrf_field() ?>

                    <div>
                        <label for="numero_telephone">Numéro de téléphone</label>
                        <input
                            type="tel"
                            id="numero_telephone"
                            name="numero_telephone"
                            placeholder="0331234567"
                            value="0331234567"
                            value="<?= esc(old('numero_telephone') ?? '') ?>"
                            inputmode="numeric"
                            autocomplete="tel"
                            required
                        >
                         <div class="hint">Numero client 0331111111 ou 0332222222</div>
                        <div class="hint">Format attendu: 10 à 15 chiffres, sans espace ni séparateur.</div>
                    </div>
                    <div class="actions">
                        <button type="submit" class="primary">Accéder à mon espace</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
