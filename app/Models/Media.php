<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model {
    use HasFactory;

    protected $appends = [ 'genres', 'mediums' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'medium',
        'genre'
    ];

    public function getAgeRatingAttribute( $value ) {
        return AgeRating::find( $value ) ? AgeRating::find( $value )['fsk'] : '';
    }

    public function getGenresAttribute() {
        return $this->genre()->pluck( 'name' );
    }

    public function getMediumsAttribute() {
        return $this->medium()->pluck( 'medium' );
    }

    public function getCastAttribute($value) {
        return json_decode($value);
    }

    /**
     * Relationship between user and role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user() {
        return $this->belongsToMany( User::class, 'user_media' );
    }

    /**
     * Relationship between media and medium
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function medium() {
        return $this->belongsToMany( Medium::class, 'media_medium' );
    }

    /**
     * Relationship between media and genre
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function genre() {
        return $this->belongsToMany( Genre::class, 'media_genre' );
    }

    /**
     * Relationship between media and age rating
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ageRatings() {
        return $this->belongsTo( AgeRating::class, 'age_rating' );
    }

    /**
     * Relationship between media and location
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location() {
        return $this->belongsTo( Location::class, 'location' );
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted() {
        static::deleting( function ( $media ) {
            $media->user()->detach();
            $media->genre()->detach();
            $media->medium()->detach();
        } );
    }
}
