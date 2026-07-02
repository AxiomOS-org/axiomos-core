<?php

declare(strict_types=1);

namespace Modules\Organization\Domain\Models;

use App\Platform\Support\AuditableInterface;
use App\Platform\Support\PlatformEntityInterface;
use App\Platform\Support\VersionableInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Modules\Organization\Domain\Models\Concerns\HasPlatformEntityType;

/**
 * Base model for organization hierarchy entities with UUID primary keys.
 */
abstract class BaseEntityModel extends Model implements AuditableInterface, PlatformEntityInterface, VersionableInterface
{
    use HasPlatformEntityType;
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, mixed>
     */
    public function toAuditSnapshot(): array
    {
        return $this->attributesToArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function toVersionSnapshot(): array
    {
        return $this->attributesToArray();
    }
}
