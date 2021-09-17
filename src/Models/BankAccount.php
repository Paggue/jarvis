<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lara\Jarvis\Models\Traits\HasSanitizer;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class BankAccount extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable, HasSanitizer;

    protected $fillable = [
        'bank_id',
        'holder',
        'document',
        'account_type',
        'agency',
        'agency_digit',
        'account',
        'account_digit',
        'account',
        'pix_key',
        'operation',
        'main_account',
    ];

    protected static function newFactory ()
    {
        return \Lara\Jarvis\Database\Factories\BankAccountFactory::new();
    }

    protected $casts = [
        'operation' => 'int',
        'info'      => 'json'
    ];

    public function bankAccountable ()
    {
        return $this->morphTo();
    }

    public function bank ()
    {
        return $this->belongsTo(Bank::class);
    }

    public function comments ()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
