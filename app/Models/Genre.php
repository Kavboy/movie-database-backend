<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that are hidden in the response.
     *
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * Relationship between media and genre
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function media(){
        return $this->belongsToMany(Media::class, 'media_genre');
    }
}
