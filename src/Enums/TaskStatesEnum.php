<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Enums;

enum TaskStatesEnum: string
{
    case PENDING = 'pending';
    case STARTED = 'started';
    case PROCESSING = 'processing';
    case WAITING = 'waiting';
    case RESUME = 'resume';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case ABORTED = 'aborted';
    case SKIPPED = 'skipped';

    public function getLabel(): array|string
    {
        return match ($this) {
            self::PENDING => trans('pending'),
            self::STARTED => trans('started'),
            self::PROCESSING => trans('processing'),
            self::WAITING => trans('waiting'),
            self::RESUME => trans('resume'),
            self::COMPLETED => trans('completed'),
            self::FAILED => trans('failed'),
            self::ABORTED => trans('aborted'),
            self::SKIPPED => trans('skipped'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::STARTED => 'info',
            self::PROCESSING => 'warning',
            self::WAITING => 'gray',
            self::RESUME => 'info',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
            self::ABORTED => 'danger',
            self::SKIPPED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-m-clock',
            self::STARTED => 'heroicon-m-play',
            self::PROCESSING => 'heroicon-m-cog-6-tooth',
            self::WAITING => 'heroicon-m-clock',
            self::RESUME => 'heroicon-m-cog-6-tooth',
            self::COMPLETED => 'heroicon-m-check',
            self::FAILED => 'heroicon-m-x-mark',
            self::ABORTED => 'heroicon-m-x-mark',
            self::SKIPPED => 'heroicon-m-x-mark',
        };
    }
}
