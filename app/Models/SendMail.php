<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class SendMail extends Model
{
    use CrudTrait;
    protected $fillable = [
        'mail_name',
        'team_id',
    ];
    protected $table = 'mails';
    protected $guarded = ['id'];
}
