<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    // Pivot table for fields linked to identities.

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enjin_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_id', 255)->nullable();
            $table->integer('app_id');
            $table->integer('identity_id')->nullable();
            $table->enum('type', ['buy', 'sell', 'send', 'use', 'trade', 'melt', 'subscribe'])->default('send');
            $table->integer('recipient_id')->nullable();
            $table->string('recipient_address', 255)->nullable();
            $table->string('icon',255)->nullable();
            $table->string('title', 255)->nullable();
            $table->integer('token_id')->nullable();
            $table->string('value', 255)->default('0');
            $table->enum('state', ['pending', 'broadcasted', 'executed', 'confirmed', 'canceled_user', 'canceled_platform', 'failed'])->default('pending');
            $table->integer('accepted')->default(0);
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
        Schema::dropIfExists('enjin_transactions');
    }
}
