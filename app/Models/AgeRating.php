<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeRating extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fsk',
    ];

    /**
     * Relationship between media and age rating
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function media(){
        return $this->hasMany(Media::class);
    }

}
