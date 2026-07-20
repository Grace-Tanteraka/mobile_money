<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php if (isset($clients) && !empty($clients)) : ?>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Numéro de téléphone</th>
                    <th>Opérateur</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client) : ?>
                    <tr>
                        <td><?= esc($client['id']) ?></td>
                        <td><?= esc($client['nom']) ?></td>
                        <td><?= esc($client['prenom']) ?></td>
                        <td><?= esc($client['email']) ?></td>
                        <td><?= esc($client['numero_telephone']) ?></td>
                        <td><?= esc($client['operateur_nom']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucun client trouvé.</p>
    <?php endif; ?>
</body>
</html>