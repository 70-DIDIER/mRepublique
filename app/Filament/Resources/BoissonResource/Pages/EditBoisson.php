<?php

namespace App\Filament\Resources\BoissonResource\Pages;

use App\Filament\Resources\BoissonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBoisson extends EditRecord
{
    protected static string $resource = BoissonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
