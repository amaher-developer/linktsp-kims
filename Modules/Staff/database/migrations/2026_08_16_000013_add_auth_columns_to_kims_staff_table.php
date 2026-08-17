<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Not part of kims_schema.sql: kims_staff has no credential columns in the
 * source schema. The manager admin panel needs a password to authenticate
 * against, so this migration adds the minimum Breeze/Auth needs on top of
 * the source schema rather than editing kims_staff.php's own migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kims_staff', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->timestamp('email_verified_at')->nullable()->after('password');
            $table->rememberToken()->after('email_verified_at');
            $table->unique('email', 'uq_staff_email');
        });
    }

    public function down(): void
    {
        Schema::table('kims_staff', function (Blueprint $table) {
            $table->dropUnique('uq_staff_email');
            $table->dropColumn(['password', 'remember_token', 'email_verified_at']);
        });
    }
};
