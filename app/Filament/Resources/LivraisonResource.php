<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LivraisonResource\Pages;
use App\Models\Livraison;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class LivraisonResource extends Resource
{
    protected static ?string $model = Livraison::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Livraisons';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Livraison';
    protected static ?string $pluralModelLabel = 'Livraisons';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('livreur.name')
                    ->label('Livreur')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('commande.user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('commande.adresse_livraison')
                    ->label('Adresse')
                    ->limit(40),

                Tables\Columns\TextColumn::make('commande.montant_total')
                    ->label('Montant')
                    ->money('XOF', locale: 'fr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'en_chemin' => 'info',
                        'livree'    => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'en_chemin' => 'En chemin',
                        'livree'    => 'Livrée',
                        default     => $state,
                    }),

                Tables\Columns\IconColumn::make('commande.est_paye')
                    ->label('Payé')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->options([
                        'en_chemin' => 'En chemin',
                        'livree'    => 'Livrée',
                    ]),

                SelectFilter::make('livreur_id')
                    ->label('Livreur')
                    ->options(User::where('role', 'livreur')->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Livreur')->schema([
                Infolists\Components\TextEntry::make('livreur.name')->label('Nom'),
                Infolists\Components\TextEntry::make('livreur.telephone')->label('Téléphone'),
                Infolists\Components\TextEntry::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'en_chemin' => 'info',
                        'livree'    => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'en_chemin' => 'En chemin',
                        'livree'    => 'Livrée',
                        default     => $state,
                    }),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Pris en charge le')
                    ->dateTime('d/m/Y H:i'),
            ])->columns(2),

            Infolists\Components\Section::make('Commande')->schema([
                Infolists\Components\TextEntry::make('commande.user.name')->label('Client'),
                Infolists\Components\TextEntry::make('commande.user.telephone')->label('Téléphone client'),
                Infolists\Components\TextEntry::make('commande.adresse_livraison')->label('Adresse')->columnSpanFull(),
                Infolists\Components\TextEntry::make('commande.montant_total')
                    ->money('XOF', locale: 'fr')
                    ->label('Montant total'),
                Infolists\Components\TextEntry::make('commande.frais_livraison')
                    ->money('XOF', locale: 'fr')
                    ->label('Frais de livraison'),
                Infolists\Components\IconEntry::make('commande.est_paye')
                    ->label('Payé')
                    ->boolean(),
                Infolists\Components\TextEntry::make('commande.statut')
                    ->label('Statut commande')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'en_attente' => 'warning',
                        'en_cours'   => 'info',
                        'livree'     => 'success',
                        'annulee'    => 'danger',
                        default      => 'gray',
                    }),
            ])->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLivraisons::route('/'),
            'view'  => Pages\ViewLivraison::route('/{record}'),
        ];
    }
}
