<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdentityFieldTable extends Migration
{
    // Pivot table for fields linked to identities.

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enjin_identity_field', function (Blueprint $table) {
            $table->integer('identity_id');
            $table->integer('field_id');
            $table->string('field_value');
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
        Schema::dropIfExists('enjin_identity_field');
    }
}
