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
        --line: #dcd6c7;
        --success: #4f7a5c;
        --error: #c1452b;
    }

    .commissions-page {
        min-height: calc(100vh - 72px);
        padding: 28px 16px 48px;
        background:
            radial-gradient(circle at top left, rgba(217, 164, 65, 0.12), transparent 38%),
            radial-gradient(circle at bottom right, rgba(16, 58, 55, 0.34), transparent 44%),
            linear-gradient(180deg, rgba(11, 46, 43, 0.98), rgba(11, 46, 43, 0.92));
        display: flex;
        justify-content: center;
        color: var(--ink);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .commissions-shell {
        width: 100%;
        max-width: 1100px;
        display: grid;
        gap: 18px;
    }

    .commissions-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        background: linear-gradient(160deg, rgba(246, 243, 236, 0.98), rgba(246, 243, 236, 0.94));
        border-radius: 28px;
        padding: 28px 32px;
        box-shadow: 0 28px 72px rgba(0, 0, 0, 0.28);
    }

    .commissions-header h1 {
        margin: 0;
        font-family: 'Fraunces', Georgia, serif;
        font-size: clamp(26px, 4vw, 38px);
        line-height: 1.1;
        color: var(--teal-950);
    }

    .commissions-header p {
        margin: 8px 0 0;
        color: var(--ink-soft);
        font-size: 15px;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 20px;
        border-radius: 999px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 800;
        transition: transform 0.12s ease, background 0.12s ease;
        border: none;
        cursor: pointer;
    }

    .btn-action:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        background: var(--gold);
        color: #1f2421;
    }

    .btn-primary:hover {
        background: var(--gold-dark);
    }

    .btn-secondary {
        border: 1px solid var(--line);
        background: #fff;
        color: var(--ink);
    }

    .btn-secondary:hover {
        border-color: var(--gold-dark);
    }

    .btn-danger {
        background: var(--error);
        color: #fff;
    }

    .btn-danger:hover {
        background: #a83821;
    }

    .panel-card {
        background: rgba(246, 243, 236, 0.98);
        border-radius: 24px;
        box-shadow: 0 20px 42px rgba(0, 0, 0, 0.18);
        padding: 24px;
        border: 1px solid rgba(0, 0, 0, 0.05);
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
        border-bottom: 2px solid var(--line);
        color: var(--teal-950);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.05em;
    }

    td {
        padding: 16px;
        border-bottom: 1px solid var(--line);
        color: var(--ink);
    }

    tr:hover td {
        background: rgba(18, 67, 63, 0.02);
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
        background: rgba(79, 122, 92, 0.12);
        color: var(--success);
    }

    .status-inactive {
        background: rgba(193, 69, 43, 0.1);
        color: var(--error);
    }

    .flash {
        border-radius: 12px;
        padding: 14px;
        font-size: 14px;
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

    .actions-cell {
        display: flex;
        gap: 8px;
    }

    .empty-state {
        padding: 48px 16px;
        text-align: center;
        color: var(--ink-soft);
    }

    .empty-state strong {
        display: block;
        font-family: 'Fraunces', Georgia, serif;
        font-size: 22px;
        color: var(--teal-950);
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
        background: var(--paper);
        border-radius: 24px;
        padding: 28px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 28px 72px rgba(0,0,0,0.35);
    }

    .modal h2 {
        margin: 0 0 18px;
        font-family: 'Fraunces', Georgia, serif;
        font-size: 24px;
        color: var(--teal-950);
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
        color: var(--ink-soft);
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--line);
        border-radius: 12px;
        background: #fff;
        font-size: 14px;
        color: var(--ink);
        outline: none;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(217, 164, 65, 0.2);
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .commissions-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 24px;
        }

        .btn-action {
            width: 100%;
        }
    }
</style>

