<?php
declare(strict_types=1);
namespace Modules\Accounting\Application\DTOs;
final class PostingPreview { public function __construct(public readonly bool $balanced, public readonly string $debitTotal, public readonly string $creditTotal, public readonly array $lines, public readonly array $errors) {} public function toArray(): array { return ['balanced'=>$this->balanced,'debit_total'=>$this->debitTotal,'credit_total'=>$this->creditTotal,'lines'=>$this->lines,'errors'=>$this->errors]; } }

