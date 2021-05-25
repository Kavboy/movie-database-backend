<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTriggerBeforeMediaDeletion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::unprepared('
            -- Remove all relationship data before deletion of actual media
            CREATE OR ALTER TRIGGER trigger_before_media_delete
                ON [dbo].[medias]
                FOR DELETE
                AS
                BEGIN
                    DECLARE @id BIGINT;

                    SELECT @id = id FROM deleted;

                    DELETE FROM media_medium WHERE media_id = @id;

                    DELETE FROM media_genre WHERE media_id = @id;

                    DELETE FROM user_media WHERE media_id = @id;

                END;
       ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `trigger_before_media_delete`');
    }
}
