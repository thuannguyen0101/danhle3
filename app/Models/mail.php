<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mail extends Model
{
    use CrudTrait;
    protected $fillable = [
        'mail_name',
        'team_id',
    ];
    protected $table = 'mails';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
}
