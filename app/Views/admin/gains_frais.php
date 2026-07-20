<?= $this->extend('layout/client') ?? '' ?>

<?php
$stats     = isset($stats) && is_array($stats) ? $stats : [];
$operateurs = $stats['operateurs'] ?? [];
$totalFrais = (float) ($stats['total_frais'] ?? 0);
$totalOps   = (int) ($stats['total_operations'] ?? 0);
$totalMontant = (float) ($stats['total_montant'] ?? 0);
$dateDebut = $stats['date_debut'] ?? ($date_debut ?? '');
$dateFin   = $stats['date_fin'] ?? ($date_fin ?? '');
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
        --line: #dcd6c7;
        --success: #4f7a5c;
        --error: #c1452b;
    }

    .gains-page {
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

    .gains-shell {
        width: 100%;
        max-width: 1000px;
        display: grid;
        gap: 18px;
    }

    .gains-header {
        background: rgba(246, 243, 236, 0.98);
        border-radius: 28px;
        padding: 28px 32px;
        box-shadow: 0 28px 72px rgba(0, 0, 0, 0.28);
    }

    .gains-header h1 {
        margin: 0;
        font-family: 'Fraunces', Georgia, serif;
        font-size: clamp(28px, 4vw, 40px);
        line-height: 0.95;
        letter-spacing: -0.04em;
        color: var(--teal-950);
    }

    .gains-header p {
        margin: 10px 0 0;
        color: var(--ink-soft);
        font-size: 15px;
    }

    /* Filtre par période */
    .filter-card {
        background: rgba(246, 243, 236, 0.98);
        border-radius: 24px;
        padding: 20px 24px;
        box-shadow: 0 20px 42px rgba(0, 0, 0, 0.18);
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--ink-soft);
    }

    .filter-group input {
        padding: 10px 14px;
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #fff;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 14px;
        color: var(--ink);
        outline: none;
        transition: border-color 0.12s ease;
    }

    .filter-group input:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 2px rgba(217, 164, 65, 0.2);
    }

    .filter-submit {
        padding: 10px 20px;
        border: none;
        border-radius: 999px;
        background: var(--gold);
        color: #1f2421;
        font-weight: 800;
        font-size: 13px;
        cursor: pointer;
        transition: background 0.12s ease, transform 0.12s ease;
    }

    .filter-submit:hover {
        background: var(--gold-dark);
        transform: translateY(-1px);
    }

    .filter-reset {
        padding: 10px 20px;
        border: 1px solid var(--line);
        border-radius: 999px;
        background: transparent;
        color: var(--ink-soft);
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        transition: background 0.12s ease;
    }

    .filter-reset:hover {
        background: rgba(0,0,0,0.04);
    }

    /* Cartes de synthèse */
    .synthese-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .synthese-card {
        background: rgba(246, 243, 236, 0.98);
        border-radius: 24px;
        padding: 20px;
        box-shadow: 0 20px 42px rgba(0, 0, 0, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.14);
    }

    .synthese-card span {
        display: block;
        margin-bottom: 8px;
        color: var(--ink-soft);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
    }

    .synthese-card strong {
        display: block;
        font-family: 'Fraunces', Georgia, serif;
        font-size: 30px;
        line-height: 1;
        color: var(--teal-950);
    }

    .synthese-card small {
        display: block;
        margin-top: 8px;
        color: var(--ink-soft);
        line-height: 1.45;
        font-size: 13px;
    }

    /* Cards de tableaux séparés */
    .table-card {
        background: rgba(246, 243, 236, 0.98);
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 20px 42px rgba(0, 0, 0, 0.18);
    }

    .table-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .table-card-header .badge-type {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        border-radius: 50%;
        font-weight: 800;
        font-size: 15px;
    }

    .badge-type.retrait {
        background: rgba(193, 69, 43, 0.12);
        color: var(--error);
    }

    .badge-type.meme {
        background: rgba(79, 122, 92, 0.12);
        color: var(--success);
    }

    .badge-type.autre {
        background: rgba(217, 164, 65, 0.12);
        color: var(--gold-dark);
    }

    .badge-type.total {
        background: rgba(18, 67, 63, 0.12);
        color: var(--teal-800);
    }

    .table-card-header h3 {
        margin: 0;
        font-family: 'Fraunces', Georgia, serif;
        font-size: 20px;
        letter-spacing: -0.02em;
        color: var(--teal-950);
    }

    .table-card-header .sous-titre {
        font-size: 13px;
        color: var(--ink-soft);
        margin-left: auto;
    }

    .gains-table {
        width: 100%;
        border-collapse: collapse;
    }

    .gains-table thead th {
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--ink-soft);
        padding: 10px 12px;
        border-bottom: 2px solid var(--line);
    }

    .gains-table thead th.num-header {
        text-align: right;
    }

    .gains-table tbody td {
        padding: 12px;
        border-bottom: 1px solid var(--line);
        font-size: 14px;
        color: var(--ink);
    }

    .gains-table tbody tr:last-child td {
        border-bottom: none;
    }

    .gains-table tbody tr:hover {
        background: rgba(18, 67, 63, 0.03);
    }

    .gains-table .num {
        font-family: 'IBM Plex Mono', monospace;
        text-align: right;
        white-space: nowrap;
    }

    .gains-table .operator-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        background: rgba(18, 67, 63, 0.08);
        color: var(--teal-800);
    }

    .gains-table .total-row td {
        font-weight: 800;
        border-top: 2px solid var(--teal-800);
        border-bottom: none;
        color: var(--teal-950);
        background: rgba(11, 46, 43, 0.03);
    }

    .empty-state {
        padding: 24px 14px;
        text-align: center;
        color: var(--ink-soft);
        border: 1px dashed var(--line);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.6);
    }

    .empty-state strong {
        display: block;
        margin-bottom: 6px;
        color: var(--ink);
        font-family: 'Fraunces', Georgia, serif;
        font-size: 17px;
    }

    @media (max-width: 768px) {
        .synthese-grid {
            grid-template-columns: 1fr;
        }
        .filter-form {
            flex-direction: column;
        }
    }
