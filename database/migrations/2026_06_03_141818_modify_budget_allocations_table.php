<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budget_allocations', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'category_id', 'month', 'year']);
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->dropColumn('percentage');
            
            $table->string('name')->after('user_id');
            $table->enum('amount_type', ['percentage', 'nominal'])->default('percentage')->after('name');
            $table->decimal('amount', 15, 2)->default(0)->after('amount_type');
            $table->string('color')->nullable()->after('year');

            $table->unique(['user_id', 'name', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::table('budget_allocations', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'name', 'month', 'year']);
            $table->dropColumn(['name', 'amount_type', 'amount', 'color']);
            
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->decimal('percentage', 5, 2)->default(0);
            
            $table->unique(['user_id', 'category_id', 'month', 'year']);
        });
    }
};