<div class="commissions-page">
    <div class="commissions-shell">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="flash success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="flash error"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <section class="commissions-header">
            <div>
                <h1>Commissions par opérateur</h1>
                <p>Configurez les pourcentages de commission appliqués par opérateur et par type d'opération.</p>
            </div>
            <button class="btn-action btn-primary" onclick="openModal()">+ Ajouter une commission</button>
        </section>

        <section class="panel-card">
            <?php if (empty($commissions)): ?>
                <div class="empty-state">
                    <strong>Aucune commission configurée</strong>
                    <p>Commencez par ajouter une commission pour un opérateur.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Opérateur</th>
                            <th>Type d'opération</th>
                            <th>Autre opérateur</th>
                            <th>Commission (%)</th>
                            <th style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commissions as $c): ?>
                            <tr>
                                <td><strong><?= esc($c['operateur_nom']) ?></strong></td>
                                <td><?= esc($c['type_operation_libelle']) ?></td>
                                <td>
                                    <span class="status-pill <?= $c['autre_operateur'] ? 'status-active' : 'status-inactive' ?>">
                                        <?= $c['autre_operateur'] ? 'Oui' : 'Non' ?>
                                    </span>
                                </td>
                                <td><strong><?= number_format((float) $c['commission_pourcentage'], 2, ',', ' ') ?> %</strong></td>
                                <td>
                                    <div class="actions-cell">
                                        <button class="btn-action btn-secondary" style="min-height: 32px; padding: 0 12px; font-size: 12px;" onclick="editCommission(<?= (int) $c['id'] ?>, <?= (int) $c['operateur_id'] ?>, <?= (int) $c['type_operation_id'] ?>, <?= (float) $c['commission_pourcentage'] ?>, <?= (int) $c['autre_operateur'] ?>)">Modifier</button>
                                        <a href="<?= site_url('admin/commissions/delete/' . $c['id']) ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commission ?')" class="btn-action btn-danger" style="min-height: 32px; padding: 0 12px; font-size: 12px;">Supprimer</a>
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

<!-- MODAL AJOUT / MODIFICATION -->
<div class="modal-overlay" id="commissionModal">
    <div class="modal">
        <h2 id="modalTitle">Ajouter une commission</h2>
        <form id="commissionForm" method="POST" action="<?= site_url('admin/commissions/store') ?>">
            <div class="form-group">
                <label for="operateur_id">Opérateur</label>
                <select id="operateur_id" name="operateur_id" required>
                    <option value="">-- Choisir --</option>
                    <?php foreach ($operateurs as $op): ?>
                        <option value="<?= (int) $op['id'] ?>"><?= esc($op['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="type_operation_id">Type d'opération</label>
                <select id="type_operation_id" name="type_operation_id" required>
                    <option value="">-- Choisir --</option>
                    <?php foreach ($types_operations as $t): ?>
                        <option value="<?= (int) $t['id'] ?>"><?= esc($t['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="commission_pourcentage">Commission (%)</label>
                <input type="number" step="0.01" min="0" max="100" id="commission_pourcentage" name="commission_pourcentage" required placeholder="Ex: 2.50">
            </div>
            <div class="form-group">
                <label for="autre_operateur">Autre opérateur</label>
                <select id="autre_operateur" name="autre_operateur">
                    <option value="0">Non</option>
                    <option value="1">Oui</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-action btn-secondary" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn-action btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('commissionModal');
const form = document.getElementById('commissionForm');
const modalTitle = document.getElementById('modalTitle');

function openModal() {
    modalTitle.textContent = 'Ajouter une commission';
    form.action = '<?= site_url('admin/commissions/store') ?>';
    form.reset();
    modal.classList.add('open');
}

function closeModal() {
    modal.classList.remove('open');
}

function editCommission(id, operateurId, typeOperationId, commission, autreOperateur) {
    modalTitle.textContent = 'Modifier la commission';
    form.action = '<?= site_url('admin/commissions/update/') ?>' + id;
    document.getElementById('operateur_id').value = operateurId;
    document.getElementById('type_operation_id').value = typeOperationId;
    document.getElementById('commission_pourcentage').value = commission;
    document.getElementById('autre_operateur').value = autreOperateur;
    modal.classList.add('open');
}

modal.addEventListener('click', function(e) {
    if (e.target === modal) closeModal();
});
</script>

<?= $this->endSection() ?>