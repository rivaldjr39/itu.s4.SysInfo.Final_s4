<?= $this->extend('layout/client') ?? '' ?>

<?php
$p = isset($promotion) && is_array($promotion) ? $promotion : [];
$id = (int) ($p['id'] ?? 0);
$pourcentage = (float) ($p['pourcentage_reduction'] ?? 0);
$dateDebut = $p['date_debut'] ?? date('Y-m-d\TH:i');
$dateFin = $p['date_fin'] ?? '';
$montantMin = (float) ($p['montant_min'] ?? 0);
$montantMax = (float) ($p['montant_max'] ?? 999999999.99);
$actif = (int) ($p['actif'] ?? 1);
?>

<?= $this->section('content') ?>

<style>
    .edit-page {
        padding: 32px 32px 48px;
        max-width: 600px;
        margin: 0 auto;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: var(--text);
    }

    .edit-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
    }

    .edit-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 28px 32px;
    }

    .edit-header .eyebrow {
        font-size: 12px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.7);
        margin: 0 0 6px;
        font-weight: 600;
    }

    .edit-header h1 {
        font-weight: 700;
        font-size: 26px;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .edit-body {
        padding: 28px 32px 32px;
    }

    .flash {
        border-radius: var(--radius);
        padding: 14px;
        margin-bottom: 18px;
        line-height: 1.45;
        font-size: 14px;
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

    form { display: grid; gap: 18px; }

    .form-group { display: grid; gap: 6px; }

    .form-group label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-secondary);
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg);
        font-size: 15px;
        color: var(--text);
        outline: none;
        transition: var(--transition);
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
    }

    .form-group .hint {
        font-size: 12px;
        color: var(--text-muted);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .actions-row {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 44px;
        padding: 0 20px;
        border-radius: 999px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        flex: 1;
    }

    .btn-action:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-secondary {
        border: 1px solid var(--border);
        background: transparent;
        color: var(--text);
        text-align: center;
    }

    .btn-secondary:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    @media (max-width: 640px) {
        .edit-page { padding: 16px; }
        .form-row { grid-template-columns: 1fr; }
        .actions-row { flex-direction: column; }
    }
</style>

<div class="edit-page">
    <div class="edit-card">
        <div class="edit-header">
            <p class="eyebrow"><i class="fas fa-tag" style="margin-right: 6px;"></i>Administration</p>
            <h1>Modifier la promotion</h1>
        </div>

        <div class="edit-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash success"><i class="fas fa-check-circle" style="margin-right: 6px;"></i><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="flash error"><i class="fas fa-exclamation-circle" style="margin-right: 6px;"></i><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?php if (session('errors')): ?>
                <div class="flash error"><i class="fas fa-exclamation-circle" style="margin-right: 6px;"></i>
                    <?php foreach (session('errors') as $error): ?>
                        <div><?= esc($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('admin/promotions/update/' . $id) ?>" method="post">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="pourcentage_reduction"><i class="fas fa-percent" style="margin-right: 4px;"></i>Pourcentage de réduction</label>
                    <input type="number" step="0.01" min="0.01" max="100" id="pourcentage_reduction" name="pourcentage_reduction" value="<?= $pourcentage ?>" required placeholder="Ex: 10">
                    <div class="hint">Les frais de transfert seront réduits de ce pourcentage (ex: 10 = -10%)</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_debut">Date début</label>
                        <input type="datetime-local" id="date_debut" name="date_debut" value="<?= esc(date('Y-m-d\TH:i', strtotime($dateDebut))) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="date_fin">Date fin (optionnelle)</label>
                        <input type="datetime-local" id="date_fin" name="date_fin" value="<?= $dateFin ? esc(date('Y-m-d\TH:i', strtotime($dateFin))) : '' ?>">
                        <div class="hint">Laissez vide pour illimité</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="montant_min">Montant minimum (Ar)</label>
                        <input type="number" step="1" min="0" id="montant_min" name="montant_min" value="<?= $montantMin ?>">
                    </div>
                    <div class="form-group">
                        <label for="montant_max">Montant maximum (Ar)</label>
                        <input type="number" step="1" min="1" id="montant_max" name="montant_max" value="<?= $montantMax >= 999999999 ? '' : $montantMax ?>" placeholder="Illimité">
                    </div>
                </div>

                <div class="form-group">
                    <label for="actif">Statut</label>
                    <select id="actif" name="actif">
                        <option value="1" <?= $actif === 1 ? 'selected' : '' ?>>Actif</option>
                        <option value="0" <?= $actif === 0 ? 'selected' : '' ?>>Inactif</option>
                    </select>
                </div>

                <div class="actions-row">
                    <a href="<?= site_url('admin/promotions') ?>" class="btn-action btn-secondary"><i class="fas fa-arrow-left"></i> Annuler</a>
                    <button type="submit" class="btn-action btn-primary"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>