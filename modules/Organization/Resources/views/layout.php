<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars((string) ($title ?? 'AxiomOS')) ?></title>
    <style>
        :root { color-scheme: light dark; --bg: #0f172a; --card: #1e293b; --text: #f8fafc; --muted: #94a3b8; --accent: #38bdf8; --border: #334155; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, Segoe UI, sans-serif; background: var(--bg); color: var(--text); }
        header { padding: 1rem 2rem; border-bottom: 1px solid var(--border); display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        header a { color: var(--accent); text-decoration: none; font-weight: 600; }
        header nav a { margin-right: 1rem; color: var(--muted); }
        header nav a.active { color: var(--text); }
        main { max-width: 1100px; margin: 2rem auto; padding: 0 1rem 3rem; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; }
        h1 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: .75rem; border-bottom: 1px solid var(--border); }
        th { color: var(--muted); font-size: .85rem; text-transform: uppercase; letter-spacing: .04em; }
        .badge { display: inline-block; padding: .2rem .55rem; border-radius: 999px; background: #14532d; color: #bbf7d0; font-size: .75rem; }
        .muted { color: var(--muted); }
        form { display: grid; gap: .75rem; max-width: 480px; margin-top: 1rem; }
        label { display: grid; gap: .35rem; font-size: .9rem; }
        input, select, textarea { padding: .65rem .75rem; border-radius: 8px; border: 1px solid var(--border); background: #0b1220; color: var(--text); }
        button { padding: .7rem 1rem; border: 0; border-radius: 8px; background: var(--accent); color: #082f49; font-weight: 700; cursor: pointer; width: fit-content; }
        .actions { display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; }
    </style>
</head>
<body>
<header>
    <a href="/">AxiomOS</a>
    <nav>
        <a href="/organizations" class="<?= ($active ?? '') === 'organizations' ? 'active' : '' ?>">Organizations</a>
        <a href="/companies" class="<?= ($active ?? '') === 'companies' ? 'active' : '' ?>">Companies</a>
        <a href="/branches" class="<?= ($active ?? '') === 'branches' ? 'active' : '' ?>">Branches</a>
        <a href="/departments" class="<?= ($active ?? '') === 'departments' ? 'active' : '' ?>">Departments</a>
    </nav>
</header>
<main>
    <?= $content ?? '' ?>
</main>
</body>
</html>
