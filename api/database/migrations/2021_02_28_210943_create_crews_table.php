<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration

{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
     Schema::create('crews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->uuid('organizer_id')->index();
            $table->timestamps();

            $table->foreign('organizer_id')
                ->references('id')
                ->on('users');
        });

        Schema::create('crew_user', function (Blueprint $table) {
            $table->uuid('crew_id')->primary();
            $table->uuid('user_id')->index();
            $table->timestamps();

            $table->foreign('crew_id')
                ->references('id')
                ->on('crews')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('crew_users');
        Schema::dropIfExists('crews');
    }
};
