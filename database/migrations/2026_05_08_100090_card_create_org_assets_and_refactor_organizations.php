<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Shared asset library: upload once, reuse across any number of orgs
        Schema::create('org_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // human label e.g. "Barchhain Stamp"
            $table->enum('type', ['logo', 'signature', 'stamp']);
            $table->string('path');                        // relative to public/
            $table->timestamps();
        });

        // Replace the three _path string columns with FK references to org_assets
        Schema::table('organizations', function (Blueprint $table) {
            foreach (['logo_path', 'signature_path', 'stamp_path'] as $column) {
                if (Schema::hasColumn('organizations', $column)) {
                    $table->dropColumn($column);
                }
            }
            $table->foreignId('logo_asset_id')->nullable()->constrained('org_assets')->nullOnDelete()->after('type');
            $table->foreignId('signature_asset_id')->nullable()->constrained('org_assets')->nullOnDelete()->after('logo_asset_id');
            $table->foreignId('stamp_asset_id')->nullable()->constrained('org_assets')->nullOnDelete()->after('signature_asset_id');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['logo_asset_id']);
            $table->dropForeign(['signature_asset_id']);
            $table->dropForeign(['stamp_asset_id']);
            $table->dropColumn(['logo_asset_id', 'signature_asset_id', 'stamp_asset_id']);
            $table->string('logo_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('stamp_path')->nullable();
        });
        Schema::dropIfExists('org_assets');
    }
};
