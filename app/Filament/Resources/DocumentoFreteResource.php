<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentoFreteResource\Pages;
use App\Filament\Resources\DocumentoFreteResource\RelationManagers;
use App\Frete\TipoDocumentoEnum;
use App\Models\DocumentoFrete;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentoFreteResource extends Resource
{
    protected static ?string $model = DocumentoFrete::class;

    protected static ?string $navigationGroup = 'Viagens';

    protected static ?string $pluralModelLabel = 'Documentos Frete';

    protected static ?string $pluralLabel = 'Documentos Frete';

    protected static ?string $label = 'Documento Frete';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('veiculo_id')
                    ->relationship('veiculo', 'placa')
                    ->required(),
                Forms\Components\Select::make('integrado_id')
                    ->relationship('integrado', 'nome')
                    ->required(),
                Forms\Components\TextInput::make('numero_documento')
                    ->maxLength(50),
                Forms\Components\TextInput::make('documento_transporte')
                    ->maxLength(50),
                Forms\Components\Select::make('tipo_documento')
                    ->options(TipoDocumentoEnum::toSelectArray()),
                Forms\Components\DatePicker::make('data_emissao')
                    ->label('Dt. de Emissão')
                    ->required(),
                Forms\Components\TextInput::make('valor_total')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('valor_icms')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('municipio')
                    ->maxLength(255),
                Forms\Components\TextInput::make('estado')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->numeric()
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('integrado.nome')
                    ->numeric()
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('numero_documento')
                    ->label('Nº Doc.')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('documento_transporte')
                    ->label('Doc. Transporte')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('tipo_documento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_emissao')
                    ->label('Dt. de Emissão')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor_total')
                    ->label('Vlr. Total')
                    ->money('BRL', locale: 'pt-BR')
                    ->sortable()
                    ->summarize(Sum::make()->money('BRL')),
                Tables\Columns\TextColumn::make('valor_icms')
                    ->label('Vlr. ICMS')
                    ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                    ->sortable()
                    ->summarize(Sum::make()->money('BRL')),
                Tables\Columns\TextColumn::make('municipio')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('estado')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->searchOnBlur()
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocumentoFretes::route('/'),
            'create' => Pages\CreateDocumentoFrete::route('/create'),
            'edit' => Pages\EditDocumentoFrete::route('/{record}/edit'),
        ];
    }
}
