<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->string('monitoring_uuid')->nullable()->after('log_batch_uuid');
        });
    }
};
