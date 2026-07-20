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
        --line: #dcd6c7;
    }

    .bareme-form-page {
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

    .bareme-card {
        width: 100%;
        max-width: 600px;
        background: var(--paper);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 60px -20px rgba(0, 0, 0, 0.5);
    }

    .bareme-header {
        background: linear-gradient(160deg, var(--teal-800), #0c3835);
        color: var(--paper);
        padding: 28px;
    }

    .bareme-header .eyebrow {
        font-size: 12px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(246, 243, 236, 0.6);
        margin: 0 0 6px;
        font-family: 'IBM Plex Mono', monospace;
    }

    .bareme-header h1 {
        font-family: 'Fraunces', Georgia, serif;
        font-weight: 600;
        font-size: 26px;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .bareme-body {
        padding: 24px 28px 28px;
    }

    .flash.error {
        background: rgba(193, 69, 43, 0.1);
        color: var(--error);
        border: 1px solid rgba(193, 69, 43, 0.3);
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 13.5px;
        margin-bottom: 18px;
        line-height: 1.45;
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

    .field input,
    .field select {
        width: 100%;
        box-sizing: border-box;
        border: 1.5px solid var(--line);
        background: #fff;
        border-radius: 12px;
        padding: 13px 14px;
        font-size: 15px;
        color: var(--ink);
        transition: border-color 0.15s ease;
    }

    .field input:focus,
    .field select:focus {
        outline: none;
        border-color: var(--gold-dark);
        box-shadow: 0 0 0 3px rgba(217, 164, 65, 0.2);
    }

    .row-fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .field-error {
        color: var(--error);
        font-size: 12.5px;
        margin-top: 6px;
        font-family: 'IBM Plex Mono', monospace;
    }

    .btn-submit {
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
        margin-top: 10px;
    }

    .btn-submit:hover {
        background: var(--gold-dark);
    }

    .btn-submit:active {
        transform: scale(0.98);
    }

    .lien-retour {
        display: block;
        text-align: center;
        margin-top: 18px;
        font-size: 13px;
        color: var(--ink-soft);
        text-decoration: none;
    }

    .lien-retour:hover {
        color: var(--gold-dark);
        text-decoration: underline;
    }
</style>

<div class="bareme-form-page">
    <div class="bareme-card">
        <div class="bareme-header">
            <p class="eyebrow">Administration</p>
            <h1>Ajouter un barème de frais</h1>
        </div>

        <div class="bareme-body">
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="flash error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <form action="<?= site_url('admin/baremes/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="field">
                    <label for="type_operation_id">Type d'opération</label>
                    <select id="type_operation_id" name="type_operation_id" required>
                        <option value="">Sélectionnez un type d'opération</option>
                        <?php foreach ($types_operations as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= old('type_operation_id') == $type['id'] ? 'selected' : '' ?>>
                                <?= esc($type['libelle']) ?> (<?= esc($type['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.type_operation_id')): ?>
                        <div class="field-error"><?= esc(session('errors.type_operation_id')) ?></div>
                    <?php endif; ?>
                </div>

                <div class="row-fields">
                    <div class="field">
                        <label for="montant_min">Montant Minimum (Ar)</label>
                        <input
                            type="number"
                            id="montant_min"
                            name="montant_min"
                            placeholder="0"
                            min="0"
                            step="1"
                            value="<?= esc(old('montant_min') ?? '0') ?>"
                            required
                        >
                        <?php if (session('errors.montant_min')): ?>
                            <div class="field-error"><?= esc(session('errors.montant_min')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="montant_max">Montant Maximum (Ar)</label>
                        <input
                            type="number"
                            id="montant_max"
                            name="montant_max"
                            placeholder="999999999"
                            min="0"
                            step="1"
                            value="<?= esc(old('montant_max') ?? '999999999') ?>"
                            required
                        >
                        <div style="font-size: 11px; color: var(--ink-soft); margin-top: 4px;">
                            Utiliser 999999999 pour "Et plus".
                        </div>
                        <?php if (session('errors.montant_max')): ?>
                            <div class="field-error"><?= esc(session('errors.montant_max')) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row-fields">
                    <div class="field">
                        <label for="frais_fixe">Frais Fixe (Ar)</label>
                        <input
                            type="number"
                            id="frais_fixe"
                            name="frais_fixe"
                            placeholder="0"
                            min="0"
                            step="0.01"
                            value="<?= esc(old('frais_fixe') ?? '0') ?>"
                            required
                        >
                        <?php if (session('errors.frais_fixe')): ?>
                            <div class="field-error"><?= esc(session('errors.frais_fixe')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="frais_pourcentage">Frais Pourcentage (%)</label>
                        <input
                            type="number"
                            id="frais_pourcentage"
                            name="frais_pourcentage"
                            placeholder="0"
                            min="0"
                            max="100"
                            step="0.01"
                            value="<?= esc(old('frais_pourcentage') ?? '0') ?>"
                            required
                        >
                        <?php if (session('errors.frais_pourcentage')): ?>
                            <div class="field-error"><?= esc(session('errors.frais_pourcentage')) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row-fields">
                    <div class="field">
                        <label for="date_debut">Date de Début</label>
                        <input
                            type="datetime-local"
                            id="date_debut"
                            name="date_debut"
                            value="<?= esc(old('date_debut') ?? date('Y-m-d\TH:i')) ?>"
                        >
                        <?php if (session('errors.date_debut')): ?>
                            <div class="field-error"><?= esc(session('errors.date_debut')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="date_fin">Date de Fin (Optionnel)</label>
                        <input
                            type="datetime-local"
                            id="date_fin"
                            name="date_fin"
                            value="<?= esc(old('date_fin')) ?>"
                        >
                        <?php if (session('errors.date_fin')): ?>
                            <div class="field-error"><?= esc(session('errors.date_fin')) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Créer le barème</button>
            </form>

            <a href="<?= site_url('admin/baremes') ?>" class="lien-retour">← Retour à la liste</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
