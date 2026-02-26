<?php

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
        Schema::create('department_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('from_department_id')->nullable();
            $table->foreign('from_department_id')->references('id')->on('departments')->nullOnDelete();
            $table->unsignedBigInteger('to_department_id');
            $table->foreign('to_department_id')->references('id')->on('departments')->restrictOnDelete();

            $table->uuid('requested_by_id');
            $table->string('requested_by_type');

            $table->uuid('receiver_id')->nullable();
            $table->string('receiver_type')->nullable();

            $table->string('status')->default('pending_approval');
            $table->uuid('approved_by_id')->nullable();
            $table->string('approved_by_type')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->uuid('dispatched_by_id')->nullable();
            $table->string('dispatched_by_type')->nullable();
            $table->timestamp('dispatched_at')->nullable();

            $table->uuid('received_by_id')->nullable();
            $table->string('received_by_type')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('department_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_transfer_id');
            $table->foreign('department_transfer_id')
                ->references('id')->on('department_transfers')
                ->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->unsignedBigInteger('uom_id')->nullable();
            $table->foreign('uom_id')->references('id')->on('units_of_measure')->nullOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->decimal('quantity_dispatched', 12, 2)->default(0);
            $table->decimal('quantity_received', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_transfer_items');
        Schema::dropIfExists('department_transfers');
    }
};
