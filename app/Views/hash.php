<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de hash - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hash-card { max-width: 480px; width: 100%; border: none; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="card hash-card p-4">
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-key text-info me-2"></i>Générateur de hash</h5>
        <p class="text-muted small mb-4">Outil interne pour générer un mot de passe hashé (bcrypt).</p>

        <form action="<?= base_url('hash') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="input" class="form-label fw-semibold">Chaîne de caractères</label>
                <input type="text" id="input" name="input" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-info text-white w-100 fw-semibold">
                <i class="fa-solid fa-gear me-2"></i>Générer le hash
            </button>
        </form>

        <?php if (isset($hash)) : ?>
            <div class="alert alert-success mt-3 mb-0 small" style="word-break: break-all;">
                <strong>Hash généré :</strong><br><?= esc($hash) ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
