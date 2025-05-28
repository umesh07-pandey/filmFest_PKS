<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use League\CommonMark\Reference\Reference;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        //
         Schema::table('event', function (Blueprint $table) {
           
            $table->dropColumn('category_id');
           // $table->foreignId('category_id')->nullable()->references('id')->on('category')->onDelete('cascade'); // or whatever action you intend
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
          Schema::table('event', function (Blueprint $table) {
           
            $table->dropColumn("category_id");
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade'); // or whatever action you intend
            
        });
    }
};
