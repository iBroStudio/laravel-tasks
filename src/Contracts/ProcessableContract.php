<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface ProcessableContract
{
    public function processes(): MorphMany;
}
