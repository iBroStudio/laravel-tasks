<?php

declare(strict_types=1);

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->json('payload')->nullable();
            $table->string('state')->default(ProcessStatesEnum::PENDING);
            $table->nullableMorphs('processable');
            $table->json('processable_dto')->nullable();
            $table->unsignedBigInteger('parent_process_id')->nullable();
            $table->string('log_batch_uuid')->nullable();
            $table->timestamps();

            $table->foreign('parent_process_id')->references('id')->on('processes');
        });

        Schema::create('processes_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->nullable()->constrained('processes');
            $table->string('type')->nullable();
            $table->foreignId('as_process_id')->nullable()->constrained('processes');
            $table->string('state')->default(TaskStatesEnum::PENDING);
        });
    }

    public function down()
    {
        Schema::dropIfExists('processes_tasks');
        Schema::dropIfExists('processes');
    }
};
