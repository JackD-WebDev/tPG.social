<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        DB::unprepared('drop function if exists f_new_uuid;');
        DB::unprepared("
            CREATE DEFINER=`root`@`%` FUNCTION `f_new_uuid`() RETURNS char(36)
                NOT DETERMINISTIC
            BEGIN
                DECLARE cNewUUID char(36);
                DECLARE cMd5Val char(32);

                set cMd5Val = md5(concat(rand(),now(6)));
                set cNewUUID = concat(left(md5(concat(year(now()),week(now()))),4),left(cMd5Val,4),'-',
                mid(cMd5Val,5,4),'-4',mid(cMd5Val,9,3),'-',mid(cMd5Val,13,4),'-',mid(cMd5Val,17,12));

                RETURN cNewUUID;
            END;;
        ");
    }

    /**
     * @return void
     */
    public function down(): void
    {
        DB::unprepared('drop function if exists f_new_uuid;');
    }
};
