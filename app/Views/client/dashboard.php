<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client | Dashboard</title>
    <!-- Bootstrap 5 CSS & FontAwesome Icons -->
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
        <!-- Sidebar Navigation -->
        <aside class="col-md-3 col-lg-2 d-md-block sidebar p-3 collapse">
            <h4 class="text-white mb-4 ps-2"><i class="fa-solid fa-wallet text-info me-2"></i>Mon Espace</h4>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="#" class="nav-link active"><i class="fa-solid fa-chart-line me-2"></i>Tableau de bord</a>
                </li>
                <li>
                    <a href="#" class="nav-link"><i class="fa-solid fa-arrow-right-arrow-left me-2"></i>Transactions</a>
                </li>
                <li>
                    <a href="#" class="nav-link"><i class="fa-solid fa-user me-2"></i>Mon Profil</a>
                </li>
                <li>
                    <a href="#" class="nav-link"><i class="fa-solid fa-gear me-2"></i>Paramètres</a>
                </li>
            </ul>
            <hr>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Déconnexion</a>
        </aside>

        <!-- Contenu Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <!-- Topbar / En-tête -->
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <h1 class="h3 fw-bold">Bonjour, <?= session()->get('name') ?? 'Client' ?> 👋</h1>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-circle me-1" style="font-size: 8px;"></i> Compte Actif
                    </span>
                </div>
            </div>

            <!-- Cartes de Statistiques / Résumé -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary-subtle text-primary me-3">
                                <i class="fa-solid fa-wallet fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Solde Disponible</small>
                                <h3 class="mb-0 fw-bold">250 000 Ar</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success-subtle text-success me-3">
                                <i class="fa-solid fa-arrow-down-left fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Total Reçu</small>
                                <h3 class="mb-0 fw-bold">1 200 000 Ar</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-danger-subtle text-danger me-3">
                                <i class="fa-solid fa-arrow-up-right fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Total Envoyé</small>
                                <h3 class="mb-0 fw-bold">950 000 Ar</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Raccourcis d'actions rapides -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm p-3">
                        <h5 class="fw-bold mb-3">Actions rapides</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transferModal">
                                <i class="fa-solid fa-paper-plane me-2"></i>Effectuer un transfert
                            </button>
                            <button class="btn btn-outline-secondary">
                                <i class="fa-solid fa-qrcode me-2"></i>Mon QR Code
                            </button>
                            <button class="btn btn-outline-secondary">
                                <i class="fa-solid fa-file-invoice me-2"></i>Télécharger Relevé
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des Dernières Transactions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Dernières opérations</h5>
                    <a href="#" class="btn btn-sm btn-link text-decoration-none">Voir tout</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>Destinataire / Expéditeur</th>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-danger-subtle text-danger"><i class="fa-solid fa-arrow-up"></i> Envoi</span></td>
                                <td>034 12 345 67 (Rabe)</td>
                                <td>20 Juillet 2026</td>
                                <td class="fw-bold text-danger">- 50 000 Ar</td>
                                <td><span class="badge bg-success">Complété</span></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success-subtle text-success"><i class="fa-solid fa-arrow-down"></i> Dépôt</span></td>
                                <td>Dépôt Cash Point</td>
                                <td>19 Juillet 2026</td>
                                <td class="fw-bold text-success">+ 100 000 Ar</td>
                                <td><span class="badge bg-success">Complété</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Modal de Transfert Rapide -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Nouveau Transfert</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('client/transferer') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Numéro du destinataire</label>
                        <input type="text" name="telephone" class="form-control" placeholder="ex: 0340000000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Montant (Ar)</label>
                        <input type="number" name="montant" class="form-control" min="1000" placeholder="ex: 10000" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Valider le transfert</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>