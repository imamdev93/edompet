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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('from_wallet_id')->unsigned();
            $table->bigInteger('to_wallet_id')->unsigned();
            $table->double('amount');
            $table->string('type');
            $table->string('note')->nullable();
            $table->timestamps();
            $table->string('created_by');
            $table->string('updated_by')->nullable();

            $table->foreign('from_wallet_id')->references('id')->on('wallets')->onUpdate('cascade');
            $table->foreign('to_wallet_id')->references('id')->on('wallets')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
};
