<?php
declare(strict_types=1);
namespace Modules\Purchase\Application\Services;
use Modules\Accounting\Application\DTOs\PostingRequest;
use Modules\Accounting\Domain\Services\Contracts\PostingEngineInterface;
use Modules\Purchase\Domain\Repositories\Contracts\PurchaseBillRepositoryInterface;
final class PurchasePostingService {
    public function __construct(private readonly PostingEngineInterface $posting, private readonly PurchaseBillRepositoryInterface $documents) {}
    public function submit(array $payload): array {
        $lines = (array) ($payload['lines'] ?? []);
        if ($lines === []) {
            return ['success' => false, 'errors' => ['Posting lines are required.']];
        }
        $result = $this->posting->submit(new PostingRequest(
            (string) ($payload['idempotency_key'] ?? uniqid('purchase:', true)),
            'Purchase',
            (string) ($payload['document_type'] ?? 'purchase_document'),
            (string) ($payload['document_id'] ?? ''),
            (string) ($payload['company_id'] ?? ''),
            isset($payload['organization_id']) ? (string) $payload['organization_id'] : null,
            isset($payload['branch_id']) ? (string) $payload['branch_id'] : null,
            isset($payload['department_id']) ? (string) $payload['department_id'] : null,
            (string) ($payload['posting_date'] ?? date('Y-m-d')),
            strtoupper((string) ($payload['currency'] ?? 'USD')),
            (string) ($payload['exchange_rate'] ?? '1'),
            (string) ($payload['voucher_type'] ?? 'JV'),
            $lines,
        ));
        if ($result->success && isset($payload['id'])) {
            $doc = $this->documents->find((string) $payload['id']);
            if ($doc !== null) {
                $this->documents->update($doc, ['status' => 'posted', 'journal_id' => $result->journalId]);
            }
        }
        return $result->toArray();
    }
}