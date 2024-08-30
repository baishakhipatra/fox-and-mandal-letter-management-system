<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','bookshelves_id', 'office_id', 'uid', 'qrcode', 'category_id',
        'title', 'author', 'publisher', 'edition', 'page', 'quantity',
    ];

    // public function users()
    // {
    //     return $this->belongsToMany(User::class);
    // }
    // public function bookshelve()
    // {
    //     return $this->belongsTo(Bookshelve::class);
    // }

    public function bookshelve()
    {
        return $this->belongsTo(Bookshelve::class, 'bookshelves_id');
    }
}
