<?php

namespace App\Filament\Resources\DemoOrderResource\Pages;

use App\Filament\Resources\DemoOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDemoOrder extends EditRecord
{
    protected static string $resource = DemoOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
