<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - Mobile Money</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .error-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        h1 {
            color: #d32f2f;
            margin-top: 0;
        }
        p {
            color: #666;
            line-height: 1.6;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #1976d2;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a:hover {
            background-color: #1565c0;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Une erreur est survenue</h1>
        <p><?= esc($message) ?></p>
        <?php if (!empty($exception)): ?>
            <p style="font-size: 12px; color: #999; margin-top: 20px;">
                <?= esc(get_class($exception)) ?>
            </p>
        <?php endif; ?>
        <a href="/login">Retour à la page de connexion</a>
    </div>
</body>
</html>
