
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('challenge_id');

            $table->timestamp('attempted_at', 6);

            $table->foreign(
                'challenge_id',
                'otp_attempts_challenge_fk'
            )->references('id')
                ->on('otp_challenges');

            $table->index(
                ['challenge_id', 'attempted_at'],
                'idx_otp_attempt_challenge'
            );

            $table->index(
                'attempted_at',
                'idx_otp_attempt_date'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_attempts');
    }
};
