<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medium extends Model
{
    use HasFactory;

    public $table = 'mediums';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'medium',
    ];

    /**
     * The attributes that are hidden in the response.
     *
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * Relationship between media and medium
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    /*public function media(){
        return $this->belongsToMany(Video::class, 'media_medium');
    }*/
}
