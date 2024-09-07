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
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('house_owner_id');
			$table->foreign('house_owner_id')->references('id')->on('house_owners')->onDelete('cascade');
			$table->string('house_name', 255);
			$table->integer('house_number');
			$table->string('address', 100);
			$table->string('division', 50);
			$table->string('city', 50);
			$table->string('country', 50);

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
        Schema::dropIfExists('houses');
    }
};
