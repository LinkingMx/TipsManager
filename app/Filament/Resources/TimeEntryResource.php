<?php

namespace App\Filament\Resources;

use App\Filament\Imports\TimeEntryImporter;
use App\Filament\Resources\TimeEntryResource\Pages;
use App\Models\JobPosition;
use App\Models\TimeEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class TimeEntryResource extends Resource
{
    protected static ?string $model = TimeEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Time Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Time Entry';

    protected static ?string $pluralModelLabel = 'Time Entries';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Time Entry Details')
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Information')
                            ->schema([
                                Forms\Components\Section::make('Location & Employee')
                                    ->schema([
                                        Forms\Components\TextInput::make('location')
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('location_code')
                                            ->label('Location Code')
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('employee_id')
                                            ->label('Employee ID')
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('employee_name')
                                            ->label('Employee Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(4)
                                    ->columnSpanFull(),

                                Forms\Components\Section::make('Job Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('job_code')
                                            ->label('Job Code')
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('job_title')
                                            ->label('Job Title')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Time & Hours')
                            ->schema([
                                Forms\Components\Section::make('Clock Times')
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('in_date')
                                            ->label('Clock In')
                                            ->required()
                                            ->columnSpan(1),

                                        Forms\Components\DateTimePicker::make('out_date')
                                            ->label('Clock Out')
                                            ->columnSpan(1),

                                        Forms\Components\Toggle::make('auto_clock_out')
                                            ->label('Auto Clock-out')
                                            ->columnSpan(2),
                                    ])
                                    ->columns(4)
                                    ->columnSpanFull(),

                                Forms\Components\Section::make('Hours Breakdown')
                                    ->schema([
                                        Forms\Components\TextInput::make('total_hours')
                                            ->label('Total Hours')
                                            ->numeric()
                                            ->step(0.01)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('payable_hours')
                                            ->label('Payable Hours')
                                            ->numeric()
                                            ->step(0.01)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('regular_hours')
                                            ->label('Regular Hours')
                                            ->numeric()
                                            ->step(0.01)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('overtime_hours')
                                            ->label('Overtime Hours')
                                            ->numeric()
                                            ->step(0.01)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('unpaid_break_time')
                                            ->label('Unpaid Break')
                                            ->numeric()
                                            ->step(0.01)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('paid_break_time')
                                            ->label('Paid Break')
                                            ->numeric()
                                            ->step(0.01)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Pay & Tips')
                            ->schema([
                                Forms\Components\Section::make('Wage Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('wage')
                                            ->label('Hourly Wage')
                                            ->numeric()
                                            ->step(0.01)
                                            ->prefix('$')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('regular_pay')
                                            ->label('Regular Pay')
                                            ->numeric()
                                            ->step(0.01)
                                            ->prefix('$')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('overtime_pay')
                                            ->label('Overtime Pay')
                                            ->numeric()
                                            ->step(0.01)
                                            ->prefix('$')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('total_pay')
                                            ->label('Total Pay')
                                            ->numeric()
                                            ->step(0.01)
                                            ->prefix('$')
                                            ->columnSpan(1),
                                    ])
                                    ->columns(4)
                                    ->columnSpanFull(),

                                Forms\Components\Section::make('Tips Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('cash_tips_declared')
                                            ->label('Cash Tips Declared')
                                            ->numeric()
                                            ->step(0.01)
                                            ->prefix('$')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('non_cash_tips')
                                            ->label('Non-Cash Tips')
                                            ->numeric()
                                            ->step(0.01)
                                            ->prefix('$')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('total_tips')
                                            ->label('Total Tips')
                                            ->numeric()
                                            ->step(0.01)
                                            ->prefix('$')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('tips_withheld')
                                            ->label('Tips Withheld')
                                            ->numeric()
                                            ->step(0.01)
                                            ->prefix('$')
                                            ->columnSpan(1),
                                    ])
                                    ->columns(4)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('position_exists')
                    ->label('Status')
                    ->getStateUsing(fn (TimeEntry $record): bool => JobPosition::where('name', $record->job_title)->exists()
                    )
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (TimeEntry $record): string => JobPosition::where('name', $record->job_title)->exists()
                            ? 'Position exists in Job Positions catalog'
                            : 'Position not found in Job Positions catalog'
                    )
                    ->sortable(false)
                    ->width('60px'),

                Tables\Columns\TextColumn::make('employee_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('job_title')
                    ->label('Position')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('in_date')
                    ->label('Clock In')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('out_date')
                    ->label('Clock Out')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Total Hours')
                    ->numeric(2)
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total_pay')
                    ->label('Total Pay')
                    ->money('USD')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\IconColumn::make('auto_clock_out')
                    ->label('Auto Clock-out')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('position_exists')
                    ->label('Position Status')
                    ->placeholder('All Positions')
                    ->trueLabel('✅ Valid Positions')
                    ->falseLabel('❌ Missing Positions')
                    ->queries(
                        true: fn (Builder $query) => $query->whereExists(function ($subQuery) {
                            $subQuery->select(DB::raw(1))
                                ->from('job_positions')
                                ->whereColumn('job_positions.name', 'time_entries.job_title');
                        }),
                        false: fn (Builder $query) => $query->whereNotExists(function ($subQuery) {
                            $subQuery->select(DB::raw(1))
                                ->from('job_positions')
                                ->whereColumn('job_positions.name', 'time_entries.job_title');
                        }),
                        blank: fn (Builder $query) => $query,
                    ),

                Tables\Filters\SelectFilter::make('job_title')
                    ->label('Position')
                    ->options(function () {
                        return TimeEntry::distinct()
                            ->pluck('job_title', 'job_title')
                            ->filter()
                            ->toArray();
                    }),

                Tables\Filters\SelectFilter::make('location')
                    ->label('Location')
                    ->options(function () {
                        return TimeEntry::distinct()
                            ->pluck('location', 'location')
                            ->filter()
                            ->toArray();
                    }),

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
                                fn (Builder $query, $date): Builder => $query->whereDate('in_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('in_date', '<=', $date),
                            );
                    }),

                Tables\Filters\TernaryFilter::make('auto_clock_out')
                    ->label('Auto Clock-out')
                    ->boolean()
                    ->trueLabel('Auto clock-out entries')
                    ->falseLabel('Manual clock-out entries'),
            ])
            ->headerActions([
                // Import Time Entries
                ImportAction::make('import')
                    ->label('Import Time Entries')
                    ->importer(TimeEntryImporter::class)
                    ->color('success')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalHeading('Import Time Entries')
                    ->modalDescription('Upload a CSV file to import time entries. You can choose to update existing records or skip them.')
                    ->modalSubmitActionLabel('Import')
                    ->modalWidth('lg'),

                // Export Time Entries to CSV (immediate, not queued)
                Tables\Actions\Action::make('export')
                    ->label('Export Time Entries')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function () {
                        $timeEntries = TimeEntry::all();

                        if ($timeEntries->isEmpty()) {
                            Notification::make()
                                ->warning()
                                ->title('No Data to Export')
                                ->body('There are no time entries to export.')
                                ->send();

                            return;
                        }

                        $csvData = [];
                        $csvData[] = [
                            'external_id',
                            'employee_name',
                            'employee_external_id',
                            'job_title',
                            'location',
                            'location_code',
                            'department',
                            'in_date',
                            'out_date',
                            'total_hours',
                            'regular_hours',
                            'overtime_hours',
                            'hourly_rate',
                            'overtime_rate',
                            'total_pay',
                            'regular_pay',
                            'overtime_pay',
                            'auto_clock_out',
                            'notes',
                        ];

                        foreach ($timeEntries as $entry) {
                            $csvData[] = [
                                $entry->external_id,
                                $entry->employee_name,
                                $entry->employee_external_id,
                                $entry->job_title,
                                $entry->location,
                                $entry->location_code,
                                $entry->department,
                                $entry->in_date?->format('Y-m-d H:i:s'),
                                $entry->out_date?->format('Y-m-d H:i:s'),
                                $entry->total_hours,
                                $entry->regular_hours,
                                $entry->overtime_hours,
                                $entry->hourly_rate,
                                $entry->overtime_rate,
                                $entry->total_pay,
                                $entry->regular_pay,
                                $entry->overtime_pay,
                                $entry->auto_clock_out ? '1' : '0',
                                $entry->notes,
                            ];
                        }

                        $filename = 'time_entries_export_'.now()->format('Y_m_d_H_i_s').'.csv';
                        $headers = [
                            'Content-Type' => 'text/csv',
                            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                        ];

                        $callback = function () use ($csvData) {
                            $file = fopen('php://output', 'w');
                            foreach ($csvData as $row) {
                                fputcsv($file, $row);
                            }
                            fclose($file);
                        };

                        Notification::make()
                            ->success()
                            ->title('Export Completed')
                            ->body("Exported {$timeEntries->count()} time entries successfully!")
                            ->send();

                        return Response::stream($callback, 200, $headers);
                    }),

                // Download CSV Template
                Tables\Actions\Action::make('download_template')
                    ->label('Download Template')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function () {
                        $templateData = [];
                        $templateData[] = [
                            'external_id',
                            'employee_name',
                            'employee_external_id',
                            'job_title',
                            'location',
                            'location_code',
                            'department',
                            'in_date',
                            'out_date',
                            'total_hours',
                            'regular_hours',
                            'overtime_hours',
                            'hourly_rate',
                            'overtime_rate',
                            'total_pay',
                            'regular_pay',
                            'overtime_pay',
                            'auto_clock_out',
                            'notes',
                        ];

                        // Add sample data row
                        $templateData[] = [
                            'TE001',
                            'John Doe',
                            'EMP001',
                            'Server',
                            'Main Restaurant',
                            'LOC001',
                            'Front of House',
                            '2025-01-15 09:00:00',
                            '2025-01-15 17:00:00',
                            '8.00',
                            '8.00',
                            '0.00',
                            '15.50',
                            '23.25',
                            '124.00',
                            '124.00',
                            '0.00',
                            '0',
                            'Regular shift',
                        ];

                        $filename = 'time_entries_template.csv';
                        $headers = [
                            'Content-Type' => 'text/csv',
                            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                        ];

                        $callback = function () use ($templateData) {
                            $file = fopen('php://output', 'w');
                            foreach ($templateData as $row) {
                                fputcsv($file, $row);
                            }
                            fclose($file);
                        };

                        Notification::make()
                            ->success()
                            ->title('Template Downloaded')
                            ->body('Time entries template downloaded successfully!')
                            ->send();

                        return Response::stream($callback, 200, $headers);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Time Entry Deleted Successfully')
                            ->body('The time entry has been removed from the system.')
                            ->icon('heroicon-o-trash')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Time Entries Deleted Successfully')
                                ->body('The selected time entries have been removed from the system.')
                                ->icon('heroicon-o-trash')
                        ),
                ]),
            ])
            ->defaultSort('in_date', 'desc')
            ->emptyStateHeading('No time entries found')
            ->emptyStateDescription('Import time entries or create them manually.')
            ->emptyStateIcon('heroicon-o-clock');
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
            'index' => Pages\ListTimeEntries::route('/'),
            'create' => Pages\CreateTimeEntry::route('/create'),
            'edit' => Pages\EditTimeEntry::route('/{record}/edit'),
        ];
    }
}
