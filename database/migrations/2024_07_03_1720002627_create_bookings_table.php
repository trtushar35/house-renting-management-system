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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flat_id')->nullable();
			$table->foreign('flat_id')->references('id')->on('flats')->onDelete('cascade');
			$table->unsignedBigInteger('room_id')->nullable();
			$table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
			$table->unsignedBigInteger('tenant_id')->nullable();
			$table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
			$table->unsignedBigInteger('owner_id')->nullable();
			$table->foreign('owner_id')->references('id')->on('house_owners')->onDelete('cascade');
			$table->decimal('rent', 10,2);
            $table->enum('booking_status',['Pending','Cancel', 'Approved','Rejected'])->default('Pending');
            $table->string('payment_status', 50)->default('Pending');
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
        Schema::dropIfExists('bookings');
    }
};
