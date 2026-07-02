<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Contact extends PlatformEntityModel
{
    protected $table = 'contacts';

    /** @var list<string> */
    protected $fillable = [
        'identity_id',
        'contact_type',
        'value',
        'is_primary',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'is_primary' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'identity.contact';
    }

    /** @return BelongsTo<Identity, $this> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class);
    }
}
