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
        Schema::create('receivable_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('receivable_id')->unsigned();
            $table->double('amount');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('receivable_id')->references('id')->on('receivables')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receivable_histories');
    }
};
