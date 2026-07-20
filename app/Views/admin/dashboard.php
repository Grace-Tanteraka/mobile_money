<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background-color: #1e293b; color: #fff; }
        .sidebar .nav-link { color: #94a3b8; border-radius: 8px; margin-bottom: 5px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #0ea5e9; color: #fff; }
        .card-stat { border: none; border-radius: 12px; transition: transform 0.2s; }
        .card-stat:hover { transform: translateY(-3px); }
        .icon-box { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 d-md-block sidebar p-3 collapse">
            <h4 class="text-white mb-4 ps-2"><i class="fa-solid fa-chart-line text-info me-2"></i>Admin Panel</h4>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="<?= base_url('admin/dashboard') ?>" class="nav-link active"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
                </li>
                <li>
                    <a href="<?= base_url('admin/clients') ?>" class="nav-link"><i class="fa-solid fa-users me-2"></i>Clients</a>
                </li>
            </ul>
            <hr>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Déconnexion</a>
        </aside>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <h1 class="h3 fw-bold">Bonjour, <?= esc($adminName ?? 'Administrateur') ?> 👋</h1>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-circle me-1" style="font-size: 8px;"></i> Admin Actif
                    </span>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary-subtle text-primary me-3">
                                <i class="fa-solid fa-coins fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Gains Totaux</small>
                                <h3 class="mb-0 fw-bold"><?= number_format($totalGains, 0, ',', ' ') ?> Ar</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success-subtle text-success me-3">
                                <i class="fa-solid fa-exchange-alt fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Transactions</small>
                                <h3 class="mb-0 fw-bold"><?= $totalTransactions ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-info-subtle text-info me-3">
                                <i class="fa-solid fa-users fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Clients</small>
                                <h3 class="mb-0 fw-bold"><?= $totalClients ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-warning-subtle text-warning me-3">
                                <i class="fa-solid fa-list fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Opérations</small>
                                <h3 class="mb-0 fw-bold"><?= $totalOperations ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Répartition des Gains par Opération</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="gainsOperationChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Répartition des Gains par Opérateur</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="gainsOperateurChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Données pour le graphique par opération
    const operationLabels = <?= json_encode(array_column($gainsParOperation, 'operation')) ?>;
    const operationData = <?= json_encode(array_column($gainsParOperation, 'total')) ?>;
    const operationColors = ['#0ea5e9', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6'];

    new Chart(document.getElementById('gainsOperationChart'), {
        type: 'doughnut',
        data: {
            labels: operationLabels,
            datasets: [{
                data: operationData,
                backgroundColor: operationColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Données pour le graphique par opérateur
    const operateurLabels = <?= json_encode(array_column($gainsParOperateur, 'operateur')) ?>;
    const operateurData = <?= json_encode(array_column($gainsParOperateur, 'total')) ?>;

    new Chart(document.getElementById('gainsOperateurChart'), {
        type: 'bar',
        data: {
            labels: operateurLabels,
            datasets: [{
                label: 'Gains (Ar)',
                data: operateurData,
                backgroundColor: '#0ea5e9',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
