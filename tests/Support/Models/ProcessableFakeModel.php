<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Models;

use IBroStudio\Tasks\Concerns\IsProcessable;
use IBroStudio\Tasks\Contracts\ProcessableContract;
use IBroStudio\Tasks\Tests\Support\Database\Factories\ProcessableFakeModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessableFakeModel extends Model implements ProcessableContract
{
    use HasFactory;
    use IsProcessable;

    protected $fillable = [
        'name',
        'description',
    ];

    protected static function newFactory(): ProcessableFakeModelFactory
    {
        return ProcessableFakeModelFactory::new();
    }
}
