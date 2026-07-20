<?php
$session = session();
$role = (string) ($session->get('role') ?? 'employe');
$path = trim(uri_string(), '/');

$pageTitleSection = $this->renderSection('page_title');
$pageTitle = is_string($pageTitleSection) ? trim($pageTitleSection) : '';
if ($pageTitle === '') {
    $pageTitle = 'Tableau de bord';
}

$breadcrumbSection = $this->renderSection('breadcrumb');
$breadcrumb = is_string($breadcrumbSection) ? trim($breadcrumbSection) : '';
if ($breadcrumb === '') {
    $breadcrumb = 'Accueil';
}

$toText = static function ($value): ?string {
    if ($value === null) {
        return null;
    }

    if (is_string($value)) {
        return $value;
    }

    if (is_scalar($value)) {
        return (string) $value;
    }

    if (is_array($value)) {
        $flat = [];
        array_walk_recursive($value, static function ($v) use (&$flat): void {
            if (is_scalar($v)) {
                $flat[] = (string) $v;
            }
        });
        return $flat === [] ? null : implode(' ', $flat);
    }

    return null;
};

$flashSuccess = $toText($session->getFlashdata('success'));
$flashError = $toText($session->getFlashdata('error'));
$flashInfo = $toText($session->getFlashdata('info'));
$topbarActions = $this->renderSection('topbar_actions');
$topbarActions = is_string($topbarActions) ? $topbarActions : '';

/**
 * @var array<string, array{space: string, icon: string, menu: list<array{label: string, icon: string, url: string, match: string}>}> $roleConfig
 */
$roleConfig = [
    'employe' => [
        'space' => 'Espace employe',
        'icon' => 'bi bi-briefcase',
        'menu' => [
            ['label' => 'Tableau de bord', 'icon' => 'bi bi-grid-1x2', 'url' => '/employe/dashboard', 'match' => 'employe/dashboard'],
            ['label' => 'Nouvelle demande', 'icon' => 'bi bi-plus-circle', 'url' => '/employe/demandes/nouvelle', 'match' => 'employe/demandes/nouvelle'],
            ['label' => 'Mes demandes', 'icon' => 'bi bi-calendar3', 'url' => '/employe/demandes', 'match' => 'employe/demandes'],
            ['label' => 'Mon profil', 'icon' => 'bi bi-person-gear', 'url' => '/employe/profil', 'match' => 'employe/profil'],
        ],
    ],
    'rh' => [
        'space' => 'Espace responsable',
        'icon' => 'bi bi-person-check',
        'menu' => [
            ['label' => 'Tableau de bord', 'icon' => 'bi bi-grid-1x2', 'url' => '/rh/dashboard', 'match' => 'rh/dashboard'],
            ['label' => 'Demandes', 'icon' => 'bi bi-inbox', 'url' => '/rh/demandes', 'match' => 'rh/demandes'],
            ['label' => 'Vue des soldes', 'icon' => 'bi bi-bar-chart', 'url' => '/rh/soldes', 'match' => 'rh/soldes'],
        ],
    ],
    'admin' => [
        'space' => 'Administration',
        'icon' => 'bi bi-shield-check',
        'menu' => [
            ['label' => 'Vue d\'ensemble', 'icon' => 'bi bi-speedometer2', 'url' => '/admin/dashboard', 'match' => 'admin/dashboard'],
            ['label' => 'Employés', 'icon' => 'bi bi-people', 'url' => '/admin/employes', 'match' => 'admin/employes'],
            ['label' => 'Demandes', 'icon' => 'bi bi-calendar2-week', 'url' => '/admin/demandes', 'match' => 'admin/demandes'],
        ],
    ],
];

$cfg = $roleConfig[$role] ?? $roleConfig['employe'];
$space = is_string($cfg['space'] ?? null) ? $cfg['space'] : '';
$sidebarIcon = is_string($cfg['icon'] ?? null) ? $cfg['icon'] : 'bi bi-briefcase';
$menuItems = is_array($cfg['menu'] ?? null) ? $cfg['menu'] : [];
$initials = strtoupper(substr((string) $session->get('prenom'), 0, 1) . substr((string) $session->get('nom'), 0, 1));
if ($initials === '') {
    $initials = 'U';
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?> - TechMada RH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/rh-layout.css') ?>">
</head>
<body>
    <div class="app-wrap">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-logo-icon"><i class="<?= esc($sidebarIcon) ?>"></i></div>
                <div class="sidebar-brand-name">TechMada RH<span><?= esc($space) ?></span></div>
            </div>

            <div class="sidebar-section">Menu</div>
            <ul class="sidebar-nav">
                <?php foreach ($menuItems as $item): ?>
                    <?php
                    if (!is_array($item)) {
                        continue;
                    }
                    $itemLabel = is_string($item['label'] ?? null) ? $item['label'] : '';
                    $itemIcon = is_string($item['icon'] ?? null) ? $item['icon'] : 'bi bi-dot';
                    $itemUrl = is_string($item['url'] ?? null) ? $item['url'] : '/';
                    $itemMatch = is_string($item['match'] ?? null) ? $item['match'] : '';
                    $isActive = $path === trim($itemMatch, '/');
                    ?>
                    <li>
                        <a href="<?= site_url($itemUrl) ?>" class="<?= $isActive ? 'active' : '' ?>">
                            <i class="<?= esc($itemIcon) ?>"></i>
                            <?= esc($itemLabel) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="sidebar-user">
                <div class="s-user-row">
                    <div class="avatar"><?= esc($initials) ?></div>
                    <div>
                        <div class="user-name"><?= esc(trim((string) $session->get('prenom') . ' ' . (string) $session->get('nom'))) ?></div>
                        <div class="user-role"><?= esc($role) ?></div>
                    </div>
                    <form method="post" action="<?= site_url('/logout') ?>" class="logout-form">
                        <?= csrf_field() ?>
                        <button type="submit" class="logout-btn" title="Deconnexion"><i class="bi bi-box-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="main">
            <header class="topbar">
                <div>
                    <div class="topbar-title"><?= esc($pageTitle) ?></div>
                    <div class="topbar-breadcrumb"><?= $breadcrumb ?></div>
                </div>
                <div class="topbar-actions">
                    <?= $topbarActions ?>
                </div>
            </header>

            <main class="content">
                <?php if ($flashSuccess !== null && $flashSuccess !== ''): ?>
                    <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc($flashSuccess) ?></div>
                <?php endif; ?>
                <?php if ($flashError !== null && $flashError !== ''): ?>
                    <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= esc($flashError) ?></div>
                <?php endif; ?>
                <?php if ($flashInfo !== null && $flashInfo !== ''): ?>
                    <div class="flash flash-info"><i class="bi bi-info-circle-fill"></i> <?= esc($flashInfo) ?></div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </main>

            <footer class="footer-app">
                <i class="bi bi-c-circle"></i>
                <?= date('Y') ?>
                <span>TechMada RH</span>
                - Projet CodeIgniter 4
            </footer>
        </div>
    </div>
</body>
</html>