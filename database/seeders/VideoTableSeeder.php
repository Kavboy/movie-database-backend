<?php

namespace Database\Seeders;

use App\Models\AgeRating;
use App\Models\Genre;
use App\Models\Location;
use App\Models\Medium;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class VideoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('video_medium')->delete();
        DB::table('video_genre')->delete();
        DB::table('user_video')->delete();
        DB::table('videos')->delete();

        $video = new Video;

        $video->type         = "Movie";
        $video->title        = "Doctor Strange";
        $video->release_date = "2016-10-27";

        $video->overview = "Doctor Stephen Strange ist ein arroganter, aber auch unglaublich talentierter Neurochirurg. Nach einem schweren Autounfall kann er seiner Tätigkeit trotz mehrerer Operationen und Therapien nicht mehr nachgehen. In seiner Verzweifelung wendet er sich schließlich von der Schulmedizin ab und reist nach Tibet, wo er bei der Einsiedlerin The Ancient One und ihrer Glaubensgemeinschaft lernt, sein verletztes Ego hinten anzustellen und in die Geheimnisse einer verborgenen mystischen Welt voller alternativer Dimensionen eingeführt wird. So entwickelt sich Doctor Strange nach und nach zu einem der mächtigsten Magier der Welt. Doch schon bald muss er seine neugewonnenen mystischen Kräfte nutzen, um die Welt vor einer Bedrohung aus einer anderen Dimension zu beschützen.";

        $video->poster_path = "/mzPnP190YLV4KFL3ZgsP7AsPEd2.jpg";
        $video->tmdb_id = 284052;

        $video->youtube_link = "https://www.youtube.com/watch?v=csVu15mEfQo";


        $ageRating = AgeRating::where( 'fsk', 12 )->first();

        $video->ageRating()->associate( $ageRating );

        $video->save();

        foreach ( ["Bluray", "DVD"] as $medium ) {
            $medium_element = Medium::where( 'medium', $medium )->first();
            $video->medium()->attach( $medium_element );
        }

        foreach ( ["Action", "Adventure", "Fantasy", "Science Fiction"] as $genre ) {
            $genre_element = Genre::where( 'name', $genre )->first();
            $video->genre()->attach( $genre_element );
        }


        $video = new Video;

        $video->type         = "Movie";
        $video->title        = "Sherlock Holmes";
        $video->release_date = "2010-01-28";

        $video->overview = "Sherlock Holmes und sein Partner Dr. Watson haben es geschafft, einen der kaltblütigsten Mörder des 19. Jahrhunderts festzunehmen. Lord Blackwood hat zahlreiche Menschen auf dem Gewissen und muss dafür nun selber am Galgen baumeln. Dies scheint ihm jedoch wenig auszumachen, denn er beherrscht die Kunst der schwarzen Magie und verspricht Holmes, dass er auch nach seinem Tod noch weitere Menschen ermorden wird. Holmes hält nicht viel von dieser Drohung, doch als plötzlich die Morde wieder losgehen, beschließt er, die Leiche Lord Blackwoods zu exhumieren. Dabei stellt sich erschreckenderweise heraus, dass jemand anderes in dessen Sarg liegt. Nun sind also wieder Holmes und Watson gefragt und müssen dem mysteriösen Treiben ein Ende setzen...";

        $video->poster_path = "/oUp5KjqMOUdN1KstNyYMlVMEmhb.jpg";
        $video->tmdb_id = 10528;

        $video->youtube_link = "https://www.youtube.com/watch?v=8beqXEMoPfc";


        $ageRating = AgeRating::where( 'fsk', 12 )->first();

        $video->ageRating()->associate( $ageRating );

        $video->save();

        foreach ( ["Bluray"] as $medium ) {
            $medium_element = Medium::where( 'medium', $medium )->first();
            $video->medium()->attach( $medium_element );
        }

        foreach ( ["Action", "Adventure", "Crime", "Mystery"] as $genre ) {
            $genre_element = Genre::where( 'name', $genre )->first();
            $video->genre()->attach( $genre_element );
        }

        $video = new Video;

        $video->type         = "Movie";
        $video->title        = "Men in Black";
        $video->release_date = "1997-09-11";

        $video->overview = "Nur die Men In Black kennen das bestgehütete Geheimnis der Welt: auf der Erde wimmelt es von Aliens! Außerirdische der unterschiedlichsten Art haben sich, als Menschen getarnt, auf dem Planeten breitgemacht. Einige werden geduldet, andere aufgespürt, gejagt, vertrieben. Und diesen Job erledigen die beiden zähesten unter den Alienjägern, die Agenten Mr. K und Mr. J. Doch dann landet ein intergalaktisches Riesenmonster auf der Erde - sein Ziel: die totale Vernichtung der Welt. Trotz optimaler High-Tech-Bewaffnung haben die beiden Super-Agenten jetzt einen lebensgefährlichen Auftrag. Als sie die Fährte des außerirdischen Eindringlings aufgenommen haben, scheint es fast zu spät zu sein - eine globale Katastrophe bahnt sich an...";

        $video->poster_path = "/838NNSVYSR3JflsI3dKpTkJGmPC.jpg";
        $video->tmdb_id = 607;

        $video->youtube_link = "https://www.youtube.com/watch?v=6rcvahLFLVU";


        $ageRating = AgeRating::where( 'fsk', 12 )->first();

        $video->ageRating()->associate( $ageRating );

        $video->save();

        foreach ( ["Bluray"] as $medium ) {
            $medium_element = Medium::where( 'medium', $medium )->first();
            $video->medium()->attach( $medium_element );
        }

        foreach ( ["Action", "Adventure", "Comedy", "Science Fiction"] as $genre ) {
            $genre_element = Genre::where( 'name', $genre )->first();
            $video->genre()->attach( $genre_element );
        }


        // show information in the command line after everything is run
        $this->command->info('Video seeds finished.');
    }
}
