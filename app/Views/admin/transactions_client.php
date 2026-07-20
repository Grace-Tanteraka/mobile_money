<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions client - Mobile Money Admin</title>
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
            <h4 class="text-white mb-4 ps-2"><i class="fa-solid fa-chart-line text-info me-2"></i>Admin Panel</h4>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="<?= base_url('admin/dashboard') ?>" class="nav-link"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
                </li>
                <li>
                    <a href="<?= base_url('admin/clients') ?>" class="nav-link active"><i class="fa-solid fa-users me-2"></i>Clients</a>
                </li>
            </ul>
            <hr>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Déconnexion</a>
        </aside>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <div>
                    <a href="<?= base_url('admin/clients') ?>" class="text-decoration-none text-muted small">
                        <i class="fa-solid fa-arrow-left me-1"></i>Retour à la liste des clients
                    </a>
                    <h1 class="h3 fw-bold mb-0 mt-1">
                        Transactions de
                        <?= isset($client) ? esc($client['prenom'] . ' ' . $client['nom']) : 'ce client' ?>
                    </h1>
                    <?php if (isset($client)) : ?>
                        <small class="text-muted"><?= esc($client['telephone']) ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Opération</th>
                                <th>Montant</th>
                                <th>Frais</th>
                                <th>Client hôte</th>
                                <th>Client cible</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fa-solid fa-receipt fa-2x mb-2 d-block"></i>
                                        Aucune transaction trouvée pour ce client.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $transaction) : ?>
                                    <tr>
                                        <td><?= esc(date('d/m/Y H:i', strtotime($transaction['date']))) ?></td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border border-primary">
                                                <?= esc($transaction['operation_nom']) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold"><?= number_format((float) $transaction['montant'], 0, ',', ' ') ?> Ar</td>
                                        <td class="text-muted"><?= number_format((float) $transaction['frais'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= esc($transaction['client_hote_nom'] ?? '-') ?></td>
                                        <td><?= esc($transaction['client_cible_nom'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
