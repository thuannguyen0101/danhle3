<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class SendMail extends Model
{
    use CrudTrait;

    protected $table = 'mails';
    protected $guarded = ['id'];
    protected $fillable = [
        'mail_name',
        'team_id',
    ];
}
