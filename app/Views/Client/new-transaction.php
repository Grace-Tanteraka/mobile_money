<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Transactions</title>
    <style>
        .form-group {
            margin-bottom: 15px;
        }

        .error-text {
            color: red;
            font-size: 0.9em;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>

    <h2>Effectuer une opération</h2>

    <!-- Affichage des messages flash de CodeIgniter -->
    <?php if (session()->getFlashdata('error')) : ?>
        <p style="color: red;"><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')) : ?>
        <p style="color: green;"><?= session()->getFlashdata('success') ?></p>
    <?php endif; ?>

    <!-- Le formulaire pointe vers la méthode centrale processTransaction -->
    <form action="<?= site_url('/client/processTransaction') ?>" method="post" id="transaction-form">

        <!-- Choix de l'opération -->
        <div class="form-group">
            <label for="operation">Type d'opération :</label>
            <select id="operation" name="operation" required>
                <?php foreach ($operations as $op): ?>
                    <option value="<?= esc($op['id']) ?>"><?= esc($op['libelle']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Montant (Commun à tous) -->
        <div class="form-group">
            <label for="montant">Montant :</label>
            <input type="number" id="montant" name="montant" step="0.01" min="100" required>
            <p id="montant-error" class="error-text"></p>
        </div>

        <!-- Bloc Destinataire (Affiché uniquement pour le Transfert) -->
        <div id="bloc-destinataire" class="form-group hidden">
            <label for="client_cible">Numéro du destinataire :</label>
            <input type="text" id="client_cible" name="client_cible_num">
            <p id="frais-error" class="error-text"></p>
            <p id="frais"></p>
        </div>

        <input type="submit" id="submit-btn" value="Effectuer le dépôt">
    </form>

    <script>
        const operationSelect = document.getElementById('operation');
        const blocDestinataire = document.getElementById('bloc-destinataire');
        const destinataireInput = document.getElementById('client_cible');
        const submitBtn = document.getElementById('submit-btn');
        const errorSpan = document.getElementById('frais-error');

        // 1. Gestion de l'affichage dynamique selon l'opération sélectionnée
        operationSelect.addEventListener('change', function() {
            const val = this.value;

            if (val === '3') { // Transfert
                blocDestinataire.classList.remove('hidden');
                destinataireInput.setAttribute('required', 'required');
                submitBtn.value = "Effectuer le transfert";
            } else { // Dépôt ou Retrait
                blocDestinataire.classList.add('hidden');
                destinataireInput.removeAttribute('required');
                destinataireInput.value = '';
                errorSpan.textContent = '';
                submitBtn.value = val === '1' ? "Effectuer le dépôt" : "Effectuer le retrait";
            }
        });

        // 2. Validation Regex du numéro de téléphone (uniquement pour le transfert)
        destinataireInput.addEventListener('input', function() {
            const clientCibleNum = this.value;
            const regexCalcul = /^(032|033|034|037|038)\d{7}$/;

            errorSpan.textContent = '';

            if (clientCibleNum.length < 10) {
                errorSpan.textContent = 'Le numéro du destinataire doit contenir au moins 10 caractères.';
                return;
            }

            if (clientCibleNum.length > 10) {
                errorSpan.textContent = 'Le numéro du destinataire ne doit pas dépasser 10 caractères.';
                return;
            }

            if (clientCibleNum.length === 10 && !regexCalcul.test(clientCibleNum)) {
                errorSpan.textContent = 'Le numéro est invalide. Il doit commencer par 032, 033, 034, 037 ou 038 et faire 10 chiffres.';
            }
        });
    </script>
</body>

</html>