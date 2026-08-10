<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteMovie extends Model
{
    protected $fillable = ['user_id', 'imdb_id', 'title', 'year', 'poster', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
