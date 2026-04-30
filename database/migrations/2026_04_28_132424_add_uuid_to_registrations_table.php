<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('registrations', 'uuid')) {
            Schema::table('registrations', function (Blueprint $blueprint) {
                $blueprint->uuid('uuid')->nullable()->after('id');
            });
        }

        // Populate existing records with UUIDs
        Registration::whereNull('uuid')->get()->each(function ($registration) {
            $registration->uuid = (string) Str::uuid();
            $registration->save();
        });

        // Add non-nullable
        Schema::table('registrations', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });

        // Add unique index if it doesn't exist
        $indexExists = collect(Schema::getIndexes('registrations'))->contains('name', 'registrations_uuid_unique');
        
        if (!$indexExists) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->unique('uuid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $blueprint) {
            $blueprint->dropColumn('uuid');
        });
    }
};
