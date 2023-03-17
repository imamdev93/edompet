<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payable_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payable_id')->unsigned();
            $table->bigInteger('wallet_id')->unsigned();
            $table->double('amount');
            $table->string('note')->nullable();

            $table->foreign('payable_id')->references('id')->on('payables')->onUpdate('cascade');
            $table->foreign('wallet_id')->references('id')->on('wallets')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payable_histories');
    }
};
