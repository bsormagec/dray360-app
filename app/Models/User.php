<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\Builder;
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

    public function setDeactivatedAtAttribute($value)
    {
        if (is_bool($value)) {
            $this->attributes['deactivated_at'] = $value ? now() : null;
            return;
        }

        $this->attributes['deactivated_at'] = $value;
    }

    public function deactivate(bool $save = true): self
    {
        $this->deactivated_at = now();

        if ($save) {
            $this->save();
        }

        return $this;
    }

    public function activate(bool $save = true): self
    {
        $this->deactivated_at = null;

        if ($save) {
            $this->save();
        }

        return $this;
    }

    public function scopeActive(Builder $builder, $active = true)
    {
        if ($active) {
            return $builder->whereNull('deactivated_at');
        }

        return $builder->whereNotNull('deactivated_at');
    }

    public static function bulkDeactivate(array $ids): bool
    {
        return static::whereIn('id', $ids)->update(['deactivated_at' => now()]);
    }

    public static function bulkActivate(array $ids): bool
    {
        return static::whereIn('id', $ids)->update(['deactivated_at' => null]);
    }

    public function isActive(): bool
    {
        return ! $this->deactivated_at;
    }
}
