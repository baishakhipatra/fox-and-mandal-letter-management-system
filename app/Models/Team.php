<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "teams";

    protected $fillable = [
        'name',
        'status'
    ];

    public function members(){
        return $this->belongsToMany(User::class, 'team_members');
    }

}
