<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="<?= site_url('client/retrait') ?>" method="post">
        <?= /* csrf_field() */ 1 ?>
        <label for="montant">Montant :</label>
        <input type="number" id="montant" name="montant" step="0.01" required>
        <br><br>
        <label for="client_cible">Numero du destinataire :</label>
        <input type="text" id="client_cible" name="client_cible_num" required>
        <br><br>
        <p id="frais"></p>

        <input type="submit" value="Effectuer le retrait">
    </form>
</body>
</html>