<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Transactions - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background-color: #1e293b; color: #fff; }
        .sidebar .nav-link { color: #94a3b8; border-radius: 8px; margin-bottom: 5px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #0ea5e9; color: #fff; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 d-md-block sidebar p-3 collapse">
            <h4 class="text-white mb-4 ps-2"><i class="fa-solid fa-wallet text-info me-2"></i>Mon Espace</h4>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="<?= base_url('client/dashboard') ?>" class="nav-link"><i class="fa-solid fa-chart-line me-2"></i>Tableau de bord</a>
                </li>
                <li>
                    <a href="<?= base_url('client/depot') ?>" class="nav-link"><i class="fa-solid fa-plus-circle me-2"></i>Dépôt</a>
                </li>
                <li>
                    <a href="<?= base_url('client/transfert') ?>" class="nav-link"><i class="fa-solid fa-arrow-right-arrow-left me-2"></i>Transfert</a>
                </li>
                <li>
                    <a href="<?= base_url('client/retrait') ?>" class="nav-link"><i class="fa-solid fa-minus-circle me-2"></i>Retrait</a>
                </li>
                <li>
                    <a href="<?= base_url('client/historique') ?>" class="nav-link active"><i class="fa-solid fa-history me-2"></i>Historique</a>
                </li>
                 <li>
                        <a href="<?= base_url('client/epargne') ?>" class="nav-link"><i class="fa-solid fa-history me-2"></i>Epargne</a>
                    </li>
            </ul>
            <hr>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Déconnexion</a>
        </aside>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <h1 class="h3 fw-bold">Historique des Transactions</h1>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <?php if (empty($transactions)): ?>
                        <div class="text-center py-5">
                            <i class="fa-solid fa-receipt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune transaction trouvée.</p>
                            <a href="<?= base_url('client/depot') ?>" class="btn btn-primary">Effectuer un dépôt</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Opération</th>
                                        <th>Montant</th>
                                        <th>Frais</th>
                                        <th>Détails</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($transaction['date'])) ?></td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary border border-primary">
                                                    <?= esc($transaction['operation_nom']) ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold"><?= number_format($transaction['montant'], 0, ',', ' ') ?> Ar</td>
                                            <td class="text-muted"><?= number_format($transaction['frais'], 0, ',', ' ') ?> Ar</td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php if ($transaction['client_cible']): ?>
                                                        De: <?= esc($transaction['client_hote_nom'] . ' ' . $transaction['client_hote_prenom']) ?><br>
                                                        À: <?= esc($transaction['client_cible_nom'] . ' ' . $transaction['client_cible_prenom']) ?>
                                                    <?php else: ?>
                                                        Client: <?= esc($transaction['client_hote_nom'] . ' ' . $transaction['client_hote_prenom']) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
