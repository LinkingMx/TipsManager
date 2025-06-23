<?php

namespace App\Filament\Imports;

use App\Models\JobPosition;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;

class JobPositionImporter extends Importer
{
    protected static ?string $model = JobPosition::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->label('Position Name')
                ->example('Server')
                ->guess(['position', 'job_title', 'title'])
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('points')
                ->label('Points')
                ->numeric(decimalPlaces: 2)
                ->example('2.5')
                ->guess(['point', 'point_value', 'pts'])
                ->rules(['required', 'numeric', 'min:0']),

            ImportColumn::make('applies_for_tips')
                ->label('Applies for Tips')
                ->boolean()
                ->example('true')
                ->guess(['tips', 'tip_eligible', 'eligible_for_tips', 'tips_eligible'])
                ->helperText('Enter "true", "yes", "1" for tip eligible positions, or "false", "no", "0" for non-tip eligible positions.')
                ->rules(['boolean']),
        ];
    }

    public function resolveRecord(): ?JobPosition
    {
        // Check if updateExisting option is enabled
        if ($this->options['updateExisting'] ?? false) {
            return JobPosition::firstOrNew([
                'name' => $this->data['name'],
            ]);
        }

        return new JobPosition;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make('updateExisting')
                ->label('Update existing positions')
                ->helperText('If enabled, existing job positions with the same name will be updated instead of creating duplicates.'),
        ];
    }

    public function getValidationMessages(): array
    {
        return [
            'name.required' => 'The position name field is required.',
            'name.max' => 'The position name may not be greater than 255 characters.',
            'points.required' => 'The points field is required.',
            'points.numeric' => 'The points must be a number.',
            'points.min' => 'The points must be at least 0.',
        ];
    }

    // Process imports immediately (not queued)
    public function getJobQueue(): ?string
    {
        return null; // Returns null to process synchronously
    }

    public function getJobConnection(): ?string
    {
        return 'sync'; // Force synchronous processing
    }

    protected function beforeSave(): void
    {
        // Log import activity or perform any pre-save logic if needed
    }

    protected function afterSave(): void
    {
        // Log successful import or perform any post-save logic if needed
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your job position import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
