<?php
declare(strict_types=1);
namespace Modules\Accounting\Application\DTOs;
final class ReversalRequest { public function __construct(public readonly string $journalId, public readonly string $reason, public readonly string $idempotencyKey, public readonly string $companyId, public readonly ?string $organizationId=null, public readonly ?string $branchId=null, public readonly ?string $departmentId=null) {} }

