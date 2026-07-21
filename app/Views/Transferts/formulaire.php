<?= $this->extend('layout/client') ?? '' ?>

<?php
$numero_client = isset($numero_client) && !is_array($numero_client) ? (string) $numero_client : '';
?>

<?= $this->section('content') ?>

<style>
    .transfert-page {
        padding: 32px 32px 48px;
        max-width: 600px;
        margin: 0 auto;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: var(--text);
    }

    .transfert-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
    }

    .transfert-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 28px 32px;
    }

    .transfert-header .eyebrow {
        font-size: 12px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.7);
        margin: 0 0 6px;
        font-weight: 600;
    }

    .transfert-header h1 {
        font-weight: 700;
        font-size: 26px;
        margin: 0 0 4px;
        letter-spacing: -0.01em;
    }

    .transfert-header .from-number {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.75);
    }

    .transfert-body {
        padding: 28px 32px 32px;
    }

    .info-banner,
    .flash {
        border-radius: var(--radius);
        padding: 12px 14px;
        font-size: 13.5px;
        margin-bottom: 18px;
        line-height: 1.45;
    }

    .info-banner {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }

    .flash.success {
        background: #dcfce7;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    .flash.error {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .field {
        margin-bottom: 20px;
    }

    .field label,
    .amount-field label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--text-secondary);
        margin-bottom: 8px;
    }

    .field input[type="tel"] {
        width: 100%;
        box-sizing: border-box;
        border: 1.5px solid var(--border);
        background: var(--bg);
        border-radius: var(--radius);
        padding: 13px 14px;
        font-size: 15px;
        font-family: 'IBM Plex Mono', monospace;
        color: var(--text);
        transition: var(--transition);
    }

    .field input[type="tel"]:focus,
    .amount-input-row input[type="number"]:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
    }

    .amount-field {
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg);
        padding: 16px 16px 12px;
        margin-bottom: 22px;
    }

    .amount-input-row {
        display: flex;
        align-items: baseline;
        gap: 8px;
    }

    .amount-input-row .currency {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 20px;
        color: var(--text-muted);
    }

    .amount-input-row input[type="number"] {
        flex: 1;
        border: none;
        appearance: textfield;
        -moz-appearance: textfield;
        font-size: 34px;
        font-weight: 700;
        color: var(--text);
        width: 100%;
        background: transparent;
        outline: none;
    }

    .amount-input-row input[type="number"]::-webkit-outer-spin-button,
    .amount-input-row input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .field-error {
        color: #dc2626;
        font-size: 12.5px;
        margin-top: 6px;
        font-family: 'IBM Plex Mono', monospace;
    }

    .frais-info {
        margin-top: 10px;
        font-size: 13px;
        font-family: 'IBM Plex Mono', monospace;
        color: var(--text-secondary);
        min-height: 18px;
    }

    .frais-info .frais-total {
        color: var(--accent);
        font-weight: 700;
    }

    .btn-transferer {
        width: 100%;
        border: none;
        border-radius: var(--radius);
        background: var(--primary);
        color: white;
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 15px;
        letter-spacing: 0.01em;
        padding: 15px;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-transferer:hover {
        background: var(--primary-dark);
    }

    .btn-transferer:active {
        transform: scale(0.98);
    }

    .lien-historique {
        display: block;
        text-align: center;
        margin-top: 18px;
        font-size: 13px;
        color: var(--text-secondary);
        text-decoration: none;
    }

    .lien-historique:hover {
        color: var(--primary);
    }

    .dashboard-link {
        color: #2563eb;
        font-weight: 700;
    }

    .promotion-badge {
        display: inline-block;
        background: #dcfce7;
        color: #16a34a;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        margin-bottom: 6px;
    }

    @media (max-width: 640px) {
        .transfert-page { padding: 16px; }
        .transfert-header h1 { font-size: 24px; }
    }
</style>

<div class="transfert-page">
    <div class="transfert-card">
        <div class="transfert-header">
            <p class="eyebrow"><i class="fas fa-paper-plane" style="margin-right: 6px;"></i>Nouvelle opération</p>
            <h1>Transférer de l'argent</h1>
            <div class="from-number"><i class="fas fa-phone" style="margin-right: 4px;"></i>Depuis le <?= esc($numero_client) ?></div>
        </div>

        <div class="transfert-body">
            <div class="info-banner">
                <i class="fas fa-info-circle" style="margin-right: 6px;"></i>Accédez au <a class="dashboard-link" href="<?= site_url('dashboard') ?>">dashboard</a> pour voir vos chiffres clés.
            </div>
            <div class="info-banner" style="background: #fef3c7; color: #b45309; border-color: #fde68a;">
                <i class="fas fa-exchange-alt" style="margin-right: 6px;"></i>Si le destinataire appartient à un autre opérateur, une commission additionnelle s'ajoute.
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash success"><i class="fas fa-check-circle" style="margin-right: 6px;"></i><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="flash error"><i class="fas fa-exclamation-circle" style="margin-right: 6px;"></i><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div style="text-align: right; margin-bottom: 14px;">
                <a href="<?= site_url('transfert/multiple') ?>" style="font-size: 13px; color: var(--primary); text-decoration: none; font-weight: 600;">
                    <i class="fas fa-users" style="margin-right: 4px;"></i>Envoi groupé →
                </a>
            </div>

            <form action="<?= site_url('transfert') ?>" method="post">
                <?= csrf_field() ?>

                <div class="field">
                    <label for="numero_destination"><i class="fas fa-phone" style="margin-right: 4px;"></i>Numéro du destinataire</label>
                    <input type="tel" id="numero_destination" name="numero_destination" placeholder="034 xx xxx xx" value="<?= esc(old('numero_destination')) ?>" inputmode="numeric" required>
                    <?php if (session('errors.numero_destination')): ?>
                        <div class="field-error"><?= esc(session('errors.numero_destination')) ?></div>
                    <?php endif; ?>
                </div>

                <div class="amount-field">
                    <label for="montant"><i class="fas fa-coins" style="margin-right: 4px;"></i>Montant à envoyer</label>
                    <div class="amount-input-row">
                        <span class="currency">Ar</span>
                        <input type="number" id="montant" name="montant" placeholder="0" min="1" step="1" value="<?= esc(old('montant')) ?>" required>
                    </div>
                    <?php if (session('errors.montant')): ?>
                        <div class="field-error"><?= esc(session('errors.montant')) ?></div>
                    <?php endif; ?>
                    <div class="frais-info" id="fraisInfo"></div>
                </div>

                <div class="field" style="margin-top: 18px; margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; font-weight: 500; color: var(--text); text-transform: none; letter-spacing: normal;">
                        <input type="checkbox" id="inclure_frais_retrait" name="inclure_frais_retrait" value="1" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);">
                        <span><i class="fas fa-receipt" style="margin-right: 4px;"></i>Inclure les frais de retrait dans le montant total</span>
                    </label>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px; margin-left: 28px;">
                        Si coché, les frais de retrait seront ajoutés au montant débité de votre compte
                    </div>
                </div>

                <button type="submit" class="btn-transferer"><i class="fas fa-paper-plane" style="margin-right: 6px;"></i>Confirmer le transfert</button>
            </form>

            <a href="<?= site_url('transfert/historique') ?>" class="lien-historique"><i class="fas fa-clock-rotate" style="margin-right: 4px;"></i>Voir l'historique des transferts →</a>
        </div>
    </div>
