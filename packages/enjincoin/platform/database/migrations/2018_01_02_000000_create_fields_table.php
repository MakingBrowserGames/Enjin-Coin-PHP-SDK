<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
    // Table to hold fields.

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enjin_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->integer('searchable')->default(1);
            $table->integer('displayable')->default(1);
            $table->integer('unique')->default(1);
            $table->integer('updated_at');
            $table->integer('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enjin_fields');
    }
}
