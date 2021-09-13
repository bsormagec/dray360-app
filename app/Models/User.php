<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\Deactivatable;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property string $name
 * @property string $email
 * @property string $original_email
 * @property \Carbon\Carbon $deactivated_at
 * @property array $configuration
 */
class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;
    use HasApiTokens;
    use BelongsToCompany;
    use SoftDeletes;
    use Deactivatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'position',
        'org',
        't_company_id',
        'deactivated_at',
        'configuration',
        'original_email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'configuration' => 'json',
    ];

    public function isSuperadmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public static function bulkDeactivate(array $ids): bool
    {
        return static::whereIn('id', $ids)->update(['deactivated_at' => now()]);
    }

    public static function bulkActivate(array $ids): bool
    {
        return static::whereIn('id', $ids)->update(['deactivated_at' => null]);
    }
}
