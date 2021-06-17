<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')
                  ->nullable()
                  ->references('id')
                  ->on('menus')
                  ->onUpdate('RESTRICT')
                  ->onDelete('RESTRICT');
            $table->json('label');
            $table->string('gates')->nullable();
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
