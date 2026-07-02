<?php

declare(strict_types=1);

/** @var string $title */
/** @var string $active */
/** @var list<array<string, mixed>> $items */

ob_start();
?>
<div class="card">
    <div class="actions">
        <h1>Departments</h1>
        <a href="/api/departments" class="muted">API</a>
    </div>
    <p class="muted">Operational units inside a branch (supports parent departments).</p>
    <table>
        <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Branch</th>
            <th>Parent</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars((string) $item['code']) ?></td>
                <td><?= htmlspecialchars((string) $item['name']) ?></td>
                <td class="muted"><?= htmlspecialchars((string) ($item['branch_id'] ?? '')) ?></td>
                <td class="muted"><?= htmlspecialchars((string) ($item['parent_id'] ?? '—')) ?></td>
                <td><span class="badge"><?= htmlspecialchars((string) $item['status']) ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
