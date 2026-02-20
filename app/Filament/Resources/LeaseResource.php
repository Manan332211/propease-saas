<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaseResource\Pages;
use App\Filament\Resources\LeaseResource\RelationManagers;
use App\Models\Lease;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaseResource extends Resource
{
    protected static ?string $model = Lease::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contract Details')
                    ->description('Link a tenant to a unit and define the lease terms.')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            // Select the Unit
                            Forms\Components\Select::make('unit_id')
                                ->relationship('unit', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->label('Property Unit'),

                            // Select the Tenant (We map the ID to the linked User's name)
                            Forms\Components\Select::make('tenant_id')
                                ->label('Tenant')
                                ->options(\App\Models\Tenant::with('user')->get()->pluck('user.name', 'id'))
                                ->searchable()
                                ->required(),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('start_date')
                                ->required()
                                ->native(false),

                            Forms\Components\DatePicker::make('end_date')
                                ->required()
                                ->after('start_date') // Validation: End date MUST be after start date
                                ->native(false),
                        ]),
                    ]),

                Forms\Components\Section::make('Financial & Legal')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('rent_amount')
                                ->required()
                                ->numeric()
                                ->prefix('AED')
                                ->label('Total Annual Rent'),

                            Forms\Components\Select::make('payment_frequency')
                                ->required()
                                ->options([
                                    'monthly' => 'Monthly (12 Cheques)',
                                    'quarterly' => 'Quarterly (4 Cheques)',
                                    'yearly' => 'Yearly (1 Cheque)',
                                ])
                                ->default('yearly'),
                        ]),

                        Forms\Components\TextInput::make('contract_number')
                            ->label('Ejari / Contract Number')
                            ->maxLength(255),

                        // The Senior Flex: Handling File Uploads easily
                        Forms\Components\FileUpload::make('document_path')
                            ->label('Signed Contract (PDF)')
                            ->directory('lease-documents')
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // We grab the user's name through the tenant relationship
                Tables\Columns\TextColumn::make('tenant.user.name')
                    ->label('Tenant Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('rent_amount')
                    ->money('AED', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Expires On')
                    ->date()
                    ->sortable(),

                // Dynamic Status Badge based on the date!
                Tables\Columns\BadgeColumn::make('status')
                    ->getStateUsing(function ($record) {
                        return now()->greaterThan($record->end_date) ? 'Expired' : 'Active';
                    })
                    ->colors([
                        'danger' => 'Expired',
                        'success' => 'Active',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Allows the landlord to download the contract directly from the table
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->document_path ? asset('storage/' . $record->document_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->document_path !== null),
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
            'index' => Pages\ListLeases::route('/'),
            'create' => Pages\CreateLease::route('/create'),
            'edit' => Pages\EditLease::route('/{record}/edit'),
        ];
    }
}
