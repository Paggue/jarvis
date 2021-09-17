<?php

namespace Lara\Jarvis\Tests;


use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lara\Jarvis\Models\BankAccount;
use Lara\Jarvis\Models\Traits\HasComments;
use Laravel\Passport\HasApiTokens;

class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use HasComments, Authorizable, Authenticatable, HasFactory, HasApiTokens;

    protected $guarded = [];

    protected $table = 'users';

    protected static function newFactory ()
    {
        return UserFactory::new();
    }

    public function isSuperAdmin ()
    {
        return true;
    }

    public function bankAccounts (): morphMany
    {
        return $this->morphMany(BankAccount::class, 'bank_accountable');
    }
}
