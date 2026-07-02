<?php
declare(strict_types=1);
namespace Modules\Accounting\Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Accounting\Domain\Models\Document;
final class DocumentFactory extends Factory { protected $model = Document::class; public function definition(): array { return ['id'=>(string)Str::uuid(),'organization_id'=>(string)Str::uuid(),'company_id'=>(string)Str::uuid()]; } }

