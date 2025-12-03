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
        Schema::table('blogs', function (Blueprint $table) {
            // add user_id column
            $table->unsignedBigInteger('user_id')->after('id');

            // make it a foreign key referencing users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            // drop foreign key first
            $table->dropForeign(['user_id']);

            // now drop the column
            $table->dropColumn('user_id');
        });
    }
};
