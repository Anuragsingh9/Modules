<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelMetasTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('model_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->json('fields')->nullable();
            $table->unsignedInteger('modelable_id');
            $table->string('modelable_type');
            $table->timestamps();
            $table->softDeletes();
    
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('model_metas');
    }
}
