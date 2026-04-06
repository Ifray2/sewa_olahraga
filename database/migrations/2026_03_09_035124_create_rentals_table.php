<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->string('rental_code')->unique();     // kode unik transaksi, misal: RENT-20250301-001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('duration_days');    // dihitung otomatis
            $table->decimal('total_price', 12, 2);
            $table->enum('status', [
                'pending',      // menunggu konfirmasi
                'confirmed',    // dikonfirmasi petugas
                'active',       // sedang disewa
                'returned',     // sudah dikembalikan
                'cancelled',    // dibatalkan
            ])->default('pending');
            $table->text('notes')->nullable();           // catatan dari user
            $table->foreignId('handled_by')->nullable()  // petugas yang memproses
                ->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};