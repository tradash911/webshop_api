<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(User::class)->constrained();
            $table->decimal('total_price',10,2)->default(0);
            $table->string('order_number')->nullable()->unique();
            $table->enum('status', ['pending', 'paid', 'shipped', 'cancelled'])
            ->default('pending');

            $table->string('name');
            $table->string('zip');
            $table->string('address_line');
            $table->string('city');
            $table->string('email');
            $table->string('phone');

            $table->string('billing_name')->nullable();
            $table->string('billing_zip')->nullable();
            $table->string('billing_address_line')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('company_name')->nullable();
            $table->string('tax_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
