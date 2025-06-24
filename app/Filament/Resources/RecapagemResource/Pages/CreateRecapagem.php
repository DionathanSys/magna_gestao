<?php

namespace App\Filament\Resources\RecapagemResource\Pages;

use App\Filament\Resources\RecapagemResource;
use App\Services\Pneus\PneuService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRecapagem extends CreateRecord
{
    protected static string $resource = RecapagemResource::class;

}
