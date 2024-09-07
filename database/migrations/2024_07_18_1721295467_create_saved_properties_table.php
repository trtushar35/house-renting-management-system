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
        Schema::create('saved_properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
			$table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
			$table->unsignedBigInteger('owner_id')->nullable();
			$table->foreign('owner_id')->references('id')->on('house_owners')->onDelete('cascade');
			$table->unsignedBigInteger('flat_id');
			$table->foreign('flat_id')->references('id')->on('flats')->onDelete('cascade');
			
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
        Schema::dropIfExists('saved_properties');
    }
};
