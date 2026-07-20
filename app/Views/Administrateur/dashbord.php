<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrateur - Mobile Money</title>
</head>
<body>
    <h1>Dashboard Administrateur</h1>
    <p>Bienvenue, <?= esc($clientName ?? 'Administrateur') ?></p>
    <p>Vous êtes connecté en tant qu'administrateur.</p>
    <br>
    <a href="/login">Déconnexion</a>
</body>
</html>