</div>

<script>
(function () {
    const montantInput = document.getElementById('montant');
    const numeroDestinationInput = document.getElementById('numero_destination');
    const fraisInfo = document.getElementById('fraisInfo');
    const urlCalculFrais = "<?= site_url('transfert/calculer-frais') ?>";
    const numeroSource = "<?= esc($numero_client) ?>";

    let timerId = null;

    function normaliserNumero(numero) {
        return (numero || '').replace(/\D+/g, '');
    }

    function calculerFrais() {
        const montant = parseFloat(montantInput.value);
        const numeroDestination = normaliserNumero(numeroDestinationInput.value);
        const inclureFraisRetrait = document.getElementById('inclure_frais_retrait').checked;

        clearTimeout(timerId);

        if (!montant || montant <= 0) {
            fraisInfo.textContent = '';
            return;
        }

        if (!numeroDestination || numeroDestination.length < 10) {
            fraisInfo.textContent = 'Saisissez le numéro destinataire pour calculer la commission exacte.';
            return;
        }

        fraisInfo.innerHTML = '<span class="frais-info">Calcul des frais…</span>';

        timerId = setTimeout(() => {
            const params = new URLSearchParams({
                montant: montant,
                numero_destination: numeroDestination,
                numero_source: normaliserNumero(numeroSource),
                inclure_frais_retrait: inclureFraisRetrait ? '1' : '0',
            });

            fetch(urlCalculFrais + '?' + params.toString(), {
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

                    const fraisBase = Number(data.frais_base || 0).toLocaleString('fr-FR');
                    const commission = Number(data.commission_supplementaire || 0).toLocaleString('fr-FR');
                    const fraisRetrait = Number(data.frais_retrait || 0).toLocaleString('fr-FR');
                    const total = Number(data.montant_total || 0).toLocaleString('fr-FR');

                    let message = '';
                    if (data.inter_operateur) {
                        message = 'Frais de transfert : ' + fraisBase + ' Ar' +
                                  ' — Commission opérateur destinataire : ' + commission + ' Ar';
                    } else {
                        message = 'Frais : ' + fraisBase + ' Ar';
                    }

                    if (data.inclure_frais_retrait && data.frais_retrait > 0) {
                        message += ' — Frais de retrait : ' + fraisRetrait + ' Ar';
                    }

                    message += ' — <span class="frais-total">Total débité : ' + total + ' Ar</span>';

                    // Ajouter le badge promotion si présent
                    if (data.promotion_message) {
                        message = '<span class="promotion-badge"><i class="fas fa-tag"></i> ' + data.promotion_message + '</span><br>' + message;
                    }

                    fraisInfo.innerHTML = message;
                })
                .catch((err) => {
                    console.error('Erreur calcul frais:', err);
                    fraisInfo.textContent = 'Erreur lors du calcul des frais. Veuillez vous reconnecter.';
                });
        }, 350);
    }

    montantInput.addEventListener('input', calculerFrais);
    numeroDestinationInput.addEventListener('input', calculerFrais);
    document.getElementById('inclure_frais_retrait').addEventListener('change', calculerFrais);
})();
</script>

<?= $this->endSection() ?>