<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('subdomain')->unique(); // Subdomain for each tenant
            $table->timestamps();
        });

        // Add tenant_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });

        Schema::dropIfExists('tenants');
    }
};
