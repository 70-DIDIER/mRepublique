<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommandeResource\Pages;
use App\Models\Commande;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class CommandeResource extends Resource
{
    protected static ?string $model = Commande::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Commandes';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('statut')
                ->options([
                    'en_attente' => 'En attente',
                    'en_cours'   => 'En cours',
                    'livree'     => 'Livrée',
                    'annulee'    => 'Annulée',
                ])
                ->required(),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Informations client')->schema([
                Infolists\Components\TextEntry::make('user.name')->label('Client'),
                Infolists\Components\TextEntry::make('user.telephone')->label('Téléphone'),
                Infolists\Components\TextEntry::make('adresse_livraison')->label('Adresse'),
                Infolists\Components\TextEntry::make('type_livraison')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'a_domicile' => 'warning',
                        'sur_place'  => 'success',
                        default      => 'gray',
                    }),
            ])->columns(2),

            Infolists\Components\Section::make('Commande')->schema([
                Infolists\Components\TextEntry::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'en_attente' => 'warning',
                        'en_cours'   => 'info',
                        'livree'     => 'success',
                        'annulee'    => 'danger',
                        default      => 'gray',
                    }),

                Infolists\Components\TextEntry::make('montant_total')
                    ->money('XOF', locale: 'fr')
                    ->label('Total'),

                Infolists\Components\IconEntry::make('est_paye')
                    ->label('Payé')
                    ->boolean(),

                Infolists\Components\TextEntry::make('commentaire')
                    ->columnSpanFull(),

                Infolists\Components\TextEntry::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'en_attente' => 'warning',
                        'en_cours'   => 'info',
                        'livree'     => 'success',
                        'annulee'    => 'danger',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('montant_total')
                    ->money('XOF', locale: 'fr')
                    ->sortable(),

                Tables\Columns\IconColumn::make('est_paye')
                    ->label('Payé')
                    ->boolean(),

                Tables\Columns\TextColumn::make('type_livraison')
                    ->badge()
                    ->label('Type'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->options([
                        'en_attente' => 'En attente',
                        'en_cours'   => 'En cours',
                        'livree'     => 'Livrée',
                        'annulee'    => 'Annulée',
                    ]),

                SelectFilter::make('type_livraison')
                    ->options([
                        'sur_place'  => 'Sur place',
                        'a_domicile' => 'À domicile',
                    ]),

                TernaryFilter::make('est_paye')
                    ->label('Paiement')
                    ->trueLabel('Payées')
                    ->falseLabel('Non payées'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('updateStatut')
                    ->label('Changer statut')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('statut')
                            ->options([
                                'en_attente' => 'En attente',
                                'en_cours'   => 'En cours',
                                'livree'     => 'Livrée',
                                'annulee'    => 'Annulée',
                            ])
                            ->required(),
                    ])
                    ->action(function (Commande $record, array $data): void {
                        $record->update(['statut' => $data['statut']]);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommandes::route('/'),
            'view'  => Pages\ViewCommande::route('/{record}'),
        ];
    }
}
