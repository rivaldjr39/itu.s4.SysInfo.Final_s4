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
        --success: #4f7a5c;
        --line: #dcd6c7;
    }

    .historique-page {
        min-height: 100vh;
        background: var(--teal-950);
        background-image:
            radial-gradient(circle at 15% 0%, rgba(217, 164, 65, 0.10), transparent 45%),
            radial-gradient(circle at 85% 100%, rgba(217, 164, 65, 0.08), transparent 50%);
        display: flex;
        justify-content: center;
        padding: 32px 16px 60px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: var(--ink);
    }

    .historique-card {
        width: 100%;
        max-width: 460px;
        background: var(--paper);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 60px -20px rgba(0, 0, 0, 0.5);
    }

    .historique-header {
        background: var(--teal-800);
        color: var(--paper);
        padding: 28px 28px 20px;
    }

    .historique-header .eyebrow {
        font-size: 12px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(246, 243, 236, 0.6);
        margin: 0 0 6px;
        font-family: 'IBM Plex Mono', monospace;
    }

    .historique-header h1 {
        font-family: 'Fraunces', Georgia, serif;
        font-weight: 600;
        font-size: 26px;
        margin: 0 0 4px;
        letter-spacing: -0.01em;
    }

    .historique-header .from-number {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        color: rgba(246, 243, 236, 0.75);
    }

    .historique-header .client-id {
        margin-top: 4px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        color: rgba(246, 243, 236, 0.58);
    }

    .historique-body {
        padding: 10px 20px 26px;
    }

    .btn-nouveau {
        display: block;
        text-align: center;
        text-decoration: none;
        width: 100%;
        box-sizing: border-box;
        border: none;
        border-radius: 12px;
        background: var(--gold);
        color: var(--teal-950);
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 15px;
        padding: 15px;
        margin: 16px 0 22px;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.1s ease;
    }

    .btn-nouveau:hover {
        background: var(--gold-dark);
    }

    .btn-nouveau:active {
        transform: scale(0.98);
    }

    .liste-retraits {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .retrait-item {
        border: 1px solid var(--line);
        background: #fff;
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .retrait-item .sens-icone {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'IBM Plex Mono', monospace;
        font-weight: 700;
        font-size: 15px;
        background: rgba(193, 69, 43, 0.1);
        color: var(--error);
    }

    .retrait-details {
        flex: 1;
        min-width: 0;
    }

    .retrait-details .ligne-principale {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 8px;
    }

    .retrait-details .libelle {
        font-size: 14px;
        font-weight: 600;
        color: var(--ink);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .retrait-details .montant {
        font-family: 'IBM Plex Mono', monospace;
        font-weight: 700;
        font-size: 14.5px;
        white-space: nowrap;
        color: var(--error);
    }

    .retrait-details .meta {
        display: flex;
        justify-content: space-between;
        margin-top: 4px;
        font-size: 12px;
        color: var(--ink-soft);
        font-family: 'IBM Plex Mono', monospace;
    }

    .retrait-details .frais {
        font-size: 11.5px;
        color: var(--ink-soft);
        margin-top: 2px;
    }

    .statut-badge {
        display: inline-block;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 2px 7px;
        border-radius: 6px;
        margin-top: 6px;
    }

    .statut-badge.reussi {
        background: rgba(79, 122, 92, 0.12);
        color: var(--success);
    }

    .statut-badge.echec {
        background: rgba(193, 69, 43, 0.1);
        color: var(--error);
    }

    .etat-vide {
        text-align: center;
        padding: 40px 20px;
        color: var(--ink-soft);
    }

    .etat-vide .titre-vide {
        font-family: 'Fraunces', Georgia, serif;
        font-size: 18px;
        color: var(--ink);
        margin-bottom: 6px;
    }

    .etat-vide p {
        font-size: 13.5px;
        line-height: 1.5;
        margin: 0;
    }
</style>

<div class="historique-page">
    <div class="historique-card">
        <div class="historique-header">
            <p class="eyebrow">Mes opérations</p>
            <h1>Historique des retraits</h1>
            <div class="from-number"><?= esc($numero_client) ?></div>
            <div class="client-id">ID client #<?= esc((string) $client_id) ?></div>
        </div>

        <div class="historique-body">
            <a href="<?= site_url('retrait') ?>" class="btn-nouveau">Faire un retrait d'espèces</a>

            <?php if (empty($historique)): ?>
                <div class="etat-vide">
                    <div class="titre-vide">Aucun retrait pour l'instant</div>
                    <p>Chaque retrait que tu effectues apparaîtra ici.</p>
                </div>
            <?php else: ?>
                <div class="liste-retraits">
                    <?php foreach ($historique as $t): ?>
                        <?php
                            $montantAffiche = (float) $t['montant_total'];
                        ?>
                        <div class="retrait-item">
                            <div class="sens-icone">↓</div>
                            <div class="retrait-details">
                                <div class="ligne-principale">
                                    <span class="libelle">Retrait d'espèces</span>
                                    <span class="montant">-<?= number_format($montantAffiche, 0, ',', ' ') ?> Ar</span>
                                </div>
                                <div class="meta">
                                    <span><?= esc(date('d/m/Y H:i', strtotime($t['date_operation']))) ?></span>
                                    <span><?= esc($t['reference']) ?></span>
                                </div>
                                <?php if ((float) $t['frais'] > 0): ?>
                                    <div class="frais">Frais : <?= number_format((float) $t['frais'], 0, ',', ' ') ?> Ar (compris dans le montant débité)</div>
                                <?php endif; ?>
                                <span class="statut-badge <?= strtolower($t['statut_libelle']) === 'reussi' ? 'reussi' : 'echec' ?>">
                                    <?= esc($t['statut_libelle']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
