<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Property Details')
                    ->description('Enter the main details of the building or compound.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Property Name')
                            ->placeholder('e.g., Marina Heights Tower'),

                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(), // Makes the address bar take up the full width

                        // Using a Grid puts City and Country side-by-side
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('city')
                                ->required()
                                ->maxLength(255)
                                ->default('Dubai'), // Defaults to Dubai to save typing

                            Forms\Components\TextInput::make('country')
                                ->required()
                                ->maxLength(255)
                                ->default('United Arab Emirates'),
                        ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable() // Adds a search bar that filters by name
                    ->sortable()   // Allows clicking the column header to sort A-Z
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hides by default but lets the user toggle it on
            ])
            ->filters([
                // We will add advanced filters here later
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Adds the "Edit" button
                Tables\Actions\DeleteAction::make(), // Adds the "Delete" button
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
            RelationManagers\UnitsRelationManager::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
