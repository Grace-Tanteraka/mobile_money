<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php if (isset($transactions) && !empty($transactions)) : ?>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Opération</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction) : ?>
                    <tr>
                        <td><?= esc($transaction['id']) ?></td>
                        <td><?= esc($transaction['montant']) ?></td>
                        <td><?= esc($transaction['date']) ?></td>
                        <td><?= esc($transaction['operation_nom']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucune transaction trouvée pour ce client.</p>
    <?php endif; ?>
</body>
</html>