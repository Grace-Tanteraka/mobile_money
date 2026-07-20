<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients - Mobile Money Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background-color: #1e293b; color: #fff; }
        .sidebar .nav-link { color: #94a3b8; border-radius: 8px; margin-bottom: 5px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #0ea5e9; color: #fff; }
        .avatar-sm { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #e0f2fe; color: #0ea5e9; font-weight: 600; }
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
                <h1 class="h3 fw-bold">Clients Mobile Money</h1>
                <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 rounded-pill">
                    <?= is_countable($clients) ? count($clients) : 0 ?> client(s) sur cette page
                </span>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Opérateur</th>
                                <th>Solde</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($clients)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fa-solid fa-users-slash fa-2x mb-2 d-block"></i>
                                        Aucun client trouvé.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($clients as $client) : ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-sm">
                                                    <?= esc(mb_strtoupper(mb_substr((string) $client['prenom'], 0, 1) . mb_substr((string) $client['nom'], 0, 1))) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold"><?= esc($client['prenom'] . ' ' . $client['nom']) ?></div>
                                                    <small class="text-muted">ID #<?= esc($client['id']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= esc($client['telephone']) ?></td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary border">
                                                <?= esc($client['operateur_nom']) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold"><?= number_format((float) $client['solde'], 0, ',', ' ') ?> Ar</td>
                                        <td class="text-end">
                                            <a href="<?= base_url('admin/clients/transactions/' . $client['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-list-ul me-1"></i>Transactions
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pager)) : ?>
                    <div class="card-footer bg-white">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
