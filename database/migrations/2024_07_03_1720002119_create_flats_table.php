<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('house_id');
			$table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');
			$table->string('address', 255);
			$table->string('floor_number', 20);
			$table->string('flat_number', 20);
			$table->integer('num_bedrooms');
			$table->integer('num_bathrooms');
			$table->decimal('rent', 10,2);
			$table->boolean('availability');
			$table->date('available_date');

            $table->enum('status',['Active','Inactive','Deleted'])->default('Active');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flats');
    }
};
