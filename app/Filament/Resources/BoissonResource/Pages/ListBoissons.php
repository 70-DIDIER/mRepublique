<?php

namespace App\Filament\Resources\BoissonResource\Pages;

use App\Filament\Resources\BoissonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBoissons extends ListRecords
{
    protected static string $resource = BoissonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
