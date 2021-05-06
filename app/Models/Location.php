<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'location',
    ];

    /**
     * Relationship between video and location
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function video(){
        return $this->hasMany(Video::class);
    }
}
