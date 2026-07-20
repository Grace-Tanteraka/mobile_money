<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert - Mobile Money</title>
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

        .op-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        }

        .op-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hidden {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Barre latérale -->
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
                        <a href="<?= base_url('client/transfert') ?>" class="nav-link active"><i class="fa-solid fa-arrow-right-arrow-left me-2"></i>Transfert</a>
                    </li>
                    <li>
                        <a href="<?= base_url('client/retrait') ?>" class="nav-link"><i class="fa-solid fa-minus-circle me-2"></i>Retrait</a>
                    </li>
                    <li>
                        <a href="<?= base_url('client/historique') ?>" class="nav-link"><i class="fa-solid fa-history me-2"></i>Historique</a>
                    </li>
                </ul>
                <hr>
                <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Déconnexion</a>
            </aside>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                    <h1 class="h3 fw-bold">Effectuer un transfert</h1>
                </div>

                <!-- Notifications Flash CodeIgniter 4 -->
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>

                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        <div class="card op-card p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="op-icon bg-primary-subtle text-primary me-3">
                                    <i class="fa-solid fa-paper-plane fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Envoyer de l'argent</h5>
                                    <small class="text-muted">Transférez des fonds instantanément vers un ou plusieurs numéros</small>
                                </div>
                            </div>

                            <form action="<?= site_url('/client/transferer') ?>" method="post" id="transfert-form">
                                <?= csrf_field() ?>

                                <!-- Mode d'envoi : Unique ou Multiple -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Mode d'envoi :</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type_envoi" value="unique" id="type_unique" checked>
                                            <label class="form-check-label fw-medium" for="type_unique">
                                                Destinataire unique
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type_envoi" value="multiple" id="type_multiple">
                                            <label class="form-check-label fw-medium" for="type_multiple">
                                                Envoi multiple (même réseau)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Champ Téléphone(s) -->
                                <div class="mb-3">
                                    <label for="telephone" id="label-telephone" class="form-label fw-semibold">Numéro du destinataire :</label>
                                    <input type="text" id="telephone" name="telephone" class="form-control" placeholder="Ex: 0341234567" required>
                                    <small id="telephone-help" class="form-text text-info hidden">
                                        <i class="fa-solid fa-info-circle me-1"></i>Séparez les numéros par des virgules (ex: 0341234567, 0349876543)
                                    </small>
                                    <div id="telephone-error" class="text-danger small mt-1 fw-medium"></div>
                                </div>

                                <!-- Montant -->
                                <div class="mb-3">
                                    <label for="montant" id="label-montant" class="form-label fw-semibold">Montant à envoyer (Ar) :</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-medium">Ar</span>
                                        <input type="number" id="montant" name="montant" class="form-control" step="0.01" min="100" placeholder="100 Ar min." required>
                                    </div>
                                    <small id="montant-help" class="form-text text-info hidden">
                                        <i class="fa-solid fa-calculator me-1"></i>Le montant total sera divisé équitablement entre tous les destinataires.
                                    </small>
                                </div>

                                <!-- Option d'inclusion des frais de retrait -->
                                <div class="mb-4 form-check bg-light p-3 rounded-3 border" id="bloc-frais-retrait">
                                    <div class="ms-2">
                                        <input type="checkbox" class="form-check-input" name="inclure_frais_retrait" value="1" id="inclure_frais_retrait">
                                        <label class="form-check-label fw-semibold" for="inclure_frais_retrait">
                                            Inclure les frais de retrait pour le destinataire
                                        </label>
                                        <div class="form-text text-muted small mt-1">(Valable uniquement si le destinataire est sur le même réseau)</div>
                                    </div>
                                </div>

                                <button type="submit" id="submit-btn" class="btn btn-primary w-100 fw-semibold text-white py-2">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Effectuer le transfert
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const radioUnique = document.getElementById('type_unique');
        const radioMultiple = document.getElementById('type_multiple');
        const labelTel = document.getElementById('label-telephone');
        const telInput = document.getElementById('telephone');
        const telHelp = document.getElementById('telephone-help');
        const telError = document.getElementById('telephone-error');
        const montantHelp = document.getElementById('montant-help');
        const blocFraisRetrait = document.getElementById('bloc-frais-retrait');
        const chkFraisRetrait = document.getElementById('inclure_frais_retrait');

        const regexMalagasy = /^(032|033|034|037|038)\d{7}$/;

        function toggleMode() {
            if (radioMultiple.checked) {
                labelTel.textContent = "Numéros des destinataires :";
                telInput.placeholder = "0341234567, 0349876543";
                telHelp.classList.remove('hidden');
                montantHelp.classList.remove('hidden');
                
                blocFraisRetrait.classList.add('hidden');
                chkFraisRetrait.checked = false;
            } else {
                labelTel.textContent = "Numéro du destinataire :";
                telInput.placeholder = "Ex: 0341234567";
                telHelp.classList.add('hidden');
                montantHelp.classList.add('hidden');
                
                blocFraisRetrait.classList.remove('hidden');
            }
            validerTelephones();
        }

        radioUnique.addEventListener('change', toggleMode);
        radioMultiple.addEventListener('change', toggleMode);

        function validerTelephones() {
            telError.textContent = '';
            const value = telInput.value.trim();

            if (!value) return;

            if (radioUnique.checked) {
                if (value.length !== 10) {
                    telError.textContent = 'Le numéro doit comporter exactement 10 chiffres.';
                } else if (!regexMalagasy.test(value)) {
                    telError.textContent = 'Numéro invalide (doit commencer par 032, 033, 034, 037 ou 038).';
                }
            } else {
                const listeTels = value.split(',').map(t => t.trim()).filter(t => t.length > 0);

                if (listeTels.length < 2) {
                    telError.textContent = 'Entrez au moins 2 numéros séparés par des virgules pour un envoi multiple.';
                    return;
                }

                for (let i = 0; i < listeTels.length; i++) {
                    const t = listeTels[i];
                    if (t.length !== 10 || !regexMalagasy.test(t)) {
                        telError.textContent = 'Le numéro "' + t + '" est invalide (10 chiffres, préfixe 032/033/034/037/038 requis).';
                        break;
                    }
                }
            }
        }

        telInput.addEventListener('input', validerTelephones);
    </script>
</body>

</html>