<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->datetime('creation_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('creator_user_id')->nullable();
            $table->datetime('last_modification_time')->nullable();
            $table->integer('last_modifier_user_id')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->integer('deleter_user_id')->nullable();
            $table->datetime('deletion_time')->nullable();

            $table->string('name', 120);

            $table->unsignedBigInteger('brand_id');  
            $table->foreign('brand_id')->references('id')->on('brands'); 
            
            $table->unsignedBigInteger('category_id');  
            $table->foreign('category_id')->references('id')->on('categories');  
            
            $table->unsignedBigInteger('measurement_unit_id'); 
            $table->foreign('measurement_unit_id')->references('id')->on('measurement_units'); 
            
            $table->string('measurement_unit_name', 120); 
                        
            $table->string('barcode', 100)->nullable();  
            //$table->string('internal_code', 100)->nullable();  
            $table->longText('barcode_path')->nullable(); 
            
            $table->decimal('sale_price', 16, 2)->unsigned(); 
            $table->decimal('purchase_price', 16, 4)->unsigned();  
            $table->decimal('minimum_stock', 20, 2)->unsigned(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
