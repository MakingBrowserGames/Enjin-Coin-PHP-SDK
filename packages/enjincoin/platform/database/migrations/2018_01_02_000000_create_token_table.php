<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokenTable extends Migration
{
    // Pivot table for fields linked to identities.

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enjin_tokens', function (Blueprint $table) {
            $table->integer('token_id')->primary();
            $table->integer('app_id');
            $table->string('creator', 255)->nullable();
            $table->string('adapter', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('icon',255)->nullable();
            $table->string('totalSupply', 255)->nullable();
            $table->string('exchangeRate', 255)->nullable();
            $table->tinyInteger('decimals')->default(0);
            $table->string('maxMeltFee', 255)->nullable();
            $table->string('meltFee', 255)->nullable();
            $table->tinyInteger('transferable')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enjin_tokens');
    }
}
