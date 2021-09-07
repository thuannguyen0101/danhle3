<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use CrudTrait;

    protected $table = 'teams';
    protected $guarded = ['id'];

    public function team_detail(){
        return $this->hasMany(TeamDetail::class);
    }

    public function department(){
        return $this->belongsTo(Department::class,'department_id');
    }

    public function leader(){
        return $this->belongsTo(User::class,'leader_id');
    }



}
