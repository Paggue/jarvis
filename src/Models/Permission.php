<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['description'];

    protected static function newFactory()
    {
        return \Lara\Jarvis\Database\Factories\PermissionFactory::new();
    }

    public function roles ()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }
}
