<?= $this->extend('layout/client') ?? '' ?>

<?php /*
  Si tu n'as pas encore de layout('layout/client'), retire la ligne $this->extend
  ci-dessus et la ligne $this->endSection() en bas — ce fichier est autonome
  et fonctionne aussi comme page complète (voir version standalone plus bas
  si besoin).
*/ ?>

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

    .transfert-page {
        min-height: 100vh;
        background: var(--teal-950);
        background-image:
            radial-gradient(circle at 15% 0%, rgba(217, 164, 65, 0.10), transparent 45%),
            radial-gradient(circle at 85% 100%, rgba(217, 164, 65, 0.08), transparent 50%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 16px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: var(--ink);
    }

    .transfert-card {
        width: 100%;
        max-width: 420px;
        background: var(--paper);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 60px -20px rgba(0, 0, 0, 0.5);
    }

    .transfert-header {
        background: var(--teal-800);
        color: var(--paper);
        padding: 28px 28px 22px;
        position: relative;
    }

    .transfert-header .eyebrow {
        font-size: 12px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(246, 243, 236, 0.6);
        margin: 0 0 6px;
        font-family: 'IBM Plex Mono', monospace;
    }

    .transfert-header h1 {
        font-family: 'Fraunces', Georgia, serif;
        font-weight: 600;
        font-size: 26px;
        margin: 0 0 4px;
        letter-spacing: -0.01em;
    }

    .transfert-header .from-number {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        color: rgba(246, 243, 236, 0.75);
    }

    .transfert-body {
        padding: 26px 28px 28px;
    }

    .flash {
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 13.5px;
        margin-bottom: 18px;
        line-height: 1.4;
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

    .field {
        margin-bottom: 20px;
    }

    .field label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--ink-soft);
        margin-bottom: 8px;
    }

    .field input[type="text"],
    .field input[type="tel"] {
        width: 100%;
        box-sizing: border-box;
        border: 1.5px solid var(--line);
        background: #fff;
        border-radius: 10px;
        padding: 13px 14px;
        font-size: 15px;
        font-family: 'IBM Plex Mono', monospace;
        color: var(--ink);
        transition: border-color 0.15s ease;
    }

    .field input:focus {
        outline: none;
        border-color: var(--gold-dark);
        box-shadow: 0 0 0 3px rgba(217, 164, 65, 0.2);
    }

    /* Le montant est le geste signature de la page : grand, façon compteur */
    .amount-field {
        border: 1.5px solid var(--line);
        border-radius: 12px;
        background: #fff;
        padding: 16px 16px 12px;
        margin-bottom: 22px;
    }

    .amount-field label {
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--ink-soft);
        margin-bottom: 6px;
        display: block;
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
        outline: none;
        font-family: 'Fraunces', Georgia, serif;
        font-size: 34px;
        font-weight: 600;
        color: var(--ink);
        width: 100%;
        background: transparent;
        -moz-appearance: textfield;
    }

    .amount-input-row input[type="number"]::-webkit-outer-spin-button,
    .amount-input-row input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .amount-input-row input[type="number"]:focus {
        color: var(--gold-dark);
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
        opacity: 0;
        transform: translateY(-2px);
        transition: opacity 0.15s ease, transform 0.15s ease;
    }

    .frais-info.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .frais-info .frais-total {
        color: var(--gold-dark);
        font-weight: 600;
    }

    .frais-info.loading {
        color: var(--ink-soft);
        font-style: italic;
    }

    .frais-info.error {
        color: var(--error);
    }

    @media (prefers-reduced-motion: reduce) {
        .frais-info {
            transition: none;
        }
    }

    .btn-transferer {
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

    .btn-transferer:hover {
        background: var(--gold-dark);
    }

    .btn-transferer:active {
        transform: scale(0.98);
    }

    .btn-transferer:focus-visible {
        outline: 3px solid var(--teal-800);
        outline-offset: 2px;
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

    @media (prefers-reduced-motion: reduce) {
        .btn-transferer {
            transition: none;
        }
    }
</style>

<div class="transfert-page">
    <div class="transfert-card">
        <div class="transfert-header">
            <p class="eyebrow">Nouvelle opération</p>
            <h1>Transférer de l'argent</h1>
            <div class="from-number">Depuis le <?= esc($numero_client) ?></div>
        </div>

        <div class="transfert-body">

            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="flash error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <form action="<?= site_url('transfert') ?>" method="post">
                <?= csrf_field() ?>

                <div class="field">
                    <label for="numero_destination">Numéro du destinataire</label>
                    <input
                        type="tel"
                        id="numero_destination"
                        name="numero_destination"
                        placeholder="034 xx xxx xx"
                        value="<?= esc(old('numero_destination')) ?>"
                        inputmode="numeric"
                        required
                    >
                    <?php if (session('errors.numero_destination')): ?>
                        <div class="field-error"><?= esc(session('errors.numero_destination')) ?></div>
                    <?php endif; ?>
                </div>

                <div class="amount-field">
                    <label for="montant">Montant à envoyer</label>
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

                <button type="submit" class="btn-transferer">Confirmer le transfert</button>
            </form>

            <a href="<?= site_url('transfert/historique') ?>" class="lien-historique">Voir l'historique des transferts →</a>
        </div>
    </div>
</div>

<script>
(function () {
    const montantInput = document.getElementById('montant');
    const fraisInfo = document.getElementById('fraisInfo');
    const urlCalculFrais = "<?= site_url('transfert/calculer-frais') ?>";

    let timerId = null;

    montantInput.addEventListener('input', function () {
        const montant = parseFloat(montantInput.value);

        clearTimeout(timerId);

        if (!montant || montant <= 0) {
            fraisInfo.classList.remove('visible', 'loading', 'error');
            fraisInfo.textContent = '';
            return;
        }

        fraisInfo.classList.add('visible', 'loading');
        fraisInfo.classList.remove('error');
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
                    fraisInfo.classList.remove('loading', 'error');
                    if (!data.success) {
                        fraisInfo.classList.add('error');
                        fraisInfo.textContent = data.message || 'Impossible de calculer les frais.';
                        return;
                    }
                    fraisInfo.innerHTML =
                        'Frais : ' + data.frais.toLocaleString('fr-FR') + ' Ar' +
                        ' — <span class="frais-total">Total débité : ' +
                        data.montant_total.toLocaleString('fr-FR') + ' Ar</span>';
                })
                .catch(() => {
                    fraisInfo.classList.remove('loading');
                    fraisInfo.classList.add('error');
                    fraisInfo.textContent = 'Impossible de calculer les frais.';
                });
        }, 350);
    });
})();
</script>

<?= $this->endSection() ?>