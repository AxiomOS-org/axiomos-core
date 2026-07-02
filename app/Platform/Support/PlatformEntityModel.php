<?php

declare(strict_types=1);

namespace App\Platform\Support;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Base model every platform-aware ERP entity can extend.
 */
abstract class PlatformEntityModel extends Model implements
    PlatformEntityInterface,
    AuditableInterface,
    VersionableInterface
{
    use HasPlatformAuditColumns;
    use HasUuids;
    use InteractsWithPlatform;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    public function getEntityType(): string
    {
        return static::entityType();
    }

    public function getEntityId(): string
    {
        return (string) $this->getKey();
    }

    /**
     * Dot-notation type, e.g. organization.organization.
     */
    abstract public static function entityType(): string;

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

