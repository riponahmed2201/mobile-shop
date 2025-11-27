<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'role',
        'photo',
        'is_active',
        'password',
        'created_by',
        'updated_by',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    // A user belongs to a tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // A user may create many users
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // A user may be updated by another user
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Example: user → sales/invoices
    // public function sales()
    // {
    //     return $this->hasMany(Sale::class);
    // }

    // Example: purchase entries by this user
    // public function purchases()
    // {
    //     return $this->hasMany(Purchase::class);
    // }

    // Example: stock entries created by the user
    // public function stockEntries()
    // {
    //     return $this->hasMany(StockEntry::class);
    // }

    // Profile image URL
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : asset('images/default-user.png');
    }

    // Automatically hash password
    public function setPasswordAttribute($value)
    {
        if ($value && strlen($value) < 60) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    // Only active users
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Filter by role
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Multi-tenant scope
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
