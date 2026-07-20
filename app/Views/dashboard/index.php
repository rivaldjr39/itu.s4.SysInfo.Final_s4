<?= $this->extend('layout/client') ?? '' ?>

<?php
$numero_client = isset($numero_client) && !is_array($numero_client) ? (string) $numero_client : '';
$client_nom = isset($client_nom) && !is_array($client_nom) ? (string) $client_nom : '';
$client_id = isset($client_id) && !is_array($client_id) ? (int) $client_id : 0;
$dashboard = isset($dashboard) && is_array($dashboard) ? $dashboard : [];
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
    }

    .dashboard-page {
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

    .dashboard-shell {
        width: 100%;
        max-width: 1180px;
        display: grid;
        gap: 18px;
    }

    .dashboard-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(280px, 0.9fr);
        gap: 18px;
        background: linear-gradient(160deg, rgba(246, 243, 236, 0.98), rgba(246, 243, 236, 0.94));
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 28px 72px rgba(0, 0, 0, 0.28);
    }

    .hero-copy {
        padding: 32px;
        display: grid;
        gap: 18px;
    }

    .eyebrow {
        margin: 0;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.18em;
        color: var(--ink-soft);
    }

    .hero-copy h1 {
        margin: 0;
        font-family: 'Fraunces', Georgia, serif;
        font-size: clamp(34px, 5vw, 58px);
        line-height: 0.95;
        letter-spacing: -0.04em;
        color: var(--teal-950);
    }

    .hero-copy p {
        margin: 0;
        max-width: 700px;
        color: var(--ink-soft);
        font-size: 16px;
        line-height: 1.65;
    }

    .quick-metadata {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        padding: 0 14px;
        border-radius: 999px;
        background: rgba(18, 67, 63, 0.08);
        color: var(--teal-800);
        font-size: 13px;
        font-weight: 700;
    }

    .hero-panel {
        background: linear-gradient(180deg, var(--teal-800), #0c3835);
        color: var(--paper);
        padding: 28px;
        display: grid;
        gap: 16px;
        align-content: center;
    }

    .hero-panel h2 {
        margin: 0;
        font-family: 'Fraunces', Georgia, serif;
        font-size: 24px;
        line-height: 1.1;
    }

    .hero-panel p {
        margin: 0;
        color: rgba(246, 243, 236, 0.8);
        line-height: 1.55;
        font-size: 14px;
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .hero-actions a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 999px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
        transition: transform 0.12s ease, background 0.12s ease;
    }

    .hero-actions a:hover {
        transform: translateY(-1px);
    }

    .hero-actions .primary {
        background: var(--gold);
        color: #1f2421;
    }

    .hero-actions .primary:hover {
        background: var(--gold-dark);
    }

    .hero-actions .secondary {
        border: 1px solid rgba(246, 243, 236, 0.2);
        color: var(--paper);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .stat-card,
    .panel-card {
        background: rgba(246, 243, 236, 0.98);
        border-radius: 24px;
        box-shadow: 0 20px 42px rgba(0, 0, 0, 0.18);
    }

    .stat-card {
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.14);
    }

    .stat-card span {
        display: block;
        margin-bottom: 10px;
        color: var(--ink-soft);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
    }

    .stat-card strong {
        display: block;
        font-family: 'Fraunces', Georgia, serif;
        font-size: 32px;
        line-height: 1;
        color: var(--teal-950);
    }

    .stat-card small {
        display: block;
        margin-top: 8px;
        color: var(--ink-soft);
        line-height: 1.45;
    }

    .panel-card {
        padding: 24px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .panel-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 12px;
        margin-bottom: 16px;
    }

    .panel-head h3 {
        margin: 0;
        font-size: 18px;
        letter-spacing: -0.02em;
        color: var(--teal-950);
    }

    .panel-head p {
        margin: 4px 0 0;
        color: var(--ink-soft);
        font-size: 13px;
    }

    .operations-list {
        display: grid;
        gap: 12px;
    }

    .operation-item {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        border: 1px solid var(--line);
        border-radius: 16px;
        background: #fff;
        padding: 14px 16px;
    }

    .operation-item .label {
        font-weight: 700;
        color: var(--ink);
    }

    .operation-item .meta {
        font-size: 12px;
        color: var(--ink-soft);
        margin-top: 4px;
    }

    .operation-item .amount {
        font-family: 'Fraunces', Georgia, serif;
        font-size: 20px;
        color: var(--teal-950);
        white-space: nowrap;
    }

    .empty-state {
        padding: 26px 14px;
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
        font-size: 18px;
    }

    @media (max-width: 940px) {
        .dashboard-hero {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-page">
    <div class="dashboard-shell">
        <section class="dashboard-hero" aria-labelledby="dashboard-title">
            <div class="hero-copy">
                <p class="eyebrow">Tableau de bord client</p>
                <h1 id="dashboard-title">Bonjour <?= esc($client_nom ?: 'Client') ?>.</h1>
                <p>
                    Votre espace centralise les transferts, l'historique et vos actions rapides.
                    Vous pouvez lancer un transfert, consulter vos opérations récentes ou vous déconnecter en un clic.
                </p>

                <div class="quick-metadata">
                    <span class="meta-pill">Numéro : <?= esc($numero_client) ?></span>
                    <span class="meta-pill">ID client : #<?= esc((string) $client_id) ?></span>
                </div>
            </div>

            <aside class="hero-panel">
                <h2>Accès rapide</h2>
                <p>Commencez par un transfert ou ouvrez l'historique pour vérifier vos dernières opérations.</p>
                <div class="hero-actions">
                    <a class="primary" href="<?= site_url('transfert') ?>">Faire un transfert</a>
                    <a class="secondary" href="<?= site_url('transfert/historique') ?>">Voir l'historique</a>
                </div>
            </aside>
        </section>

        <section class="stats-grid" aria-label="Résumé du compte">
            <div class="stat-card">
                <span>Transferts</span>
                <strong><?= esc((string) ($dashboard['total_operations'] ?? 0)) ?></strong>
                <small>Nombre total d'opérations enregistrées sur ce compte.</small>
            </div>

            <div class="stat-card">
                <span>Solde</span>
                <strong><?= number_format((float) ($solde ?? 0), 0, ',', ' ') ?> Ar</strong>
                <small>Montant disponible sur le compte client.</small>
            </div>

            <div class="stat-card">
                <span>Historique</span>
                <strong><?= esc((string) count($dashboard['recent_operations'] ?? [])) ?></strong>
                <small>Opérations chargées pour l'aperçu rapide du dashboard.</small>
            </div>
        </section>

        <section class="panel-card" aria-labelledby="recent-title">
            <div class="panel-head">
                <div>
                    <h3 id="recent-title">Opérations récentes</h3>
                    <p>Les trois dernières transactions de votre compte.</p>
                </div>
                <a href="<?= site_url('transfert/historique') ?>" class="hero-actions secondary" style="border-color: var(--line); color: var(--teal-950);">Historique complet</a>
            </div>

            <?php if (empty($dashboard['recent_operations'])): ?>
                <div class="empty-state">
                    <strong>Aucune opération</strong>
                    <div>Le dashboard apparaîtra ici dès votre premier transfert.</div>
                </div>
            <?php else: ?>
                <div class="operations-list">
                    <?php foreach ($dashboard['recent_operations'] as $operation): ?>
                        <?php
                            $estEnvoye = !empty($operation['source_client']) && (int) $operation['source_client'] === (int) $client_id;
                            $libelle = $estEnvoye ? 'Envoyé' : 'Reçu';
                            $autreNumero = $estEnvoye
                                ? ($operation['numero_destination'] ?? 'Numéro inconnu')
                                : ($operation['numero_source'] ?? 'Numéro inconnu');
                            $montant = $estEnvoye ? (float) $operation['montant_total'] : (float) $operation['montant'];
                        ?>
                        <article class="operation-item">
                            <div>
                                <div class="label"><?= esc($libelle) ?> <?= esc($autreNumero) ?></div>
                                <div class="meta"><?= esc(date('d/m/Y H:i', strtotime($operation['date_operation']))) ?> · <?= esc($operation['reference']) ?></div>
                            </div>
                            <div class="amount"><?= $estEnvoye ? '-' : '+' ?><?= number_format($montant, 0, ',', ' ') ?> Ar</div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
