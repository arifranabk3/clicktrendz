<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasTenants, HasDefaultTenant, FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
        // Force-Enable Super Admin access for the master email or role
        if ($this->email === 'arif@ecombrain.com' || $this->hasRole('super_admin')) {
            return true;
        }

        // Only allow regular users to access the 'admin_portal' panel
        return $panel->getId() === 'admin_portal';
    }

    public function getTenants(Panel $panel): Collection
    {
        if ($this->hasRole('super_admin') || $this->email === 'arif@ecombrain.com') {
            return Business::all();
        }

        return new Collection(array_filter([$this->business]));
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->hasRole('super_admin') || $this->email === 'arif@ecombrain.com') {
            return true;
        }

        return $this->business_id === $tenant->id;
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        if ($this->hasRole('super_admin') || $this->email === 'arif@ecombrain.com') {
            return Business::first();
        }

        return $this->business;
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'business_id',
        'vendor_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
