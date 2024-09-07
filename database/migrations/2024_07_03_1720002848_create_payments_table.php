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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
			$table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
			$table->string('transaction_id', 50)->unique();
			$table->date('payment_date');
			$table->string('payment_month', 50);
			$table->decimal('amount', 10,2);
			$table->decimal('paid_amount', 10,2);
			$table->decimal('due', 10,2);
			$table->string('payment_method', 50);

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
        Schema::dropIfExists('payments');
    }
};
