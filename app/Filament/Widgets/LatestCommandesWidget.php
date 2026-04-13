<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CommandeResource;
use App\Models\Commande;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestCommandesWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Dernières commandes';

    public function table(Table $table): Table
    {
        return $table
            ->query(Commande::with('user')->latest()->limit(8))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->width(60),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable(),

                Tables\Columns\TextColumn::make('adresse_livraison')
                    ->label('Adresse')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type_livraison')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'a_domicile' => 'warning',
                        'sur_place'  => 'info',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'a_domicile' => 'À domicile',
                        'sur_place'  => 'Sur place',
                        default      => $state,
                    }),

                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'en_attente' => 'warning',
                        'en_cours'   => 'info',
                        'livree'     => 'success',
                        'annulee'    => 'danger',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'en_attente' => 'En attente',
                        'en_cours'   => 'En cours',
                        'livree'     => 'Livrée',
                        'annulee'    => 'Annulée',
                        default      => $state,
                    }),

                Tables\Columns\TextColumn::make('montant_total')
                    ->money('XOF', locale: 'fr')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                Tables\Columns\IconColumn::make('est_paye')
                    ->label('Payé')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('voir')
                    ->url(fn (Commande $record) => CommandeResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye')
                    ->color('primary'),
            ]);
    }
}
