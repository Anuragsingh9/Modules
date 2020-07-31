<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsReviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_review', function (Blueprint $table) {
            $table->increments('id');
            $table->string('review_text')->nullable();
            $table->string('review_reaction');
            $table->tinyInteger('is_visible')->default(1)->comment('in case review need not to show yet');
            $table->unsignedInteger('reviewed_by');
            $table->unsignedInteger('reviewable_id')->comment('id of for which it is');
            $table->string('reviewable_type')->comment('for which review it is');
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
        Schema::dropIfExists('news_review');
    }
}
