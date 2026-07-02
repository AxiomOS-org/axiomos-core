<?php

declare(strict_types=1);

/** @var string $title */
/** @var string $active */
/** @var list<array<string, mixed>> $items */

ob_start();
?>
<div class="card">
    <div class="actions">
        <h1>Branches</h1>
        <a href="/api/branches" class="muted">API</a>
    </div>
    <p class="muted">Physical or logical sites under a company.</p>
    <table>
        <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Company</th>
            <th>Status</th>
            <th>Locale</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars((string) $item['code']) ?></td>
                <td><?= htmlspecialchars((string) $item['name']) ?></td>
                <td class="muted"><?= htmlspecialchars((string) ($item['company_id'] ?? '')) ?></td>
                <td><span class="badge"><?= htmlspecialchars((string) $item['status']) ?></span></td>
                <td><?= htmlspecialchars((string) $item['locale']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
