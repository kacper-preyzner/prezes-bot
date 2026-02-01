<?php

namespace App\Filament\Resources\PlannedTasks\Pages;

use App\Filament\Resources\PlannedTasks\PlannedTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlannedTask extends CreateRecord
{
    protected static string $resource = PlannedTaskResource::class;
}
