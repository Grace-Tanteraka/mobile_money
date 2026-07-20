<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }
        .login-card .card-header {
            background: #0ea5e9;
            color: #fff;
            text-align: center;
            padding: 2rem 1.5rem 1.5rem;
            border: none;
            transition: background 0.2s;
        }
        .login-card.mode-admin .card-header { background: #1e293b; }
        .login-card .card-header .brand-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-size: 1.75rem;
        }
        .login-card .card-body { padding: 2rem 1.75rem; }
        .form-control:focus, .form-select:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.2);
        }
        .btn-login {
            border: none;
            font-weight: 600;
            padding: 0.6rem 0;
        }
        .switch-mode-link {
            display: block;
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.9rem;
            color: #64748b;
            text-decoration: none;
        }
        .switch-mode-link:hover { color: #0ea5e9; }
    </style>
</head>
<body>
    <div class="login-card card" id="login-card">
        <div class="card-header">
            <div class="brand-icon"><i class="fa-solid fa-wallet" id="brand-icon"></i></div>
            <h4 class="fw-bold mb-0" id="card-title">Mobile Money</h4>
            <small class="opacity-75" id="card-subtitle">Connectez-vous avec votre numéro</small>
        </div>
        <div class="card-body">

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger py-2 small">
                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success py-2 small">
                    <i class="fa-solid fa-circle-check me-1"></i>
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('login') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="role" id="role" value="client">

                <!-- Mode CLIENT : uniquement le numéro de téléphone -->
                <div id="bloc-client">
                    <div class="mb-3">
                        <label for="telephone" class="form-label fw-semibold">Numéro de téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-phone"></i></span>
                            <input type="text" name="telephone" id="telephone" class="form-control" placeholder="ex: 0341234567" required>
                        </div>
                    </div>
                </div>

                <!-- Mode ADMIN : email + mot de passe -->
                <div id="bloc-admin" style="display: none;">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="mdp" class="form-label fw-semibold">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="mdp" id="mdp" class="form-control">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-login btn-primary w-100 text-white" id="submit-btn">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Se connecter
                </button>
            </form>

            <a href="#" class="switch-mode-link" id="switch-link" onclick="switchMode(event)">
                <i class="fa-solid fa-user-shield me-1"></i>Se connecter en tant qu'administrateur
            </a>
        </div>
    </div>

    <script>
        let isAdminMode = false;

        function switchMode(e) {
            e.preventDefault();
            isAdminMode = !isAdminMode;

            const role = document.getElementById('role');
            const blocClient = document.getElementById('bloc-client');
            const blocAdmin = document.getElementById('bloc-admin');
            const telephone = document.getElementById('telephone');
            const email = document.getElementById('email');
            const mdp = document.getElementById('mdp');
            const switchLink = document.getElementById('switch-link');
            const cardTitle = document.getElementById('card-title');
            const cardSubtitle = document.getElementById('card-subtitle');
            const brandIcon = document.getElementById('brand-icon');
            const loginCard = document.getElementById('login-card');
            const submitBtn = document.getElementById('submit-btn');

            if (isAdminMode) {
                role.value = 'admin';
                blocClient.style.display = 'none';
                blocAdmin.style.display = 'block';
                telephone.required = false;
                email.required = true;
                mdp.required = true;

                switchLink.innerHTML = '<i class="fa-solid fa-user me-1"></i>Se connecter en tant que client';
                cardTitle.textContent = 'Espace Administrateur';
                cardSubtitle.textContent = 'Connectez-vous avec votre email';
                brandIcon.className = 'fa-solid fa-user-shield';
                loginCard.classList.add('mode-admin');
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-dark');
            } else {
                role.value = 'client';
                blocClient.style.display = 'block';
                blocAdmin.style.display = 'none';
                telephone.required = true;
                email.required = false;
                mdp.required = false;

                switchLink.innerHTML = '<i class="fa-solid fa-user-shield me-1"></i>Se connecter en tant qu\'administrateur';
                cardTitle.textContent = 'Mobile Money';
                cardSubtitle.textContent = 'Connectez-vous avec votre numéro';
                brandIcon.className = 'fa-solid fa-wallet';
                loginCard.classList.remove('mode-admin');
                submitBtn.classList.remove('btn-dark');
                submitBtn.classList.add('btn-primary');
            }
        }
    </script>
</body>
</html>
                