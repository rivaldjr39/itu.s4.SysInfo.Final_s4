<?= $this->extend('layout/client') ?? '' ?>

<?php
$numero_client = isset($numero_client) && !is_array($numero_client) ? (string) $numero_client : '';
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

    .transfert-page {
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

    .transfert-card {
        width: 100%;
        max-width: 600px;
        background: var(--paper);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 60px -20px rgba(0, 0, 0, 0.5);
    }

    .transfert-header {
        background: linear-gradient(160deg, var(--teal-800), #0c3835);
        color: var(--paper);
        padding: 28px;
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

    .field input[type="tel"] {
        width: 100%;
        box-sizing: border-box;
        border: 1.5px solid var(--line);
        background: #fff;
        border-radius: 12px;
        padding: 13px 14px;
        font-size: 15px;
        font-family: 'IBM Plex Mono', monospace;
        color: var(--ink);
        transition: border-color 0.15s ease;
    }

    .field input[type="tel"]:focus,
    .amount-input-row input[type="number"]:focus {
        outline: none;
        border-color: var(--gold-dark);
        box-shadow: 0 0 0 3px rgba(217, 164, 65, 0.2);
    }

    .recipient-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .recipient-row .recipient-index {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        font-weight: 600;
        color: var(--ink-soft);
        min-width: 24px;
        text-align: center;
    }

    .recipient-row .field {
        flex: 1;
        margin-bottom: 0;
    }

    .recipient-row .btn-remove {
        background: none;
        border: 1.5px solid var(--error);
        color: var(--error);
        border-radius: 8px;
        width: 36px;
        height: 36px;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s ease, color 0.15s ease;
        flex-shrink: 0;
    }

    .recipient-row .btn-remove:hover {
        background: var(--error);
        color: #fff;
    }

    .btn-add {
        background: none;
        border: 1.5px dashed var(--ink-soft);
        color: var(--ink-soft);
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 13px;
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        transition: border-color 0.15s ease, color 0.15s ease;
        margin-bottom: 20px;
    }

    .btn-add:hover {
        border-color: var(--gold-dark);
        color: var(--gold-dark);
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

    .info-box {
        background: rgba(79, 122, 92, 0.08);
        border: 1px solid rgba(79, 122, 92, 0.2);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 20px;
        font-size: 13px;
        color: var(--ink-soft);
        line-height: 1.5;
        font-family: 'IBM Plex Mono', monospace;
    }

    .info-box strong {
        color: var(--ink);
    }

    .info-box .highlight {
        color: var(--gold-dark);
        font-weight: 600;
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

    .back-link {
        display: inline-block;
        margin-bottom: 16px;
        font-size: 13px;
        color: var(--ink-soft);
        text-decoration: none;
        font-family: 'IBM Plex Mono', monospace;
    }

    .back-link:hover {
        color: var(--gold-dark);
    }

    @media (max-width: 640px) {
        .transfert-page {
            padding: 16px;
        }

        .transfert-header h1 {
            font-size: 24px;
        }
    }
</style>

<div class="transfert-page">
    <div class="transfert-card">
        <div class="transfert-header">
            <p class="eyebrow">Nouvelle opération</p>
            <h1>Envoi groupé</h1>
            <div class="from-number">Depuis le <?= esc($numero_client) ?></div>
        </div>

        <div class="transfert-body">
            <a href="<?= site_url('transfert') ?>" class="back-link">← Transfert simple</a>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="flash error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="info-box">
                Le montant total saisi sera <strong>divisé équitablement</strong> entre tous les bénéficiaires.<br>
                Les frais sont calculés <span class="highlight">par bénéficiaire</span>.
            </div>

            <form action="<?= site_url('transfert/multiple') ?>" method="post" id="multipleForm">
                <?= csrf_field() ?>

                <div class="field">
                    <label>Bénéficiaires</label>
                    <div id="recipientsContainer">
                        <!-- Les lignes de destinataires seront insérées ici par JS -->
                    </div>
                    <button type="button" class="btn-add" id="addRecipientBtn">+ Ajouter un bénéficiaire</button>
                </div>

                <div class="amount-field">
                    <label for="montant_total">Montant total à répartir</label>
                    <div class="amount-input-row">
                        <span class="currency">Ar</span>
                        <input
                            type="number"
                            id="montant_total"
                            name="montant_total"
                            placeholder="0"
                            min="1"
                            step="1"
                            value="<?= esc(old('montant_total')) ?>"
                            required
                        >
                    </div>
                    <div class="info-box" id="repartitionInfo" style="margin-top: 12px; display: none;">
                        <span id="repartitionText"></span>
                    </div>
                </div>

                <button type="submit" class="btn-transferer">Confirmer l'envoi groupé</button>
            </form>

            <a href="<?= site_url('transfert/historique') ?>" class="lien-historique">Voir l'historique des transferts →</a>
        </div>
    </div>
</div>

<script>
(function () {
    const container = document.getElementById('recipientsContainer');
    const addBtn = document.getElementById('addRecipientBtn');
    const montantInput = document.getElementById('montant_total');
    const repartitionInfo = document.getElementById('repartitionInfo');
    const repartitionText = document.getElementById('repartitionText');
    const urlCalculFrais = "<?= site_url('transfert/calculer-frais') ?>";

    let recipientCount = 0;

    // Initialiser avec 2 champs par défaut
    function initRecipients() {
        for (let i = 0; i < 2; i++) {
            addRecipientField();
        }
    }

    function addRecipientField() {
        recipientCount++;
        const index = recipientCount;

        const row = document.createElement('div');
        row.className = 'recipient-row';
        row.dataset.index = index;

        const indexSpan = document.createElement('span');
        indexSpan.className = 'recipient-index';
        indexSpan.textContent = index + '.';

        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'field';

        const input = document.createElement('input');
        input.type = 'tel';
        input.name = 'numeros[]';
        input.placeholder = '034 xx xxx xx';
        input.inputMode = 'numeric';
        input.required = true;
        input.dataset.index = index;
        input.addEventListener('input', function () {
            validateOperateurs();
        });

        fieldDiv.appendChild(input);

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn-remove';
        removeBtn.innerHTML = '×';
        removeBtn.title = 'Supprimer ce bénéficiaire';
        removeBtn.addEventListener('click', function () {
            row.remove();
            updateIndices();
            updateRepartition();
        });

        row.appendChild(indexSpan);
        row.appendChild(fieldDiv);
        row.appendChild(removeBtn);

        container.appendChild(row);
    }

    function updateIndices() {
        const rows = container.querySelectorAll('.recipient-row');
        rows.forEach((row, idx) => {
            const newIndex = idx + 1;
            row.dataset.index = newIndex;
            const indexSpan = row.querySelector('.recipient-index');
            if (indexSpan) indexSpan.textContent = newIndex + '.';
            const input = row.querySelector('input');
            if (input) input.dataset.index = newIndex;
        });
        recipientCount = rows.length;
    }

    function validateOperateurs() {
        const rows = container.querySelectorAll('.recipient-row');
        const numeros = [];
        const operateurs = new Set();

        rows.forEach(function(row) {
            const input = row.querySelector('input');
            if (input && input.value.trim()) {
                const numero = input.value.trim();
                const prefixe = numero.substring(0, 3);
                if (prefixe.length === 3) {
                    operateurs.add(prefixe);
                }
                numeros.push(numero);
            }
        });

        // Vérifier que tous les numéros ont le même opérateur
        if (operateurs.size > 1) {
            const message = 'Erreur : Tous les numéros doivent avoir le même opérateur (' + Array.from(operateurs).join(', ') + ').';
            if (!document.getElementById('operateurError')) {
                const errorDiv = document.createElement('div');
                errorDiv.id = 'operateurError';
                errorDiv.className = 'flash error';
                errorDiv.style.marginBottom = '18px';
                errorDiv.textContent = message;
                container.parentNode.insertBefore(errorDiv, container);
            } else {
                document.getElementById('operateurError').textContent = message;
            }
        } else if (document.getElementById('operateurError')) {
            document.getElementById('operateurError').remove();
        }
    }

    function updateRepartition() {
        const montantTotal = parseFloat(montantInput.value);
        const rows = container.querySelectorAll('.recipient-row');
        const count = rows.length;

        if (!montantTotal || montantTotal <= 0 || count < 2) {
            repartitionInfo.style.display = 'none';
            return;
        }

        const montantParPersonne = Math.floor(montantTotal / count);
        const reste = montantTotal - (montantParPersonne * count);

        repartitionText.innerHTML =
            'Répartition : <strong>' + count + '</strong> bénéficiaire' + (count > 1 ? 's' : '') +
            ' × <strong>' + montantParPersonne.toLocaleString('fr-FR') + ' Ar</strong>' +
            (reste > 0 ? ' (reste ' + reste + ' Ar non distribué)' : '') +
            ' chacun';

        repartitionInfo.style.display = 'block';

        // Calculer les frais pour le montant par personne
        if (montantParPersonne > 0) {
            fetch(urlCalculFrais + '?montant=' + encodeURIComponent(montantParPersonne), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const fraisTotal = data.frais * count;
                        const totalDebite = (montantParPersonne + data.frais) * count;
                        repartitionText.innerHTML +=
                            '<br>Frais : <span class="highlight">' + data.frais.toLocaleString('fr-FR') + ' Ar</span> par transfert' +
                            ' — <strong>Total débité : ' + totalDebite.toLocaleString('fr-FR') + ' Ar</strong>';
                    }
                })
                .catch(() => {});
        }
    }

    addBtn.addEventListener('click', addRecipientField);

    montantInput.addEventListener('input', function () {
        updateRepartition();
    });

    // Observer les changements dans le container (ajout/suppression)
    const observer = new MutationObserver(function () {
        updateRepartition();
    });
    observer.observe(container, { childList: true });

    // Initialiser
    initRecipients();
})();
</script>

<?= $this->endSection() ?>