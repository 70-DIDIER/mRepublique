<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BoissonResource\Pages;
use App\Models\Boisson;
use App\Models\Plat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Storage;

class BoissonResource extends Resource
{
    protected static ?string $model = Boisson::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Boissons';
    protected static ?string $navigationGroup = 'Menu';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('nom')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('categorie')
                    ->required()
                    ->options(array_combine(Plat::CATEGORIES, Plat::CATEGORIES)),

                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('prix')
                    ->required()
                    ->numeric()
                    ->prefix('F CFA')
                    ->minValue(0),

                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('images')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('4:3')
                    ->imageResizeTargetWidth('1200')
                    ->imageResizeTargetHeight('900')
                    ->maxSize(2048)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->getStateUsing(fn ($record) => $record->image ? asset('storage/' . $record->image) : null)
                    ->circular(),

                Tables\Columns\TextColumn::make('nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('categorie')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('prix')
                    ->money('XOF', locale: 'fr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categorie')
                    ->options(array_combine(Plat::CATEGORIES, Plat::CATEGORIES)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function (Boisson $record) {
                        if ($record->image) {
                            Storage::disk('public')->delete($record->image);
                        }
                    }),
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
            'index'  => Pages\ListBoissons::route('/'),
            'create' => Pages\CreateBoisson::route('/create'),
            'edit'   => Pages\EditBoisson::route('/{record}/edit'),
        ];
    }
}
