<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
        'description',
    ];

    protected static function newFactory()
    {
        return \Lara\Jarvis\Database\Factories\RoleFactory::new();
    }

    public function permissions ()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }
}
