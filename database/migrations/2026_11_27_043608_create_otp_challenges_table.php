<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_challenges', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('user_id');

            $table->string('purpose', 40);
            $table->string('channel', 20)->default('EMAIL');
            $table->string('destination', 255);

            $table->string('code_hash', 64);
            $table->string('session_hash', 64);

            $table->unsignedSmallInteger('attempts')->default(0);

            $table->timestamp('expires_at', 6);
            $table->timestamp('verified_at', 6)->nullable();
            $table->timestamp('consumed_at', 6)->nullable();
            $table->timestamp('revoked_at', 6)->nullable();

            $table->timestamp('created_at', 6);
            $table->timestamp('updated_at', 6);

            $table->foreign('user_id', 'otp_challenges_user_fk')
                ->references('id')
                ->on('users');

            $table->index(
                ['user_id', 'purpose'],
                'idx_otp_user_purpose'
            );

            $table->index(
                'expires_at',
                'idx_otp_expiration'
            );
        });

        if (DB::connection()->getDriverName() === 'oracle') {
            DB::statement("
                ALTER TABLE otp_challenges
                ADD CONSTRAINT ck_otp_purpose
                CHECK (purpose IN (
                    'EMAIL_VERIFICATION',
                    'PASSWORD_RESET',
                    'PASSWORD_CHANGE'
                ))
            ");

            DB::statement("
                ALTER TABLE otp_challenges
                ADD CONSTRAINT ck_otp_channel
                CHECK (channel IN ('EMAIL'))
            ");

            DB::statement("
                ALTER TABLE otp_challenges
                ADD CONSTRAINT ck_otp_attempts
                CHECK (attempts BETWEEN 0 AND 5)
            ");

            DB::statement("
                ALTER TABLE otp_challenges
                ADD CONSTRAINT ck_otp_expiration
                CHECK (expires_at > created_at)
            ");

            DB::statement("
                ALTER TABLE otp_challenges
                ADD CONSTRAINT ck_otp_consumption
                CHECK (
                    consumed_at IS NULL
                    OR verified_at IS NOT NULL
                )
            ");

            DB::statement("
                ALTER TABLE otp_challenges
                ADD CONSTRAINT ck_otp_terminal
                CHECK (
                    consumed_at IS NULL
                    OR revoked_at IS NULL
                )
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_challenges');
    }
};
