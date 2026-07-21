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
                    <li class="nav-item">
                        <a href="<?= base_url('admin/promotion') ?>" class="nav-link"><i class="fa-solid fa-users me-2"></i>Gerer la promotion</a>
                    </li>
                </ul>
                <hr>
                <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Déconnexion</a>
            </aside>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <form action="<?= site_url('admin/promotion') ?>" method="post">
                    <?= csrf_field() ?>
                </form>
            </main>
        </div>
    </div>
</body>
</html>