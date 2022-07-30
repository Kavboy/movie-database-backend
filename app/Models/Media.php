<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model {
    use HasFactory;

    public $table = 'medias';

    protected $appends = [ 'genres', 'mediums', 'keywords' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'medium',
        'genre'
    ];

    public function getAgeRatingAttribute( $value ): string {
        $fsk = AgeRating::find( $value );
        return $fsk ? $fsk['fsk'] : '';
    }

    public function getGenresAttribute(): \Illuminate\Support\Collection {
        return $this->genre()->pluck( 'name' );
    }

    public function getMediumsAttribute(): \Illuminate\Support\Collection {
        return $this->medium()->pluck( 'medium' );
    }

    public function getLocationAttribute($value): string {
        $location = Location::find($value);
        return $location ? $location['location'] : '';
    }

    public function getCastAttribute($value) {
        return json_decode($value);
    }

    public function getSeasonsAttribute($value) {
        return json_decode($value);
    }

    public function getKeywordsAttribute(): \Illuminate\Support\Collection {
        return $this->keyword()->pluck( 'keyword' );
    }

    /**
     * Relationship between user and role
     *
     * @return BelongsToMany
     */
    public function user(): BelongsToMany {
        return $this->belongsToMany( User::class, 'user_media' )->withTimestamps();
    }

    /**
     * Relationship between media and medium
     *
     * @return BelongsToMany
     */
    public function medium(): BelongsToMany {
        return $this->belongsToMany( Medium::class, 'media_medium' );
    }

    /**
     * Relationship between media and genre
     *
     * @return BelongsToMany
     */
    public function genre(): BelongsToMany {
        return $this->belongsToMany( Genre::class, 'media_genre' );
    }

    /**
     * Relationship between media and age rating
     *
     * @return BelongsTo
     */
    public function ageRatings(): BelongsTo {
        return $this->belongsTo( AgeRating::class, 'age_rating' );
    }

    /**
     * Relationship between media and location
     *
     * @return BelongsTo
     */
    public function location(): BelongsTo {
        return $this->belongsTo( Location::class, 'location' );
    }

    /**
     * @return BelongsToMany
     */
    public function keyword(): BelongsToMany {
        return $this->belongsToMany(Keyword::class, 'media_keyword');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
/*    protected static function booted() {
        static::deleting( function ( $media ) {
            $media->user()->detach();
            $media->genre()->detach();
            $media->medium()->detach();
        } );
    }*/
}