</style>

<div class="gains-page">
    <div class="gains-shell">
        <section class="gains-header">
            <h1>Situation des gains via les frais</h1>
            <p>Recettes générées par les frais perçus sur les opérations de <strong>retrait</strong> et de <strong>transfert</strong>, ventilées par opérateur.</p>
        </section>

        <!-- Filtre par période -->
        <section class="filter-card">
            <form class="filter-form" method="GET" action="<?= site_url('admin/gains-frais') ?>">
                <div class="filter-group">
                    <label for="date_debut">Date début</label>
                    <input type="date" id="date_debut" name="date_debut" value="<?= esc($dateDebut ?? '') ?>">
                </div>
                <div class="filter-group">
                    <label for="date_fin">Date fin</label>
                    <input type="date" id="date_fin" name="date_fin" value="<?= esc($dateFin ?? '') ?>">
                </div>
                <button type="submit" class="filter-submit">Filtrer</button>
                <?php if (!empty($dateDebut) || !empty($dateFin)): ?>
                    <a href="<?= site_url('admin/gains-frais') ?>" class="filter-reset">Réinitialiser</a>
                <?php endif; ?>
            </form>
        </section>

        <!-- Cartes de synthèse -->
        <section class="synthese-grid">
            <div class="synthese-card">
                <span>Total des frais perçus</span>
                <strong><?= number_format($totalFrais, 0, ',', ' ') ?> Ar</strong>
                <small>Somme de tous les frais prélevés sur les retraits et transferts.</small>
            </div>
            <div class="synthese-card">
                <span>Nombre d'opérations</span>
                <strong><?= number_format($totalOps, 0, ',', ' ') ?></strong>
                <small>Total des opérations (retraits + transferts) réussies.</small>
            </div>
            <div class="synthese-card">
                <span>Montant total brut</span>
                <strong><?= number_format($totalMontant, 0, ',', ' ') ?> Ar</strong>
                <small>Somme des montants transférés/retirés (hors frais).</small>
            </div>
        </section>

        <?php if (empty($operateurs)): ?>
            <section class="table-card">
                <div class="empty-state">
                    <strong>Aucune donnée</strong>
                    <div>Aucune opération de retrait ou transfert trouvée pour la période sélectionnée.</div>
                </div>
            </section>
        <?php else: ?>

        <!-- ===== TABLEAU 1 : RETRAITS ===== -->
        <section class="table-card">
            <div class="table-card-header">
                <span class="badge-type retrait">↑</span>
                <h3>Retraits</h3>
                <span class="sous-titre">Frais perçus sur les retraits effectués</span>
            </div>
            <table class="gains-table">
                <thead>
                    <tr>
                        <th>Opérateur</th>
                        <th class="num-header">Nombre</th>
                        <th class="num-header">Total frais</th>
                        <th class="num-header">Moyenne frais</th>
                        <th class="num-header">Montant total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $totalRetraitNb = 0; $totalRetraitFrais = 0; $totalRetraitMontant = 0; ?>
                    <?php foreach ($operateurs as $op): ?>
                        <?php if ((int) $op['retrait_nb'] > 0 || (float) $op['retrait_frais'] > 0): ?>
                        <?php 
                            $totalRetraitNb += (int) $op['retrait_nb'];
                            $totalRetraitFrais += (float) $op['retrait_frais'];
                            $totalRetraitMontant += (float) $op['retrait_montant'];
                        ?>
                        <tr>
                            <td><span class="operator-badge"><?= esc($op['operateur_nom']) ?></span></td>
                            <td class="num"><?= number_format((int) $op['retrait_nb'], 0, ',', ' ') ?></td>
                            <td class="num"><?= number_format((float) $op['retrait_frais'], 0, ',', ' ') ?> Ar</td>
                            <td class="num"><?= number_format((float) $op['retrait_moyenne'], 0, ',', ' ') ?> Ar</td>
                            <td class="num"><?= number_format((float) $op['retrait_montant'], 0, ',', ' ') ?> Ar</td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td><strong>TOTAL RETRAITS</strong></td>
                        <td class="num"><strong><?= number_format($totalRetraitNb, 0, ',', ' ') ?></strong></td>
                        <td class="num"><strong><?= number_format($totalRetraitFrais, 0, ',', ' ') ?> Ar</strong></td>
                        <td class="num"><strong><?= $totalRetraitNb > 0 ? number_format($totalRetraitFrais / $totalRetraitNb, 0, ',', ' ') : 0 ?> Ar</strong></td>
                        <td class="num"><strong><?= number_format($totalRetraitMontant, 0, ',', ' ') ?> Ar</strong></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- ===== TABLEAU 2 : TRANSFERTS MÊME OPÉRATEUR ===== -->
        <section class="table-card">
            <div class="table-card-header">
                <span class="badge-type meme">→</span>
                <h3>Transferts — Même opérateur</h3>
                <span class="sous-titre">Expéditeur et destinataire sur le même réseau</span>
            </div>
            <table class="gains-table">
                <thead>
                    <tr>
                        <th>Opérateur</th>
                        <th class="num-header">Nombre</th>
                        <th class="num-header">Total frais</th>
                        <th class="num-header">Moyenne frais</th>
                        <th class="num-header">Montant total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $totalMemeNb = 0; $totalMemeFrais = 0; $totalMemeMontant = 0; ?>
                    <?php foreach ($operateurs as $op): ?>
                        <?php if ((int) $op['meme_nb'] > 0 || (float) $op['meme_frais'] > 0): ?>
                        <?php 
                            $totalMemeNb += (int) $op['meme_nb'];
                            $totalMemeFrais += (float) $op['meme_frais'];
                            $totalMemeMontant += (float) $op['meme_montant'];
                        ?>
                        <tr>
                            <td><span class="operator-badge"><?= esc($op['operateur_nom']) ?></span></td>
                            <td class="num"><?= number_format((int) $op['meme_nb'], 0, ',', ' ') ?></td>
                            <td class="num"><?= number_format((float) $op['meme_frais'], 0, ',', ' ') ?> Ar</td>
                            <td class="num"><?= number_format((float) $op['meme_moyenne'], 0, ',', ' ') ?> Ar</td>
                            <td class="num"><?= number_format((float) $op['meme_montant'], 0, ',', ' ') ?> Ar</td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td><strong>TOTAL MÊME OPÉRATEUR</strong></td>
                        <td class="num"><strong><?= number_format($totalMemeNb, 0, ',', ' ') ?></strong></td>
                        <td class="num"><strong><?= number_format($totalMemeFrais, 0, ',', ' ') ?> Ar</strong></td>
                        <td class="num"><strong><?= $totalMemeNb > 0 ? number_format($totalMemeFrais / $totalMemeNb, 0, ',', ' ') : 0 ?> Ar</strong></td>
                        <td class="num"><strong><?= number_format($totalMemeMontant, 0, ',', ' ') ?> Ar</strong></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- ===== TABLEAU 3 : TRANSFERTS AUTRE OPÉRATEUR ===== -->
        <section class="table-card">
            <div class="table-card-header">
                <span class="badge-type autre">↗</span>
                <h3>Transferts — Autre opérateur</h3>
                <span class="sous-titre">Expéditeur et destinataire sur des réseaux différents</span>
            </div>
            <table class="gains-table">
                <thead>
                    <tr>
                        <th>Opérateur (source)</th>
                        <th class="num-header">Nombre</th>
                        <th class="num-header">Total frais</th>
                        <th class="num-header">Moyenne frais</th>
                        <th class="num-header">Montant total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $totalAutreNb = 0; $totalAutreFrais = 0; $totalAutreMontant = 0; ?>
                    <?php foreach ($operateurs as $op): ?>
                        <?php if ((int) $op['autre_nb'] > 0 || (float) $op['autre_frais'] > 0): ?>
                        <?php 
                            $totalAutreNb += (int) $op['autre_nb'];
                            $totalAutreFrais += (float) $op['autre_frais'];
                            $totalAutreMontant += (float) $op['autre_montant'];
                        ?>
                        <tr>
                            <td><span class="operator-badge"><?= esc($op['operateur_nom']) ?></span></td>
                            <td class="num"><?= number_format((int) $op['autre_nb'], 0, ',', ' ') ?></td>
                            <td class="num"><?= number_format((float) $op['autre_frais'], 0, ',', ' ') ?> Ar</td>
                            <td class="num"><?= number_format((float) $op['autre_moyenne'], 0, ',', ' ') ?> Ar</td>
                            <td class="num"><?= number_format((float) $op['autre_montant'], 0, ',', ' ') ?> Ar</td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td><strong>TOTAL AUTRE OPÉRATEUR</strong></td>
                        <td class="num"><strong><?= number_format($totalAutreNb, 0, ',', ' ') ?></strong></td>
                        <td class="num"><strong><?= number_format($totalAutreFrais, 0, ',', ' ') ?> Ar</strong></td>
                        <td class="num"><strong><?= $totalAutreNb > 0 ? number_format($totalAutreFrais / $totalAutreNb, 0, ',', ' ') : 0 ?> Ar</strong></td>
                        <td class="num"><strong><?= number_format($totalAutreMontant, 0, ',', ' ') ?> Ar</strong></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- ===== TABLEAU 4 : RÉCAPITULATIF GÉNÉRAL ===== -->
        <section class="table-card">
            <div class="table-card-header">
                <span class="badge-type total">Σ</span>
                <h3>Récapitulatif général par opérateur</h3>
                <span class="sous-titre">Synthèse de tous les frais perçus</span>
            </div>
            <table class="gains-table">
                <thead>
                    <tr>
                        <th>Opérateur</th>
                        <th class="num-header">Retraits</th>
                        <th class="num-header">Transferts (même)</th>
                        <th class="num-header">Transferts (autre)</th>
                        <th class="num-header">Total opérations</th>
                        <th class="num-header">Total frais</th>
                        <th class="num-header">Total montant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($operateurs as $op): ?>
                        <tr>
                            <td><span class="operator-badge"><?= esc($op['operateur_nom']) ?></span></td>
                            <td class="num"><?= number_format((int) $op['retrait_nb'], 0, ',', ' ') ?></td>
                            <td class="num"><?= number_format((int) $op['meme_nb'], 0, ',', ' ') ?></td>
                            <td class="num"><?= number_format((int) $op['autre_nb'], 0, ',', ' ') ?></td>
                            <td class="num"><strong><?= number_format((int) $op['total_operations'], 0, ',', ' ') ?></strong></td>
                            <td class="num"><strong><?= number_format((float) $op['total_frais'], 0, ',', ' ') ?> Ar</strong></td>
                            <td class="num"><strong><?= number_format((float) $op['total_montant'], 0, ',', ' ') ?> Ar</strong></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td><strong>TOTAL GÉNÉRAL</strong></td>
                        <td class="num"><strong><?= number_format($totalRetraitNb, 0, ',', ' ') ?></strong></td>
                        <td class="num"><strong><?= number_format($totalMemeNb, 0, ',', ' ') ?></strong></td>
                        <td class="num"><strong><?= number_format($totalAutreNb, 0, ',', ' ') ?></strong></td>
                        <td class="num"><strong><?= number_format($totalOps, 0, ',', ' ') ?></strong></td>
                        <td class="num"><strong><?= number_format($totalFrais, 0, ',', ' ') ?> Ar</strong></td>
                        <td class="num"><strong><?= number_format($totalMontant, 0, ',', ' ') ?> Ar</strong></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>