<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="<?= site_url('client/transfert') ?>" method="post">
        <?= /* csrf_field() */ 1 ?>
        <label for="montant">Montant :</label>
        <input type="number" id="montant" name="montant" step="0.01" required>
        <p id="montant-error"></p>
        <br><br>
        <label for="client_cible">Numero du destinataire :</label>
        <input type="text" id="client_cible" name="client_cible_num" required>
        <p id="frais-error"></p>
        <br><br>
        <p id="frais"></p>

        <input type="submit" value="Effectuer le transfert">
    </form>


    <script>
        const destinataireInput = document.getElementById('client_cible');
        const montantInput = document.getElementById('montant');

        destinataireInput.addEventListener('input', function() {
            const clientCibleNum = this.value;
            const montant = document.getElementById('montant').value;
            const errorSpan = document.getElementById('frais-error');

            const regexCalcul = /^(032|033|034|037|038)\d{7}$/;

            errorSpan.textContent = '';

            if (clientCibleNum.length < 10) {
                errorSpan.textContent = 'Le numéro du destinataire doit contenir au moins 10 caractères.';
                destinataireInput.classList.remove('is-valid');
                destinataireInput.classList.add('is-invalid');
                return;
            } else if (clientCibleNum.length > 10) {
                errorSpan.textContent = 'Le numéro du destinataire ne doit pas dépasser 10 caractères.';
                destinataireInput.classList.remove('is-valid');
                destinataireInput.classList.add('is-invalid');
                return;
            } else if (clientCibleNum.length == 10 && regexCalcul.test(clientCibleNum)) {
                destinataireInput.classList.remove('is-invalid');
                destinataireInput.classList.add('is-valid');
            } else if (clientCibleNum.length == 10 && !regexCalcul.test(clientCibleNum)) {
                errorSpan.textContent = 'Le numéro est invalide. Il doit commencer par 032, 033, 034, 037 ou 038 et faire 10 chiffres.';
                destinataireInput.classList.remove('is-valid');
                destinataireInput.classList.add('is-invalid');
                return;
            } 

        });
    </script>
</body>

</html>