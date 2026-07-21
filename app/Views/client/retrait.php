<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retrait - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background-color: #1e293b; color: #fff; }
        .sidebar .nav-link { color: #94a3b8; border-radius: 8px; margin-bottom: 5px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #0ea5e9; color: #fff; }
        .op-card { border: none; border-radius: 14px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); }
        .op-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; }
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
                    <a href="<?= base_url('client/retrait') ?>" class="nav-link active"><i class="fa-solid fa-minus-circle me-2"></i>Retrait</a>
                </li>
                <li>
                    <a href="<?= base_url('client/historique') ?>" class="nav-link"><i class="fa-solid fa-history me-2"></i>Historique</a>
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
                <h1 class="h3 fw-bold">Effectuer un retrait</h1>
            </div>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card op-card p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="op-icon bg-danger-subtle text-danger me-3">
                                <i class="fa-solid fa-minus-circle fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Retrait d'argent</h5>
                                <small class="text-muted">Le montant, les frais et le solde sont vérifiés automatiquement</small>
                            </div>
                        </div>

                        <form action="<?= site_url('client/processRetrait') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Montant (Ar)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Ar</span>
                                    <input type="number" name="montant" class="form-control" min="100" step="0.01" placeholder="ex: 20000" required>
                                </div>
                                <small class="text-muted">Montant minimum : 100 Ar. Des frais de retrait s'appliquent.</small>
                            </div>
                            <button type="submit" class="btn btn-danger w-100 fw-semibold text-white">
                                <i class="fa-solid fa-minus-circle me-2"></i>Valider le retrait
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
