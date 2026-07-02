<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars((string) ($title ?? 'AxiomOS'), ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
<main>
    <?= $content ?? '' ?>
</main>
</body>
</html>
