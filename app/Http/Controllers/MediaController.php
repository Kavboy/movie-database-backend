<?php

namespace App\Http\Controllers;

use App\Models\AgeRating;
use App\Models\Genre;
use App\Models\Location;
use App\Models\Medium;
use App\Models\Media;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MediaController extends Controller {
    /**
     * Display a listing of medias, with hidden elements.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index( Request $request ) {

        $perPage = env( 'PER_PAGE', 5 );
        $medium  = null;

        if ( $request->get( 'per_page' ) ) {
            $perPage = $request->get( 'per_page' );
        }

        if ( $request->get( 'medium' ) ) {
            $medium = $request->get( 'medium' );
        }

        try {
            if ( ! $medium ) {
                $medias = Media::orderBy( 'title' )->paginate( $perPage );
                $medias->setCollection( $medias->getCollection()->makehidden( [
                    'cast',
                    'youtube_link',
                    'location',
                    'release_date',
                    'genres',
                    'mediums'
                ] ) );

                return response()->json( $medias, 200 );
            } elseif ( $medium ) {
                $medias = Media::whereHas( 'medium', function ( $query ) use ( $medium ) {
                    $query->where( 'medium', 'LIKE', "%{$medium}%" );
                } )->orderBy( 'title' )->paginate( $perPage );

                $medias->setCollection( $medias->getCollection()->makehidden( [
                    'cast',
                    'youtube_link',
                    'location',
                    'release_date',
                    'genres',
                    'mediums'
                ] ) );

                return response()->json( $medias, 200 );
            } else {
                return response()->json( [], 404 );
            }
        } catch ( QueryException $ex ) {

            if ( env( 'APP_DEBUG' ) ) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error( $ex );
                $res['message'] = 'Something unexpected happened, please try again later';
            }

            return response()->json( $res, 500 );
        }
    }

    /**
     * Display a listing of medias with all data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function table( Request $request ) {
        try {
            $medias = Media::orderBy( 'id' )->paginate( 10 );
            if ( $medias ) {
                return response()->json( $medias, 200 );
            } else {
                return response()->json( [], 404 );
            }
        } catch ( QueryException $ex ) {

            if ( env( 'APP_DEBUG' ) ) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error( $ex );
                $res['message'] = 'Something unexpected happened, please try again later';
            }

            return response()->json( $res, 500 );
        }
    }


    /**
     * Display a listing of the newest 5 medias.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function news() {
        try {
            if ( Media::orderByDesc( 'created_at' )->take( 5 )->get() ) {
                $medias = Media::orderByDesc( 'created_at' )->take( 5 )->get();

                $medias->makehidden( [ 'cast', 'youtube_link', 'location', 'release_date', 'genres', 'mediums' ] );

                return response()->json( $medias, 200 );
            } else {
                return response()->json( [], 404 );
            }
        } catch ( QueryException $ex ) {

            if ( env( 'APP_DEBUG' ) ) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error( $ex );
                $res['message'] = 'Something unexpected happened, please try again later';
            }

            return response()->json( $res, 500 );
        }
    }

    /**
     * Display a listing of the searched medias.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search( Request $request ) {
        $validated = $request->validate( [
            'title'         => [ 'max:255', 'string', 'nullable' ],
            'mediums'       => [ 'array' ],
            'mediums.*'     => [ 'string', 'exists:mediums,medium' ],
            'genres'        => [ 'array' ],
            'genres.*'      => [ 'string', 'exists:genres,name' ],
            'age_ratings'   => [ 'array' ],
            'age_ratings.*' => [ 'exists:age_ratings,fsk' ]
        ] );

        try {

            $title      = null;
            $mediums    = null;
            $genres     = null;
            $ageRatings = null;

            if ( Arr::exists( $validated, 'title' ) ) {
                $title = $validated['title'];
            }

            if ( Arr::exists( $validated, 'mediums' ) ) {
                $mediums = $validated['mediums'];
            }

            if ( Arr::exists( $validated, 'genres' ) ) {
                $genres = $validated['genres'];
            }

            if ( Arr::exists( $validated, 'age_ratings' ) ) {
                $ageRatings = $validated['age_ratings'];
            }


            $mediaQuery = Media::query();

            $mediaQuery->when( Arr::exists( $validated, 'title' ), function ( $query ) use ( $title ) {
                return $query->where( 'title', 'LIKE', "%{$title}%" );
            } );

            $mediaQuery->when( Arr::exists( $validated, 'mediums' ), function ( $query ) use ( $mediums ) {
                return $query->whereHas( 'medium', function ( $query ) use ( $mediums ) {
                    $query->whereIn( 'medium', $mediums );
                } );
            } );

            $mediaQuery->when( Arr::exists( $validated, 'genres' ), function ( $query ) use ( $genres ) {
                return $query->whereHas( 'genres', function ( $query ) use ( $genres ) {
                    $query->whereIn( 'genres', $genres );
                } );
            } );


            $mediaQuery->when( Arr::exists( $validated, 'age_ratings' ), function ( $query ) use ( $ageRatings ) {
                return $query->whereHas( 'ageRatings', function ( $query ) use ( $ageRatings ) {
                    $query->whereIn( 'fsk', $ageRatings );
                } );
            } );

            $medias = $mediaQuery->get();

            return response()->json( $medias, 200 );
        } catch ( QueryException $ex ) {

            if ( env( 'APP_DEBUG' ) ) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error( $ex );
                $res['message'] = 'Something unexpected happened, please try again later';
            }

            return response()->json( $res, 500 );
        }
    }

    /**
     * Display a listing of searched media titles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function titles( Request $request ) {
        $validated = $request->validate( [
            'title' => [ 'required', 'max:255', 'string', 'filled' ],
        ] );

        try {
            if ( Media::where( 'title', 'LIKE', "%{$validated['title']}%" ) ) {
                $titles = Media::where( 'title', 'LIKE', "%{$validated['title']}%" )->take( 10 )->orderBy( 'title' )->pluck( 'title' );

                return response()->json( $titles, 200 );
            } else {
                return response()->json( [], 404 );
            }
        } catch ( QueryException $ex ) {

            if ( env( 'APP_DEBUG' ) ) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error( $ex );
                $res['message'] = 'Something unexpected happened, please try again later';
            }

            return response()->json( $res, 500 );
        }
    }

    /**
     * Store a newly created media in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request ) {

        $validated = $request->validate( [
            'type'         => [ 'required', Rule::in( [ 'Movie', 'TV' ] ) ],
            'title'        => [ 'required', 'max:255', 'string', 'filled', 'unique:medias,title' ],
            'release_date' => [ 'required', 'filled', 'date_format:Y-m-d' ],
            'overview'     => [ 'string', 'nullable' ],
            'poster_file'  => [ 'file', 'mimes:jpg,png,webp' ],
            'poster_path'  => [ 'string', 'filled', 'max:255' ],
            'tmdb_id'      => [ 'numeric', 'integer' ],
            'youtube_link' => [ 'nullable', 'string', 'regex:/^([a-zA-Z\d\-_&=])+$/' ],
            'cast'         => [ 'array' ],
            'age_rating'   => [ 'required', 'exists:age_ratings,fsk' ],
            'location'     => [ 'max:255' ],
            'mediums'      => [ 'required', 'array' ],
            'mediums.*'    => [ 'required', 'exists:mediums,medium' ],
            'genres'       => [ 'required', 'array' ],
            'genres.*'     => [ 'required', 'exists:genres,name' ],
            'seasons'      => [ 'array' ],
            'seasons.*'    => [ 'string' ]
        ] );

        try {
            $media               = new Media;
            $media->type         = $validated['type'];
            $media->title        = Str::of( $validated['title'] )->trim();
            $media->release_date = $validated['release_date'];

            if ( Arr::exists( $validated, 'overview' ) ) {
                $media->overview = Str::of( $validated['overview'] )->trim();
            }

            if ( Arr::exists( $validated, 'poster_path' ) ) {
                $media->poster_path = $validated['poster_path'];
            }

            if ( Arr::exists( $validated, 'tmdb_id' ) ) {
                $media->tmdb_id = $validated['tmdb_id'];
            }

            if ( Arr::exists( $validated, 'poster_file' ) ) {
                $fileName = Str::of( $validated['title'] )->trim()->snake()->replace( ' ', '_' );
                $fileName .= "." . $request->file( 'poster_file' )->extension();
                $request->file( 'poster_file' )->storeAS( 'public', $fileName );
                $url                = Storage::url( $fileName );
                $path               = asset( $url );
                $media->poster_path = $path;
            }

            if ( Arr::exists( $validated, 'youtube_link' ) ) {
                $media->youtube_link = $validated['youtube_link'];
            }

            if ( Arr::exists( $validated, 'cast' ) ) {
                if ( $request->hasHeader( 'Content-Type' ) && str_contains( $request->header( 'Content-Type' ), 'multipart/form-data' ) ) {
                    $arr = [];
                    foreach ( $validated['cast'] as $cast ) {
                        array_push( $arr, json_decode( $cast ) );
                    }

                    $media->cast = json_encode( $arr );
                } else {
                    $media->cast = json_encode( $validated['cast'] );
                }
            }

            if ( Arr::exists( $validated, 'seasons' ) ) {
                $media->seasons = json_encode($validated['seasons']);
            }

            $ageRating = AgeRating::where( 'fsk', $validated['age_rating'] )->first();

            $media->ageRatings()->associate( $ageRating );

            if ( Arr::exists( $validated, 'location' ) ) {
                $location = Location::where( 'location', $validated['location'] )->first();

                if ( $location ) {
                    $media->location()->associate( $location );
                }
            }

            $media->save();

            foreach ( $validated['mediums'] as $medium ) {
                $medium_element = Medium::where( 'medium', $medium )->first();
                $media->medium()->attach( $medium_element );
            }

            foreach ( $validated['genres'] as $genre ) {
                $genre_element = Genre::where( 'name', $genre )->first();
                $media->genre()->attach( $genre_element );
            }


            return response( $media, 201 );
        } catch ( QueryException $ex ) {
            if ( env( 'APP_DEBUG' ) ) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error( $ex );
                $res['message'] = 'Something unexpected happened, please try again later';
            }

            return response( $res, 500 );
        }
    }

    /**
     * Display the specified media.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show( $id ) {
        try {
            if ( Media::find( $id ) ) {
                $media = Media::find( $id );

                return response()->json( $media, 200 );
            } else {
                return response()->json( [], 404 );
            }
        } catch ( QueryException $ex ) {

            if ( env( 'APP_DEBUG' ) ) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error( $ex );
                $res['message'] = 'Something unexpected happened, please try again later';
            }

            return response()->json( $res, 500 );
        }
    }

    /**
     * Update the specified media in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, int $id ) {
        $validated = $request->validate( [
            'type'         => [ Rule::in( [ 'Movie', 'TV' ] ) ],
            'title'        => [ 'max:255', 'string', 'filled' ],
            'release_date' => [ 'filled', 'date_format:Y-m-d' ],
            'overview'     => [ 'string', 'nullable' ],
            'poster_file'  => [ 'file', 'mimes:jpg,png,webp' ],
            'poster_path'  => [ 'string', 'filled', 'max:255' ],
            'tmdb_id'      => [ 'numeric', 'integer' ],
            'youtube_link' => [ 'nullable', 'string', 'regex:/^([a-zA-Z\d\-_&=])+$/' ],
            'cast'         => [ 'array' ],
            'age_rating'   => [ 'exists:age_ratings,fsk' ],
            'location'     => [ 'max:255' ],
            'mediums'      => [ 'array' ],
            'mediums.*'    => [ 'exists:mediums,medium' ],
            'genres'       => [ 'array' ],
            'genres.*'     => [ 'exists:genres,name' ],
            'seasons'      => [ 'array' ],
            'seasons.*'    => [ 'string' ]
        ] );

        try {
            $media = Media::find( $id );

            if ( Arr::exists( $validated, 'type' ) ) {
                $media->type = $validated['type'];
            }

            if ( Arr::exists( $validated, 'title' ) ) {
                $media->title = $validated['title'];
            }

            if ( Arr::exists( $validated, 'release_date' ) ) {
                $media->release_date = $validated['release_date'];
            }

            if ( Arr::exists( $validated, 'overview' ) ) {
                $media->overview = $validated['overview'];
            }

            if ( Arr::exists( $validated, 'poster_path' ) ) {
                $media->poster_path = $validated['poster_path'];
            }

            if ( Arr::exists( $validated, 'poster_file' ) ) {
                $fileName = Str::of( $validated['title'] )->trim()->snake()->replace( ' ', '_' );
                $fileName .= "." . $request->file( 'poster_file' )->extension();

                if ( Storage::exists( $fileName ) ) {
                    Storage::delete( $fileName );
                }

                $request->file( 'poster_file' )->storeAS( 'public', $fileName );
                $url                = Storage::url( $fileName );
                $path               = asset( $url );
                $media->poster_path = $path;
            }

            if ( Arr::exists( $validated, 'tmdb_id' ) ) {
                $media->tmdb_id = $validated['tmdb_id'];
            }

            if ( Arr::exists( $validated, 'youtube_link' ) ) {
                $media->youtube_link = $validated['youtube_link'];
            }

            if ( Arr::exists( $validated, 'cast' ) ) {
                if ( $request->hasHeader( 'Content-Type' ) && str_contains( $request->header( 'Content-Type' ), 'multipart/form-data' ) ) {
                    $arr = [];
                    foreach ( $validated['cast'] as $cast ) {
                        array_push( $arr, json_decode( $cast ) );
                    }

                    $media->cast = json_encode( $arr );
                } else {
                    $media->cast = json_encode( $validated['cast'] );
                }
            }

            if ( Arr::exists( $validated, 'seasons' ) ) {
                $media->seasons = json_encode($validated['seasons']);
            }

            if ( Arr::exists( $validated, 'age_rating' ) ) {

                $media->ageRatings()->dissociate();

                $ageRating = AgeRating::where( 'fsk', $validated['age_rating'] )->first();

                $media->ageRatings()->associate( $ageRating );
            }

            if ( Arr::exists( $validated, 'location' ) ) {

                $media->location()->dissociate();

                $location = Location::where( 'location', $validated['location'] )->first();

                if ( $location ) {
                    $media->location()->associate( $location );
                }
            }

            $media->save();

            if ( Arr::exists( $validated, 'mediums' ) ) {
                $media->medium()->detach();

                foreach ( $validated['mediums'] as $medium ) {
                    $medium_element = Medium::where( 'medium', $medium )->first();
                    $media->medium()->attach( $medium_element );
                }
            }

            if ( Arr::exists( $validated, 'genres' ) ) {
                $media->genre()->detach();

                foreach ( $validated['genres'] as $genre ) {
                    $genre_element = Genre::where( 'name', $genre )->first();
                    $media->genre()->attach( $genre_element );
                }
            }

            return response( $media, 201 );
        } catch ( QueryException $ex ) {
            if ( env( 'APP_DEBUG' ) ) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error( $ex );
                $res['message'] = 'Something unexpected happened, please try again later';
            }

            return response( $res, 500 );
        }
    }

    /**
     * Remove the specified media from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( $id ) {
        try {
            if ( Media::find( $id ) ) {
                $media = Media::find( $id );

                $fileName = Str::of( $media->title )->trim()->snake()->replace( ' ', '_' );
                $fileName .= ".jpg";

                $fileNames  = [];
                $extensions = [ '.jpg', '.png', '.webp' ];

                foreach ( $extensions as $ext ) {
                    array_push( $fileNames, $fileName . $ext );
                }

                foreach ( $fileNames as $fileName ) {
                    if ( Storage::exists( $fileName ) ) {
                        Storage::delete( $fileName );
                    }
                }


                $media->delete();

                return response()->json( $media, 200 );
            } else {
                return response()->json( [], 404 );
            }
        } catch ( QueryException $ex ) {

            if ( env( 'APP_DEBUG' ) ) {
                $res['message'] = $ex->getMessage();
            } else {
                Log::error( $ex );
                $res['message'] = 'Something unexpected happened, please try again later';
            }

            return response()->json( $res, 500 );
        }
    }
}
