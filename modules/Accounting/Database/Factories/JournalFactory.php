<?php
declare(strict_types=1);
namespace Modules\Accounting\Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Accounting\Domain\Models\Journal;
final class JournalFactory extends Factory { protected $model = Journal::class; public function definition(): array { return ['id'=>(string)Str::uuid(),'organization_id'=>(string)Str::uuid(),'company_id'=>(string)Str::uuid()]; } }

