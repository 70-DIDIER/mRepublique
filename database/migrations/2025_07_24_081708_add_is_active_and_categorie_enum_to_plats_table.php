<?php

use App\Models\Plat;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plats', function (Blueprint $table) {
            if (Schema::hasColumn('plats', 'categorie')) {
                $table->dropColumn('categorie');
            }
            $table->enum('categorie', Plat::CATEGORIES)->after('nom');
            $table->boolean('is_active')->default(true)->after('prix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plats', function (Blueprint $table) {
            $table->dropColumn('categorie');
            $table->dropColumn('is_active');
        });
    }
};
