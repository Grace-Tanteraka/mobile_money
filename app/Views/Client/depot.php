<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="<?= site_url('/client/depot') ?>" method="post">
        <label for="montant">Montant</label>
        <input type="number" id="montant" name="montant" step="0.01" required>

        <input type="submit" value="Effectuer le depot">
    </form>
</body>
</html>