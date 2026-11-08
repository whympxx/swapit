<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fitur tambahan: favorit (wishlist), ulasan setelah barter, dan jumlah dilihat.
 * Tipe kolom relasi mengikuti tipe "id" tabel lama (INT pada database versi native,
 * BIGINT UNSIGNED pada database baru) supaya foreign key selalu cocok.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('items', 'views')) {
            Schema::table('items', function (Blueprint $table) {
                $table->unsignedInteger('views')->default(0)->after('status');
            });
        }

        if (!Schema::hasTable('favorites')) {
            Schema::create('favorites', function (Blueprint $table) {
                $table->id();
                $this->relation($table, 'user_id', 'users');
                $this->relation($table, 'item_id', 'items');
                $table->timestamp('created_at')->useCurrent();
                $table->unique(['user_id', 'item_id']);
            });
        }

        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $this->relation($table, 'offer_id', 'offers');
                $this->relation($table, 'reviewer_id', 'users');
                $this->relation($table, 'reviewee_id', 'users');
                $table->unsignedTinyInteger('rating');
                $table->text('comment')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->unique(['offer_id', 'reviewer_id']);
            });
        }
    }

    private function relation(Blueprint $table, string $column, string $on): void
    {
        $type = strtolower(Schema::getColumnType($on, 'id'));
        if (str_contains($type, 'bigint')) {
            $table->unsignedBigInteger($column);
        } else {
            $table->integer($column);
        }
        $table->foreign($column)->references('id')->on($on)->cascadeOnDelete();
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('favorites');
        if (Schema::hasColumn('items', 'views')) {
            Schema::table('items', fn (Blueprint $table) => $table->dropColumn('views'));
        }
    }
};
