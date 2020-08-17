<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * This Migration is for creating News
 * Class CreateNewsTable
 */
class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('news_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('header');
            $table->string('description');
            $table->string('status');
            $table->unsignedInteger('created_by');
            $table->tinyInteger('media_type')->nullable();
            $table->string('media_url')->nullable();
            $table->string('media_thumbnail')->nullable();
            $table->tinyInteger('is_send_with_newsletter')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
