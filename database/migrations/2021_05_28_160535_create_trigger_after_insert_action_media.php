<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggerAfterInsertActionMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            -- count inserts per month
           CREATE OR REPLACE TRIGGER trigger_after_insert_action_media
               AFTER INSERT
               ON medias FOR EACH ROW
               BEGIN
                   DECLARE month_var varchar(10);
                   DECLARE year_var INT;
                   DECLARE action_var varchar(7);
                   SET month_var = MONTHNAME(CURDATE());
                   SET year_var = YEAR(CURDATE());
                   SET action_var = 'Insert';
                   IF EXISTS (SELECT * FROM statistics WHERE action = action_var AND month LIKE month_var AND year = year_var) THEN
                           UPDATE statistics
                               SET
                                   count = (SELECT count FROM statistics WHERE action = action_var AND month LIKE month_var AND year = year_var) + 1
                               WHERE action = action_var AND month LIKE month_var AND year = year_var;
                   ELSE
                       INSERT INTO statistics (month, year, action, count) VALUES ( month_var, year_var, action_var, 1);
                   END IF;
               END
            ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `trigger_after_insert_action_media`');
    }
}
