<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Espace Client | Dashboard</title>
    <!-- Bootstrap 5 CSS & FontAwesome Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
            <!-- Sidebar Navigation -->
            <aside class="col-md-3 col-lg-2 d-md-block sidebar p-3 collapse">
                <h4 class="text-white mb-4 ps-2"><i class="fa-solid fa-wallet text-info me-2"></i>Mon Espace</h4>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="<?= base_url('client/dashboard') ?>" class="nav-link"><i class="fa-solid fa-chart-line me-2"></i>Tableau de bord</a>
                    </li>
                    <li>
                        <a href="<?= base_url('client/depot') ?>" class="nav-link active"><i class="fa-solid fa-plus-circle me-2"></i>Dépôt</a>
                    </li>
                    <li>
                        <a href="<?= base_url('client/transfert') ?>" class="nav-link"><i class="fa-solid fa-arrow-right-arrow-left me-2"></i>Transfert</a>
                    </li>
                    <li>
                        <a href="<?= base_url('client/retrait') ?>" class="nav-link"><i class="fa-solid fa-minus-circle me-2"></i>Retrait</a>
                    </li>
                    <li>
                        <a href="<?= base_url('client/historique') ?>" class="nav-link"><i class="fa-solid fa-history me-2"></i>Historique</a>
                    </li>
                    <li>
                        <a href="<?= base_url('client/epargne') ?>" class="nav-link"><i class="fa-solid fa-history me-2"></i>Epargne</a>
                    </li>
                </ul>
                <hr />
                <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Déconnexion</a>
            </aside>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">

                <form action="<?= site_url('client/epargne_modifie')  ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">% Epargne</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">%</span>
                            <input type="number" name="epargne" class="form-control" min="5" step="0.01" placeholder="ex: 50000" required />
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-semibold text-white">
                        <i class="fa-solid fa-plus-circle me-2"></i>Modifier
                    </button>
                </form>
            </main>

        </div>
    </div>

</body>