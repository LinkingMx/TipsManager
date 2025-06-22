<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftResource\Pages;
use App\Models\Shift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Catalogs';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Shift Information')
                    ->description('Define the shift name and working hours')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Shift Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., Morning Shift, Evening Shift'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('start_hour')
                                    ->label('Start Time')
                                    ->required()
                                    ->seconds(false)
                                    ->displayFormat('g:i A')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Clear end time when start time changes to force user to re-select
                                        if ($state && $get('end_hour')) {
                                            $start = \Carbon\Carbon::parse($state);
                                            $end = \Carbon\Carbon::parse($get('end_hour'));

                                            // If end time is the same as start time, clear it
                                            if ($start->format('H:i') === $end->format('H:i')) {
                                                $set('end_hour', null);
                                            }
                                        }
                                    }),

                                Forms\Components\TimePicker::make('end_hour')
                                    ->label('End Time')
                                    ->required()
                                    ->seconds(false)
                                    ->displayFormat('g:i A')
                                    ->reactive()
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                $start = request()->input('start_hour');
                                                if ($start && $value) {
                                                    $startTime = \Carbon\Carbon::parse($start);
                                                    $endTime = \Carbon\Carbon::parse($value);

                                                    // Check if start and end times are exactly the same
                                                    if ($startTime->format('H:i') === $endTime->format('H:i')) {
                                                        $fail('End time cannot be the same as start time.');
                                                    }
                                                }
                                            };
                                        },
                                    ])
                                    ->helperText('Note: End time can be earlier than start time for overnight shifts.'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Shift Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('start_hour')
                    ->label('Start Time')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('g:i A'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_hour')
                    ->label('End Time')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('g:i A'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('time_range')
                    ->label('Time Range')
                    ->getStateUsing(function (Shift $record) {
                        return $record->time_range;
                    })
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(function (Shift $record) {
                        return number_format($record->duration, 1).' hours';
                    })
                    ->badge()
                    ->color(fn (Shift $record) => $record->isOvernightShift() ? 'warning' : 'success'),

                Tables\Columns\IconColumn::make('is_overnight')
                    ->label('Overnight')
                    ->getStateUsing(function (Shift $record) {
                        return $record->isOvernightShift();
                    })
                    ->boolean()
                    ->trueIcon('heroicon-o-moon')
                    ->falseIcon('heroicon-o-sun'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('overnight_shifts')
                    ->label('Overnight Shifts')
                    ->query(fn (Builder $query) => $query->whereRaw('TIME(end_hour) < TIME(start_hour)')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Shift Updated')
                            ->body('The shift has been updated successfully.')
                            ->icon('heroicon-o-check-circle')
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Shift Deleted')
                            ->body('The shift has been deleted successfully.')
                            ->icon('heroicon-o-trash')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Shifts Deleted')
                                ->body('The selected shifts have been deleted successfully.')
                                ->icon('heroicon-o-trash')
                        ),
                ]),
            ])
            ->defaultSort('start_hour');
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
            'index' => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShift::route('/create'),
            'edit' => Pages\EditShift::route('/{record}/edit'),
        ];
    }
}
