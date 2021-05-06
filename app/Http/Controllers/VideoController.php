<?php

namespace App\Http\Controllers;

use App\Models\AgeRating;
use App\Models\Genre;
use App\Models\Location;
use App\Models\Medium;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class VideoController extends Controller {
    /**
     * Display a listing of the resource.
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
                $videos = Video::orderBy( 'title' )->paginate( $perPage );

                return response()->json( $videos, 200 );
            } elseif ( $medium ) {
                $videos = Video::whereHas( 'medium', function ( $query ) use ( $medium ) {
                    $query->where( 'medium', 'LIKE', "%{$medium}%" );
                } )->orderBy( 'title' )->paginate( $perPage );

                return response()->json( $videos, 200 );
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
     * Display a listing of the newest 5 videos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function news() {
        try {
            if ( Video::orderByDesc( 'created_at' )->take( 5 )->get() ) {
                $videos = Video::orderByDesc( 'created_at' )->take( 5 )->get();

                return response()->json( $videos, 200 );
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
     * Display a listing of the searched videos.
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

            $title   = null;
            $mediums = null;
            $genres  = null;
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


            $videoQuery = Video::query();

            $videoQuery->when( Arr::exists( $validated, 'title' ), function ( $query ) use ( $title ) {
                return $query->where( 'title', 'LIKE', "%{$title}%" );
            } );

            $videoQuery->when( Arr::exists( $validated, 'mediums' ), function ( $query ) use ( $mediums ) {
                return $query->whereHas( 'medium', function ( $query ) use ( $mediums ) {
                    $query->whereIn( 'medium', $mediums );
                } );
            } );

            $videoQuery->when( Arr::exists( $validated, 'genres' ), function ( $query ) use ( $genres ) {
                return $query->whereHas( 'genres', function ( $query ) use ( $genres ) {
                    $query->whereIn( 'genres', $genres );
                } );
            } );


            $videoQuery->when( Arr::exists( $validated, 'age_ratings' ), function ( $query ) use ( $ageRatings ) {
                return $query->whereHas( 'ageRatings', function ( $query ) use ( $ageRatings ) {
                    $query->whereIn( 'fsk', $ageRatings );
                } );
            } );

            $videos = $videoQuery->get();

            return response()->json( $videos, 200 );
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function titles( Request $request ) {
        $validated = $request->validate( [
            'title' => [ 'required', 'max:255', 'string', 'filled' ],
        ] );

        try {
            if ( Video::where( 'title', 'LIKE', "%{$validated['title']}%" ) ) {
                $titles = Video::where( 'title', 'LIKE', "%{$validated['title']}%" )->take( 10 )->orderBy( 'title' )->pluck( 'title' );

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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request ) {
        $validated = $request->validate( [
            'type'         => [ 'required', Rule::in( [ 'Movie', 'TV' ] ) ],
            'title'        => [ 'required', 'max:255', 'string', 'filled' ],
            'release_date' => [ 'required', 'filled', 'date_format:Y-m-d' ],
            'overview'     => [ 'string', 'nullable' ],
            'poster_path'  => [ 'required', 'string', 'filled', 'max:255' ],
            'tmdb_id'      => [ 'numeric', 'integer' ],
            'youtube_link' => [ 'string', 'nullable' ],
            'cast'         => [ 'array' ],
            'age_rating'   => [ 'required', 'exists:age_ratings,fsk' ],
            'location'     => [ 'max:255' ],
            'mediums'      => [ 'required', 'array' ],
            'mediums.*'    => [ 'required', 'exists:mediums,medium' ],
            'genres'       => [ 'required', 'array' ],
            'genres.*'     => [ 'required', 'exists:genres,name' ]
        ] );

        try {
            $video               = new Video;
            $video->type         = $validated['type'];
            $video->title        = $validated['title'];
            $video->release_date = $validated['release_date'];

            if ( Arr::exists( $validated, 'overview' ) ) {
                $video->overview = $validated['overview'];
            }

            $video->poster_path = $validated['poster_path'];

            if ( Arr::exists( $validated, 'tmdb_id' ) ) {
                $video->tmdb_id = $validated['tmdb_id'];
            }

            if ( Arr::exists( $validated, 'youtube_link' ) ) {
                $video->youtube_link = $validated['youtube_link'];
            }

            if ( Arr::exists( $validated, 'cast' ) ) {
                $video->cast = json_encode( $validated['cast'] );
            }

            $ageRating = AgeRating::where( 'fsk', $validated['age_rating'] )->first();

            $video->ageRating()->associate( $ageRating );

            if ( Arr::exists( $validated, 'location' ) ) {
                $location = Location::find( $validated['location'] );

                if ( $location ) {
                    $video->location()->associate( $location );
                }
            }

            $video->save();

            foreach ( $validated['mediums'] as $medium ) {
                $medium_element = Medium::where( 'medium', $medium )->first();
                $video->medium()->attach( $medium_element );
            }

            foreach ( $validated['genres'] as $genre ) {
                $genre_element = Genre::where( 'name', $genre )->first();
                $video->genre()->attach( $genre_element );
            }


            return response( $video, 201 );
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
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show( $id ) {
        try {
            if ( Video::find( $id ) ) {
                $video = Video::find( $id );

                return response()->json( $video, 200 );
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
     * Update the specified resource in storage.
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
            'poster_path'  => [ 'string', 'filled', 'max:255' ],
            'tmdb_id'      => [ 'numeric', 'integer' ],
            'youtube_link' => [ 'string', 'nullable' ],
            'cast'         => [ 'json' ],
            'age_rating'   => [ 'exists:age_ratings,fsk' ],
            'location'     => [ 'max:255' ],
            'mediums'      => [ 'array' ],
            'mediums.*'    => [ 'exists:mediums,medium' ],
            'genres'       => [ 'array' ],
            'genres.*'     => [ 'exists:genres,name' ]
        ] );

        try {
            $video = Video::find( $id );

            if ( Arr::exists( $validated, 'type' ) ) {
                $video->type = $validated['type'];
            }

            if ( Arr::exists( $validated, 'title' ) ) {
                $video->type = $validated['title'];
            }

            if ( Arr::exists( $validated, 'release_date' ) ) {
                $video->release_date = $validated['release_date'];
            }

            if ( Arr::exists( $validated, 'overview' ) ) {
                $video->overview = $validated['overview'];
            }

            if ( Arr::exists( $validated, 'poster_path' ) ) {
                $video->poster_path = $validated['poster_path'];
            }

            if ( Arr::exists( $validated, 'tmdb_id' ) ) {
                $video->tmdb_id = $validated['tmdb_id'];
            }

            if ( Arr::exists( $validated, 'youtube_link' ) ) {
                $video->youtube_link = $validated['youtube_link'];
            }

            if ( Arr::exists( $validated, 'cast' ) ) {
                $video->cast = json_encode( $validated['cast'] );
            }

            if ( Arr::exists( $validated, 'age_rating' ) ) {

                $video->ageRating()->dissociate();

                $ageRating = AgeRating::where( 'fsk', $validated['age_rating'] )->first();

                $video->ageRating()->associate( $ageRating );
            }

            if ( Arr::exists( $validated, 'location' ) ) {

                $video->location()->dissociate();

                $location = Location::find( $validated['location'] );

                if ( $location ) {
                    $video->location()->associate( $location );
                }
            }

            $video->save();

            if ( Arr::exists( $validated, 'mediums' ) ) {
                $video->mediums()->detach();

                foreach ( $validated['mediums'] as $medium ) {
                    $medium_element = Medium::where( 'medium', $medium )->first();
                    $video->medium()->attach( $medium_element );
                }
            }

            if ( Arr::exists( $validated, 'genres' ) ) {
                $video->genre()->detach();

                foreach ( $validated['genres'] as $genre ) {
                    $genre_element = Genre::where( 'name', $genre )->first();
                    $video->genre()->attach( $genre_element );
                }
            }

            return response( $video, 201 );
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( $id ) {
        try {
            if ( Video::find( $id ) ) {
                $video = Video::find( $id );

                $video->delete();

                return response()->json( $video, 200 );
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
