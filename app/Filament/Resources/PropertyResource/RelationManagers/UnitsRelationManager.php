<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

   public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Unit Number/Name')
                    ->placeholder('e.g., Apt 101'),

                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('bedrooms')
                        ->required()
                        ->numeric()
                        ->default(1),

                    Forms\Components\TextInput::make('bathrooms')
                        ->required()
                        ->numeric()
                        ->default(1),

                    Forms\Components\TextInput::make('area_sqft')
                        ->required()
                        ->numeric()
                        ->label('Area (Sq. Ft.)'),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('status')
                        ->required()
                        ->options([
                            'vacant' => 'Vacant',
                            'occupied' => 'Occupied',
                            'maintenance' => 'Under Maintenance',
                        ])
                        ->default('vacant'),

                    Forms\Components\TextInput::make('market_rent')
                        ->required()
                        ->numeric()
                        ->prefix('AED') // Using AED for the local market context
                        ->label('Target Rent'),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->weight('bold'),
                Tables\Columns\TextColumn::make('bedrooms')->numeric(),
                
                // This adds beautiful color-coded badges for the status
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'maintenance',
                        'warning' => 'vacant',
                        'success' => 'occupied',
                    ]),
                    
                Tables\Columns\TextColumn::make('market_rent')
                    ->money('AED', true) // Automatically formats as currency
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Button to add a unit
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
