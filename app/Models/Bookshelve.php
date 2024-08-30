<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookshelve extends Model
{
    use HasFactory;

    public function books()
    {
        return $this->hasMany(Book::class, 'bookshelves_id');
    }
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
    
    
     public function user()
    {
        return $this->belongsTo(User::class);
    }
}
