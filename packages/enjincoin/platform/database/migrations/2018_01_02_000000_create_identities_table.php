<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdentitiesTable extends Migration
{
    // Table to hold identities

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enjin_identities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('ethereum_address')->nullable();
            $table->string('linking_code')->nullable();
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
        Schema::dropIfExists('enjin_identities');
    }
}
