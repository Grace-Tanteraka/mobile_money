<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=4, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="<?= base_url('hash') ?>" method="post">
        <label for="input">Entrez une chaîne de caractères :</label>
        <input type="text" id="input" name="input" required>
        <br><br>
        <input type="submit" value="Générer le hash">
    </form>
    <?php if (isset($hash)) : ?>
        <p>Hash généré : <?= esc($hash) ?></p>
    <?php endif; ?>
</body>
</html>