<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situation des Gains - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #1e293b;
            color: #fff;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #0ea5e9;
            color: #fff;
        }

        .card-stat {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s;
        }

        .card-stat:hover {
            transform: translateY(-3px);
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Barre latérale d'administration corrigée -->
            <aside class="col-md-3 col-lg-2 d-md-block sidebar p-3 collapse">
                <h4 class="text-white mb-4 ps-2"><i class="fa-solid fa-chart-line text-info me-2"></i>Admin Panel</h4>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="<?= base_url('admin/dashboard') ?>" class="nav-link"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/clients') ?>" class="nav-link"><i class="fa-solid fa-users me-2"></i>Clients</a>
                    </li>
                </ul>
                <hr>
                <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Déconnexion</a>
            </aside>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                    <h1 class="h3 fw-bold">Situation des Gains de la Plateforme</h1>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                            <i class="fa-solid fa-circle me-1" style="font-size: 8px;"></i> Vue Financière
                        </span>
                    </div>
                </div>

                <!-- 1. Résumé Global & Inclusions (KPI Cards) -->
                <div class="row g-3 mb-4">
                    <!-- Gain Global Total -->
                    <div class="col-12 col-sm-6 col-xl-4">
                        <div class="card card-stat shadow-sm p-3 border-start border-success border-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-success-subtle text-success me-3">
                                    <i class="fa-solid fa-wallet fa-lg"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-semibold text-uppercase">Gain Global Total</small>
                                    <h3 class="mb-0 fw-bold text-success"><?= number_format($gainGlobal ?? 0, 2, ',', ' ') ?> Ar</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($detailGains)) : ?>
                        <!-- Frais / Commission Inter-Op -->
                        <div class="col-12 col-sm-6 col-xl-4">
                            <div class="card card-stat shadow-sm p-3 border-start border-info border-4">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-info-subtle text-info me-3">
                                        <i class="fa-solid fa-handshake fa-lg"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted fw-semibold text-uppercase">Frais / Commission Inter-Op</small>
                                        <h3 class="mb-0 fw-bold text-info">
                                            <?= number_format($detailGains['total_commissions_interop'] ?? 0, 2, ',', ' ') ?> Ar
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 2. Zone des Graphiques -->
                <div class="row mb-4">
                    <!-- Chart 1 : Gains par Type d'Opération -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold">Gains par Type d'Opération</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartGainsOps"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Chart 2 : Gains par Opérateur -->
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold">Gains par Opérateur</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartGainsOperateurs"></canvas>
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
        // --- Extraction dynamique des données PHP vers JS ---
        const gainsOpsRaw = <?= json_encode($gainsOps ?? []) ?>;
        const labelsOps = gainsOpsRaw.map(item => item.libelle || item.operation || 'Op ' + item.operation_id);
        const valuesOps = gainsOpsRaw.map(item => parseFloat(item.gain_total || item.gain || 0));

        const gainsOperateursRaw = <?= json_encode($gainsOperateurs ?? []) ?>;
        const labelsOperateurs = gainsOperateursRaw.map(item => item.operateur || item.nom || item.nom_op || 'Opérateur');
        const valuesOperateurs = gainsOperateursRaw.map(item => parseFloat(item.gain_total || item.gain || 0));

        const operationColors = ['#0ea5e9', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6'];

        // 1. Graphique Doughnut : Gains par Opération
        new Chart(document.getElementById('chartGainsOps'), {
            type: 'doughnut',
            data: {
                labels: labelsOps,
                datasets: [{
                    data: valuesOps,
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

        // 2. Graphique Barres : Gains par Opérateur
        new Chart(document.getElementById('chartGainsOperateurs'), {
            type: 'bar',
            data: {
                labels: labelsOperateurs,
                datasets: [{
                    label: 'Gain (Ar)',
                    data: valuesOperateurs,
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
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) {
                                return val.toLocaleString('fr-FR') + ' Ar';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>