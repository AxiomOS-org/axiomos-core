<?php

declare(strict_types=1);

/** @var string $title */
/** @var string $active */
/** @var list<array<string, mixed>> $items */

ob_start();
?>
<div class="card">
    <div class="actions">
        <h1>Companies</h1>
        <a href="/api/companies" class="muted">API</a>
    </div>
    <p class="muted">Legal entities belonging to an organization.</p>
    <table>
        <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Organization</th>
            <th>Status</th>
            <th>Currency</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars((string) $item['code']) ?></td>
                <td><?= htmlspecialchars((string) $item['name']) ?></td>
                <td class="muted"><?= htmlspecialchars((string) ($item['organization_id'] ?? '')) ?></td>
                <td><span class="badge"><?= htmlspecialchars((string) $item['status']) ?></span></td>
                <td><?= htmlspecialchars((string) $item['currency']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
