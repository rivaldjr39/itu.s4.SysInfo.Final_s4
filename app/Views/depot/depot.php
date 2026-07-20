<?= $this->extend('layout/client') ?>

<?php
$client = $client ?? [];
$numeroClient = $client['numero_client'] ?? session()->get('numero_telephone') ?? '';
$clientNom = $client['client_nom'] ?? session()->get('client_nom') ?? '';
$solde = isset($solde) && is_numeric($solde) ? (float) $solde : null;
?>

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

    .depot-page {
        min-height: calc(100vh - 72px);
        background:
            radial-gradient(circle at 15% 0%, rgba(217, 164, 65, 0.10), transparent 45%),
            radial-gradient(circle at 85% 100%, rgba(217, 164, 65, 0.08), transparent 50%),
            var(--teal-950);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 16px 48px;
        font-family: Inter, -apple-system, BlinkMacSystemFont, sans-serif;
        color: var(--ink);
    }

    .depot-card {
        width: 100%;
        max-width: 560px;
        background: var(--paper);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 60px -20px rgba(0, 0, 0, 0.5);
    }

    .depot-header {
        background: linear-gradient(160deg, var(--teal-800), #0c3835);
        color: var(--paper);
        padding: 28px;
    }

    .depot-header .eyebrow {
        font-size: 12px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(246, 243, 236, 0.6);
        margin: 0 0 6px;
        font-family: monospace;
    }

    .depot-header h1 {
        font-size: 28px;
        margin: 0 0 6px;
        letter-spacing: -0.02em;
    }

    .depot-header .meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 16px;
        font-size: 13px;
        color: rgba(246, 243, 236, 0.78);
    }

    .depot-body {
        padding: 24px 28px 28px;
    }

    .flash {
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 13.5px;
        margin-bottom: 18px;
        line-height: 1.45;
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

    .balance-box {
        display: grid;
        gap: 4px;
        padding: 14px 16px;
        border-radius: 14px;
        background: rgba(217, 164, 65, 0.12);
        border: 1px solid rgba(217, 164, 65, 0.28);
        margin-bottom: 18px;
    }

    .balance-box .label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--ink-soft);
    }

    .balance-box .amount {
        font-size: 26px;
        font-weight: 800;
    }

    form {
        display: grid;
        gap: 16px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--ink-soft);
    }

    input[type="number"] {
        width: 100%;
        border: 1.5px solid var(--line);
        border-radius: 14px;
        padding: 16px;
        font-size: 18px;
        font-weight: 700;
        color: var(--ink);
        background: #fff;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    input[type="number"]:focus {
        border-color: var(--gold-dark);
        box-shadow: 0 0 0 4px rgba(217, 164, 65, 0.18);
        transform: translateY(-1px);
    }

    .hint {
        margin-top: 8px;
        color: var(--ink-soft);
        font-size: 13px;
        line-height: 1.45;
    }

    .frais-info {
        min-height: 20px;
        font-size: 13px;
        color: var(--ink-soft);
        font-family: monospace;
    }

    .frais-info .frais-total {
        color: var(--gold-dark);
        font-weight: 700;
    }

    .actions {
        display: grid;
        gap: 10px;
        margin-top: 6px;
    }

    .btn-primary,
    .btn-secondary {
        border-radius: 14px;
        padding: 15px 18px;
        font-size: 15px;
        font-weight: 800;
        text-decoration: none;
        text-align: center;
        transition: transform .12s ease, background-color .12s ease, border-color .12s ease;
    }

    .btn-primary {
        border: 0;
        background: var(--gold);
        color: #1d241f;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: var(--gold-dark);
    }

    .btn-secondary {
        background: transparent;
        border: 1px solid var(--line);
        color: var(--ink);
    }

    .btn-secondary:hover {
        border-color: var(--gold-dark);
    }

    @media (max-width: 640px) {
        .depot-header,
        .depot-body {
            padding-left: 20px;
            padding-right: 20px;
        }

        .depot-header h1 {
            font-size: 24px;
        }
    }
</style>

<div class="depot-page">
    <section class="depot-card">
        <header class="depot-header">
            <p class="eyebrow">Opérateur mobile money</p>
            <h1>Dépôt d'argent</h1>
            <div class="meta">
                <span>Client : <?= esc($clientNom ?: $numeroClient) ?></span>
                <span>Numéro : <?= esc($numeroClient) ?></span>
            </div>
        </header>

        <div class="depot-body">
            <?php $successMessage = session()->getFlashdata('success'); ?>
            <?php $errorMessage = session()->getFlashdata('error'); ?>

            <?php if ($successMessage): ?>
                <div class="flash success"><?= esc($successMessage) ?></div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                <div class="flash error"><?= esc($errorMessage) ?></div>
            <?php endif; ?>

            <div class="balance-box">
                <span class="label">Solde actuel</span>
                <span class="amount"><?= $solde !== null ? number_format($solde, 0, ',', ' ') . ' Ar' : '0 Ar' ?></span>
            </div>

            <form action="<?= site_url('depot') ?>" method="post">
                <?= csrf_field() ?>

                <div>
                    <label for="montant">Montant à déposer</label>
                    <input type="number" id="montant" name="montant" min="1" step="1" placeholder="Ex. 50000" value="<?= old('montant') ?>" required>
                    <div class="hint">Le dépôt crédite le compte du client et applique automatiquement les frais du barème.</div>
                </div>

                <div class="frais-info" id="fraisInfo">Saisissez un montant pour afficher les frais estimés.</div>

                <div class="actions">
                    <button class="btn-primary" type="submit">Valider le dépôt</button>
                    <a class="btn-secondary" href="<?= site_url('dashboard') ?>">Retour au dashboard</a>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
(() => {
    const montantInput = document.getElementById('montant');
    const fraisInfo = document.getElementById('fraisInfo');
    const urlCalculFrais = "<?= site_url('depot/calculer-frais') ?>";

    const formatter = new Intl.NumberFormat('fr-FR');

    const afficherMessage = (message) => {
        fraisInfo.textContent = message;
    };

    const calculerFrais = async () => {
        const montant = parseFloat(montantInput.value || '0');

        if (!montant || montant <= 0) {
            afficherMessage('Saisissez un montant pour afficher les frais estimés.');
            return;
        }

        afficherMessage('Calcul des frais…');

        try {
            const response = await fetch(urlCalculFrais + '?montant=' + encodeURIComponent(montant), {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (!data.success) {
                afficherMessage(data.message || 'Impossible de calculer les frais.');
                return;
            }

            fraisInfo.innerHTML =
                'Frais : ' + formatter.format(data.frais) + ' Ar' +
                ' — <span class="frais-total">Total à payer : ' + formatter.format(data.montant_total) + ' Ar</span>';
        } catch (error) {
            afficherMessage('Impossible de calculer les frais.');
        }
    };

    montantInput.addEventListener('input', calculerFrais);

    if (montantInput.value) {
        calculerFrais();
    }
})();
</script>

<?= $this->endSection() ?>