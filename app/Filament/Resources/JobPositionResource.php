<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobPositionResource\Pages;
use App\Models\JobPosition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JobPositionResource extends Resource
{
    protected static ?string $model = JobPosition::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Catalogs';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Job Position';

    protected static ?string $pluralModelLabel = 'Job Positions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Job Position Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(JobPosition::class, 'name', ignoreRecord: true)
                            ->placeholder('Enter job position name')
                            ->helperText('The name must be unique')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('points')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->default(0)
                            ->minValue(0)
                            ->placeholder('Enter points value (e.g., 10.50)')
                            ->helperText('Point value for this position (supports decimals)')
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('applies_for_tips')
                            ->label('Applies for Tips')
                            ->default(true)
                            ->helperText('Enable if this position is eligible for tip distribution')
                            ->columnSpan(2),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Position Name'),

                Tables\Columns\TextColumn::make('points')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->label('Points')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),

                Tables\Columns\IconColumn::make('applies_for_tips')
                    ->boolean()
                    ->label('Tips Eligible')
                    ->alignCenter()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Updated'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('applies_for_tips')
                    ->label('Tips Eligibility')
                    ->boolean()
                    ->trueLabel('Tips Eligible')
                    ->falseLabel('Not Tips Eligible')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Job Position Deleted Successfully')
                            ->body('The job position has been removed from the system.')
                            ->icon('heroicon-o-trash')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Job Positions Deleted Successfully')
                                ->body('The selected job positions have been removed from the system.')
                                ->icon('heroicon-o-trash')
                        ),
                ]),
            ])
            ->emptyStateHeading('No job positions found')
            ->emptyStateDescription('Create your first job position to get started.')
            ->emptyStateIcon('heroicon-o-briefcase');
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
            'index' => Pages\ListJobPositions::route('/'),
            'create' => Pages\CreateJobPosition::route('/create'),
            'edit' => Pages\EditJobPosition::route('/{record}/edit'),
        ];
    }
}
