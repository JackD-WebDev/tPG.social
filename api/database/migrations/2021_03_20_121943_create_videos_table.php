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
        Schema::create('videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('channel_id')->index();
            $table->integer('percentage')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('location')->nullable();
            $table->string('path')->nullable();
            $table->bigInteger('views')->default(0);
            $table->boolean('is_live')->default(false);
            $table->boolean('closed_to_comments')->default(false);
            $table->boolean('upload_successful')->default(false);
            $table->string('disk')->default('public');
            $table->timestamps();

            $table->foreign('channel_id')
                ->references('id')
                ->on('channels')
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
        Schema::dropIfExists('videos');
    }
};
