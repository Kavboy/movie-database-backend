<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTriggerBeforeUserDeletion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            -- Remove all relationship data before deletion of actual user
            CREATE OR ALTER TRIGGER trigger_before_user_delete
                ON [dbo].[users]
                FOR DELETE
                AS
                BEGIN
                    DECLARE @id BIGINT;

                    SELECT @id = id FROM deleted;

                    DELETE FROM user_media WHERE user_id = @id;

                    DELETE FROM sessions WHERE user_id = @id;

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
        DB::unprepared('DROP TRIGGER `trigger_before_user_delete`');
    }
}
