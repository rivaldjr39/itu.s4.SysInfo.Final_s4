<?= $this->extend('layout/client') ?? '' ?>

<?= $this->section('content') ?>

<style>
    :root {
        --primary: #0d9488;
        --primary-dark: #0f766e;
        --primary-light: #ccfbf1;
        --accent: #d97706;
        --accent-light: #fef3c7;
        --bg: #f1f5f9;
        --bg-card: #ffffff;
        --text: #1e293b;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --border: #e2e8f0;
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.06), 0 1px 2px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.04);
        --radius: 12px;
        --radius-lg: 16px;
        --success: #16a34a;
        --error: #dc2626;
    }

    .promotions-page {
        padding: 32px 32px 48px;
        max-width: 1100px;
        margin: 0 auto;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: var(--text);
    }

    .promotions-shell {
        display: grid;
        gap: 20px;
    }

    .promotions-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-lg);
        padding: 28px 32px;
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .promotions-header h1 {
        margin: 0;
        font-size: clamp(24px, 3vw, 36px);
        font-weight: 700;
        color: white;
    }

    .promotions-header p {
        margin: 8px 0 0;
        color: rgba(255, 255, 255, 0.85);
        font-size: 15px;
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
    }

    .btn-action:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        background: white;
        color: var(--primary);
    }

    .btn-primary:hover {
        background: #f1f5f9;
    }

    .btn-secondary {
        border: 1px solid var(--border);
        background: var(--bg-card);
        color: var(--text);
    }

    .btn-secondary:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .btn-danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-danger:hover {
        background: #fecaca;
    }

    .panel-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        padding: 24px;
        border: 1px solid var(--border);
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }

    th {
        padding: 14px 16px;
        border-bottom: 2px solid var(--border);
        color: var(--text);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.05em;
    }

    td {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        color: var(--text);
    }

    tr:hover td {
        background: var(--bg);
    }

    .promo-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        background: #fef2f2;
        color: #dc2626;
    }

    .promo-badge.active {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .status-active {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-inactive {
        background: #fee2e2;
        color: #dc2626;
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

    .actions-cell {
        display: flex;
        gap: 8px;
    }

    .empty-state {
        text-align: center;
        padding: 48px 16px;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 48px;
        color: var(--text-muted);
        margin-bottom: 16px;
    }

    .empty-state strong {
        display: block;
        font-size: 20px;
        color: var(--text);
        margin-bottom: 8px;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 100;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.open {
        display: flex;
    }

    .modal {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        padding: 28px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 28px 72px rgba(0,0,0,0.35);
    }

    .modal h2 {
        margin: 0 0 18px;
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
    }

    .form-group {
        margin-bottom: 14px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg);
        font-size: 14px;
        color: var(--text);
        outline: none;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .promotions-page { padding: 16px; }
        .promotions-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 24px;
        }
        .btn-action { width: 100%; }
    }
</style>

<div class="promotions-page">
    <div class="promotions-shell">
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

        <section class="promotions-header">
            <div>
                <h1><i class="fas fa-percent" style="margin-right: 10px;"></i>Promotions sur transferts</h1>
                <p>Gérez les réductions appliquées sur les frais de transfert vers le même opérateur.</p>
            </div>
            <button class="btn-action btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> Ajouter une promotion</button>
        </section>

        <section class="panel-card">
            <?php if (empty($promotions)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <strong>Aucune promotion configurée</strong>
                    <p>Ajoutez une promotion pour réduire les frais de transfert entre clients du même opérateur.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Réduction</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Montant min</th>
                            <th>Montant max</th>
                            <th>Statut</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $now = time();
                        foreach ($promotions as $p): 
                            $debut = strtotime($p['date_debut']);
                            $fin = $p['date_fin'] ? strtotime($p['date_fin']) : null;
                            $estActive = $p['actif'] && $debut <= $now && (!$fin || $fin > $now);
                        ?>
                            <tr>
                                <td>
                                    <span class="promo-badge <?= $estActive ? 'active' : '' ?>">
                                        <i class="fas fa-tag"></i> -<?= number_format((float) $p['pourcentage_reduction'], 0) ?>%
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', $debut) ?></td>
                                <td><?= $fin ? date('d/m/Y', $fin) : '<span style="color: var(--text-muted);">—</span>' ?></td>
                                <td><?= number_format((float) $p['montant_min'], 0, ',', ' ') ?> Ar</td>
                                <td>
                                    <?php if ((float) $p['montant_max'] >= 999999999): ?>
                                        Et plus
                                    <?php else: ?>
                                        <?= number_format((float) $p['montant_max'], 0, ',', ' ') ?> Ar
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-pill <?= $p['actif'] ? 'status-active' : 'status-inactive' ?>">
                                        <?= $p['actif'] ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <button class="btn-action btn-secondary" style="min-height: 32px; padding: 0 12px; font-size: 12px;" 
                                            onclick="editPromotion(<?= (int) $p['id'] ?>, <?= (float) $p['pourcentage_reduction'] ?>, '<?= esc(date('Y-m-d\TH:i', strtotime($p['date_debut'])), 'attr') ?>', '<?= $p['date_fin'] ? esc(date('Y-m-d\TH:i', strtotime($p['date_fin'])), 'attr') : '' ?>', <?= (float) $p['montant_min'] ?>, <?= (float) $p['montant_max'] ?>, <?= (int) $p['actif'] ?>)">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <a href="<?= site_url('admin/promotions/delete/' . $p['id']) ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?')" class="btn-action btn-danger" style="min-height: 32px; padding: 0 12px; font-size: 12px;"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </div>
</div>

<div class="modal-overlay" id="promotionModal">
    <div class="modal">
        <h2 id="modalTitle">Ajouter une promotion</h2>
        <form id="promotionForm" method="POST" action="<?= site_url('admin/promotions/store') ?>">
            <div class="form-group">
                <label for="pourcentage_reduction">Réduction (%)</label>
                <input type="number" step="0.01" min="0.01" max="100" id="pourcentage_reduction" name="pourcentage_reduction" required placeholder="Ex: 10">
            </div>
            <div class="form-group">
                <label for="date_debut">Date début</label>
                <input type="datetime-local" id="date_debut" name="date_debut" required>
            </div>
            <div class="form-group">
                <label for="date_fin">Date fin (optionnelle)</label>
                <input type="datetime-local" id="date_fin" name="date_fin">
            </div>
            <div class="form-group">
                <label for="montant_min">Montant minimum (Ar)</label>
                <input type="number" step="1" min="0" id="montant_min" name="montant_min" placeholder="0" value="0">
            </div>
            <div class="form-group">
                <label for="montant_max">Montant maximum (Ar)</label>
                <input type="number" step="1" min="1" id="montant_max" name="montant_max" placeholder="Laissez vide pour illimité">
            </div>
            <div class="form-group">
                <label for="actif">Statut</label>
                <select id="actif" name="actif">
                    <option value="1">Actif</option>
                    <option value="0">Inactif</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-action btn-secondary" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn-action btn-primary" style="background: var(--primary); color: white;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('promotionModal');
const form = document.getElementById('promotionForm');
const modalTitle = document.getElementById('modalTitle');

function openModal() {
    modalTitle.textContent = 'Ajouter une promotion';
    form.action = '<?= site_url('admin/promotions/store') ?>';
    form.reset();
    document.getElementById('montant_min').value = 0;
    modal.classList.add('open');
}

function closeModal() {
    modal.classList.remove('open');
}

function editPromotion(id, reduction, dateDebut, dateFin, montantMin, montantMax, actif) {
    modalTitle.textContent = 'Modifier la promotion';
    form.action = '<?= site_url('admin/promotions/update/') ?>' + id;
    document.getElementById('pourcentage_reduction').value = reduction;
    document.getElementById('date_debut').value = dateDebut;
    document.getElementById('date_fin').value = dateFin;
    document.getElementById('montant_min').value = montantMin;
    document.getElementById('montant_max').value = montantMax >= 999999999 ? '' : montantMax;
    document.getElementById('actif').value = actif;
    modal.classList.add('open');
}

modal.addEventListener('click', function(e) {
    if (e.target === modal) closeModal();
});
</script>

<?= $this->endSection() ?>