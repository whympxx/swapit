<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Skema SWAP-IT. Dibuat "idempotent": aman dijalankan pada database lama
 * (swapit_db dari versi PHP native) maupun database kosong.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('username', 50)->unique();
                $table->string('email', 100)->unique();
                $table->string('password');
                $table->string('full_name', 100);
                $table->string('phone', 20)->nullable();
                $table->string('university', 100)->nullable();
                $table->string('profile_photo')->nullable()->default('default-avatar.png');
                $table->rememberToken();
                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->rememberToken()->after('profile_photo');
            });
        }

        if (!Schema::hasTable('items')) {
            Schema::create('items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('title', 200);
                $table->text('description');
                $table->string('category', 50);
                $table->enum('condition_item', ['Baru', 'Seperti Baru', 'Baik', 'Layak Pakai'])->default('Baik');
                $table->string('photo');
                $table->enum('status', ['available', 'pending', 'traded'])->default('available');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('offers')) {
            Schema::create('offers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('item_wanted_id')->constrained('items')->cascadeOnDelete();
                $table->foreignId('item_offered_id')->constrained('items')->cascadeOnDelete();
                $table->text('message')->nullable();
                $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled'])->default('pending');
                $table->timestamps();
            });
        } else {
            // Versi lama hanya punya pending/accepted/rejected
            DB::statement("ALTER TABLE offers MODIFY status ENUM('pending','accepted','rejected','completed','cancelled') DEFAULT 'pending'");
        }

        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('title', 200);
                $table->text('message');
                $table->string('link')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('token')->unique();
                $table->dateTime('expires_at')->index();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        // Sengaja tidak menghapus tabel agar data lama tidak hilang.
    }
};
