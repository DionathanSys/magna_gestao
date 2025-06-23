<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ViagemResource;
use App\Filament\Resources\ViagemResource\Widgets\AdvancedStatsOverviewWidget;
use App\Models\Viagem;
use Filament;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DashboardViagem extends Page
{


    protected static string $view = 'filament.pages.dashboard-viagem';

    protected static ?string $navigationGroup = 'Viagens';

    protected static ?string $pluralModelLabel = 'Dash Viagens';

    protected static ?string $pluralLabel = 'Dash Viagens';

    protected static ?string $label = 'Dash Viagem';

}
