<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyTipResource\Pages;
use App\Models\DailyTip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DailyTipResource extends Resource
{
    protected static ?string $model = DailyTip::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Tips Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Daily Tip';

    protected static ?string $pluralModelLabel = 'Daily Tips';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Daily Tip Information')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->unique(
                                table: DailyTip::class,
                                column: 'date',
                                ignoreRecord: true,
                                modifyRuleUsing: function ($rule, $get) {
                                    return $rule->where('shift_period', $get('shift_period'));
                                }
                            )
                            ->default(now())
                            ->helperText('Each date can have one AM and one PM entry')
                            ->columnSpan(1),

                        Forms\Components\Select::make('shift_period')
                            ->required()
                            ->options([
                                'AM' => 'AM (Morning Shift)',
                                'PM' => 'PM (Evening Shift)',
                            ])
                            ->default('AM')
                            ->helperText('Select whether this is for the morning or evening shift')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Total tips amount for the shift')
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('notes')
                            ->placeholder('Optional notes about the tips for this day...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date('M j, Y')
                    ->sortable()
                    ->searchable()
                    ->description(fn (DailyTip $record): string => $record->day_of_week),

                Tables\Columns\TextColumn::make('shift_period')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'AM' => 'warning',
                        'PM' => 'success',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'AM' => 'heroicon-m-sun',
                        'PM' => 'heroicon-m-moon',
                    })
                    ->sortable()
                    ->label('Shift'),

                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('USD')
                            ->label('Total'),
                        Tables\Columns\Summarizers\Average::make()
                            ->money('USD')
                            ->label('Average'),
                    ]),

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
                Tables\Filters\SelectFilter::make('shift_period')
                    ->options([
                        'AM' => 'AM (Morning)',
                        'PM' => 'PM (Evening)',
                    ])
                    ->label('Shift Period'),

                Tables\Filters\Filter::make('current_week')
                    ->query(fn (Builder $query): Builder => $query->currentWeek())
                    ->label('Current Week'),

                Tables\Filters\Filter::make('current_month')
                    ->query(fn (Builder $query): Builder => $query->currentMonth())
                    ->label('Current Month'),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('high_tips')
                    ->query(fn (Builder $query): Builder => $query->where('amount', '>=', 100))
                    ->label('High Tips (≥$100)'),

                Tables\Filters\Filter::make('low_tips')
                    ->query(fn (Builder $query): Builder => $query->where('amount', '<', 50))
                    ->label('Low Tips (<$50)'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Daily Tip Deleted Successfully')
                            ->body('The daily tip has been removed from the system.')
                            ->icon('heroicon-o-trash')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Daily Tips Deleted Successfully')
                                ->body('The selected daily tips have been removed from the system.')
                                ->icon('heroicon-o-trash')
                        ),
                ]),
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('No daily tips recorded')
            ->emptyStateDescription('Start recording daily tip amounts to track performance.')
            ->emptyStateIcon('heroicon-o-currency-dollar');
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
            'index' => Pages\ListDailyTips::route('/'),
            'create' => Pages\CreateDailyTip::route('/create'),
            'edit' => Pages\EditDailyTip::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}
