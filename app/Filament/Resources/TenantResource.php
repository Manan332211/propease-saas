<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tenant Profile')
                    ->description('Contact and legal details for the tenant.')
                    ->schema([
                        // This links the Tenant to a User account
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Linked User Account (For Portal Access)'),

                        Forms\Components\TextInput::make('phone_number')
                            ->required()
                            ->tel()
                            ->label('Phone Number'),

                        // Using a Grid to keep legal documents organized side-by-side
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('national_id_number')
                                ->label('Emirates ID (National ID)')
                                ->maxLength(255),

                            Forms\Components\TextInput::make('passport_number')
                                ->label('Passport Number')
                                ->maxLength(255),

                            Forms\Components\DatePicker::make('passport_expiry')
                                ->label('Passport Expiry Date')
                                ->native(false), // Uses a nicer calendar UI
                        ]),
                    ])
            ]);
    }
   public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // We use 'user.name' to grab the name from the linked User table!
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Tenant Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('national_id_number')
                    ->label('Emirates ID')
                    ->searchable(),

                Tables\Columns\TextColumn::make('passport_expiry')
                    ->date()
                    ->sortable()
                    // If the passport is expiring soon, make it red! (A great UX detail)
                    ->color(fn ($state): string => now()->diffInDays($state) < 30 ? 'danger' : 'success'),
            ])
            ->filters([
                //
            ])
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
