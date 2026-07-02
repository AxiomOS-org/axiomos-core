<?php
declare(strict_types=1);
namespace Modules\Accounting\Application\DTOs;
final class PostingResult { public function __construct(public readonly bool $success, public readonly ?string $journalId, public readonly ?string $documentNumber, public readonly array $errors=[], public readonly array $meta=[]) {} public static function ok(string $journalId, ?string $documentNumber, array $meta=[]): self { return new self(true,$journalId,$documentNumber,[],$meta); } public static function failed(array $errors, array $meta=[]): self { return new self(false,null,null,$errors,$meta); } public function toArray(): array { return ['success'=>$this->success,'journal_id'=>$this->journalId,'document_number'=>$this->documentNumber,'errors'=>$this->errors,'meta'=>$this->meta]; } }

