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
        --warning: #d9a441;
    }

    .baremes-page {
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

    .baremes-shell {
        width: 100%;
        max-width: 1180px;
        display: grid;
        gap: 20px;
    }

    .baremes-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        background: linear-gradient(160deg, rgba(246, 243, 236, 0.98), rgba(246, 243, 236, 0.94));
        border-radius: 28px;
        padding: 32px;
        box-shadow: 0 28px 72px rgba(0, 0, 0, 0.28);
    }

    .hero-copy h1 {
        margin: 0;
        font-family: 'Fraunces', Georgia, serif;
        font-size: clamp(28px, 4vw, 42px);
        line-height: 1.1;
        color: var(--teal-950);
    }

    .hero-copy p {
        margin: 8px 0 0;
        color: var(--ink-soft);
        font-size: 15px;
        line-height: 1.5;
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

    .btn-action:active {
        transform: scale(0.98);
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

    .amount-val {
        font-family: 'IBM Plex Mono', monospace;
        font-weight: 600;
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

    .status-planned {
        background: rgba(217, 164, 65, 0.12);
        color: var(--gold-dark);
    }

    .status-expired {
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

    @media (max-width: 768px) {
        .baremes-hero {
            flex-direction: column;
            align-items: flex-start;
            padding: 24px;
        }

        .btn-action {
            width: 100%;
        }
    }
</style>

<div class="baremes-page">
    <div class="baremes-shell">
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="flash success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="flash error"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <section class="baremes-hero" aria-labelledby="hero-title">
            <div class="hero-copy">
                <h1 id="hero-title">Barèmes de frais</h1>
                <p>Configurez et modifiez les grilles tarifaires appliquées aux différentes opérations (Dépôt, Retrait, Transfert).</p>
            </div>
            <a class="btn-action btn-primary" href="<?= site_url('admin/baremes/create') ?>">Ajouter un barème</a>
        </section>

        <section class="panel-card">
            <?php if (empty($baremes)): ?>
                <div class="empty-state">
                    <strong>Aucun barème configuré</strong>
                    <p>Commencez par ajouter une grille tarifaire pour vos opérations.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Opération</th>
                            <th>Montant Min</th>
                            <th>Montant Max</th>
                            <th>Frais Fixe</th>
                            <th>Frais (%)</th>
                            <th>Date Début</th>
                            <th>Date Fin</th>
                            <th>Statut</th>
                            <th style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $now = date('Y-m-d H:i:s');
                        foreach ($baremes as $b): 
                            $statut = 'Actif';
                            $statutClass = 'status-active';
                            if ($b['date_debut'] > $now) {
                                $statut = 'Planifié';
                                $statutClass = 'status-planned';
                            } elseif ($b['date_fin'] !== null && $b['date_fin'] < $now) {
                                $statut = 'Expiré';
                                $statutClass = 'status-expired';
                            }
                        ?>
                            <tr>
                                <td>
                                    <strong><?= esc($b['type_operation_libelle']) ?></strong>
                                </td>
                                <td class="amount-val">
                                    <?= number_format((float)$b['montant_min'], 0, ',', ' ') ?> Ar
                                </td>
                                <td class="amount-val">
                                    <?php if ((float)$b['montant_max'] >= 999999999): ?>
                                        Et plus
                                    <?php else: ?>
                                        <?= number_format((float)$b['montant_max'], 0, ',', ' ') ?> Ar
                                    <?php endif; ?>
                                </td>
                                <td class="amount-val">
                                    <?= number_format((float)$b['frais_fixe'], 0, ',', ' ') ?> Ar
                                </td>
                                <td class="amount-val">
                                    <?= number_format((float)$b['frais_pourcentage'], 2, ',', ' ') ?> %
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($b['date_debut'])) ?>
                                </td>
                                <td>
                                    <?= $b['date_fin'] ? date('d/m/Y H:i', strtotime($b['date_fin'])) : '<span style="color: var(--ink-soft); font-style: italic;">Indéterminée</span>' ?>
                                </td>
                                <td>
                                    <span class="status-pill <?= $statutClass ?>"><?= $statut ?></span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="<?= site_url('admin/baremes/edit/' . $b['id']) ?>" class="btn-action btn-secondary" style="min-height: 32px; padding: 0 12px; font-size: 12px;">Modifier</a>
                                        <a href="<?= site_url('admin/baremes/delete/' . $b['id']) ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce barème ?')" class="btn-action btn-danger" style="min-height: 32px; padding: 0 12px; font-size: 12px;">Supprimer</a>
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

<?= $this->endSection() ?>
