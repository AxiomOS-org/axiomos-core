<?php

declare(strict_types=1);

/** @var string $title */
/** @var string $active */
/** @var list<array<string, mixed>> $items */

ob_start();
?>
<div class="card">
    <div class="actions">
        <h1>Organizations</h1>
        <a href="/api/organizations" class="muted">API</a>
    </div>
    <p class="muted">Root tenant entities in the AxiomOS hierarchy.</p>
    <table>
        <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Status</th>
            <th>Country</th>
            <th>Timezone</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars((string) $item['code']) ?></td>
                <td><?= htmlspecialchars((string) $item['name']) ?></td>
                <td><span class="badge"><?= htmlspecialchars((string) $item['status']) ?></span></td>
                <td><?= htmlspecialchars((string) $item['country']) ?></td>
                <td><?= htmlspecialchars((string) $item['timezone']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
