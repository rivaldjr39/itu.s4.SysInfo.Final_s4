<?= $this->extend('layout/client') ?? '' ?>

<?= $this->section('content') ?>

<style>
    :root {
        --teal-950: #0b2e2b;
        --teal-800: #12433f;
        --paper: #f6f3ec;
        --ink: #1f2421;
        --ink-soft: #5c655f;
        --gold: #d9a441;
        --gold-dark: #b5842e;
        --error: #c1452b;
        --success: #4f7a5c;
        --line: #dcd6c7;
    }

    .retrait-page {
        min-height: calc(100vh - 72px);
        background:
            radial-gradient(circle at 15% 0%, rgba(217, 164, 65, 0.10), transparent 45%),
            radial-gradient(circle at 85% 100%, rgba(217, 164, 65, 0.08), transparent 50%),
            var(--teal-950);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 16px 48px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: var(--ink);
    }

    .retrait-card {
        width: 100%;
        max-width: 520px;
        background: var(--paper);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 60px -20px rgba(0, 0, 0, 0.5);
    }

    .retrait-header {
        background: linear-gradient(160deg, var(--teal-800), #0c3835);
        color: var(--paper);
        padding: 28px;
    }

    .retrait-header .eyebrow {
        font-size: 12px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(246, 243, 236, 0.6);
        margin: 0 0 6px;
        font-family: 'IBM Plex Mono', monospace;
    }

    .retrait-header h1 {
        font-family: 'Fraunces', Georgia, serif;
        font-weight: 600;
        font-size: 26px;
        margin: 0 0 4px;
        letter-spacing: -0.01em;
    }

    .retrait-header .from-number {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        color: rgba(246, 243, 236, 0.75);
    }

    .retrait-body {
        padding: 24px 28px 28px;
    }

    .info-banner,
    .flash {
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 13.5px;
        margin-bottom: 18px;
        line-height: 1.45;
    }

    .info-banner {
        background: rgba(79, 122, 92, 0.12);
        color: var(--success);
        border: 1px solid rgba(79, 122, 92, 0.3);
    }

    .flash.success {
        background: rgba(79, 122, 92, 0.12);
        color: var(--success);
        border: 1px solid rgba(79, 122, 92, 0.3);
    }

    .flash.error {
        background: rgba(193, 69, 43, 0.1);
        color: var(--error);
        border: 1px solid rgba(193, 69, 43, 0.3);
    }

    .amount-field {
        border: 1.5px solid var(--line);
        border-radius: 14px;
        background: #fff;
        padding: 16px 16px 12px;
        margin-bottom: 22px;
    }

    .amount-field label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--ink-soft);
        margin-bottom: 8px;
    }

    .amount-input-row {
        display: flex;
        align-items: baseline;
        gap: 8px;
    }

    .amount-input-row .currency {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 20px;
        color: var(--ink-soft);
    }

    .amount-input-row input[type="number"] {
        flex: 1;
        border: none;
        appearance: textfield;
        -moz-appearance: textfield;
        font-family: 'Fraunces', Georgia, serif;
        font-size: 34px;
        font-weight: 600;
        color: var(--ink);
        width: 100%;
        background: transparent;
    }

    .amount-input-row input[type="number"]::-webkit-outer-spin-button,
    .amount-input-row input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .field-error {
        color: var(--error);
        font-size: 12.5px;
        margin-top: 6px;
        font-family: 'IBM Plex Mono', monospace;
    }

    .frais-info {
        margin-top: 10px;
        font-size: 13px;
        font-family: 'IBM Plex Mono', monospace;
        color: var(--ink-soft);
        min-height: 18px;
    }

    .frais-info .frais-total {
        color: var(--gold-dark);
        font-weight: 600;
    }

    .btn-retirer {
        width: 100%;
        border: none;
        border-radius: 12px;
        background: var(--gold);
        color: var(--teal-950);
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 15px;
        letter-spacing: 0.01em;
        padding: 15px;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.1s ease;
    }

    .btn-retirer:hover {
        background: var(--gold-dark);
    }

    .btn-retirer:active {
        transform: scale(0.98);
    }

    .lien-historique {
        display: block;
        text-align: center;
        margin-top: 18px;
        font-size: 13px;
        color: var(--ink-soft);
        text-decoration: none;
    }

    .lien-historique:hover {
        color: var(--gold-dark);
        text-decoration: underline;
    }

    .dashboard-link {
        color: inherit;
        font-weight: 700;
    }

    @media (max-width: 640px) {
        .retrait-page {
            padding: 16px;
        }

        .retrait-header h1 {
            font-size: 24px;
        }
    }
</style>

<div class="retrait-page">
    <div class="retrait-card">
        <div class="retrait-header">
            <p class="eyebrow">Nouvelle opération</p>
            <h1>Retirer de l'argent</h1>
            <div class="from-number">Depuis le compte <?= esc($numero_client) ?></div>
        </div>

        <div class="retrait-body">
            <div class="info-banner">
                Accédez au <a class="dashboard-link" href="<?= site_url('dashboard') ?>">dashboard</a> pour voir vos chiffres clés avant d'effectuer un retrait.
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="flash error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <form action="<?= site_url('retrait') ?>" method="post">
                <?= csrf_field() ?>

                <div class="amount-field">
                    <label for="montant">Montant à retirer</label>
                    <div class="amount-input-row">
                        <span class="currency">Ar</span>
                        <input
                            type="number"
                            id="montant"
                            name="montant"
                            placeholder="0"
                            min="1"
                            step="1"
                            value="<?= esc(old('montant')) ?>"
                            required
                        >
                    </div>
                    <?php if (session('errors.montant')): ?>
                        <div class="field-error"><?= esc(session('errors.montant')) ?></div>
                    <?php endif; ?>
                    <div class="frais-info" id="fraisInfo"></div>
                </div>

                <button type="submit" class="btn-retirer">Confirmer le retrait</button>
            </form>

            <a href="<?= site_url('retrait/historique') ?>" class="lien-historique">Voir l'historique des retraits →</a>
        </div>
    </div>
</div>

<script>
(function () {
    const montantInput = document.getElementById('montant');
    const fraisInfo = document.getElementById('fraisInfo');
    const urlCalculFrais = "<?= site_url('retrait/calculer-frais') ?>";

    let timerId = null;

    montantInput.addEventListener('input', function () {
        const montant = parseFloat(montantInput.value);

        clearTimeout(timerId);

        if (!montant || montant <= 0) {
            fraisInfo.textContent = '';
            return;
        }

        fraisInfo.textContent = 'Calcul des frais…';

        timerId = setTimeout(() => {
            fetch(urlCalculFrais + '?montant=' + encodeURIComponent(montant), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then((res) => {
                    if (!res.ok) throw new Error('Requête invalide');
                    return res.json();
                })
                .then((data) => {
                    if (!data.success) {
                        fraisInfo.textContent = data.message || 'Impossible de calculer les frais.';
                        return;
                    }
                    fraisInfo.innerHTML =
                        'Frais de retrait : ' + data.frais.toLocaleString('fr-FR') + ' Ar' +
                        ' — <span class="frais-total">Total débité : ' +
                        data.montant_total.toLocaleString('fr-FR') + ' Ar</span>';
                })
                .catch(() => {
                    fraisInfo.textContent = 'Impossible de calculer les frais.';
                });
        }, 350);
    });
})();
</script>

<?= $this->endSection() ?>
