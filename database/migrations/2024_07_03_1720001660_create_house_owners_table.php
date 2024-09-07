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
        Schema::create('house_owners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
			$table->string('email', 100);
			$table->string('phone', 20);
			$table->string('address', 20);
			$table->string('password', 255);
			$table->string('role', 20)->default('house_owner');
			$table->string('image', 255)->nullable();

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
        Schema::dropIfExists('house_owners');
    }
};
