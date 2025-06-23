# Job Positions Import/Export Guide

## Overview

The Job Positions resource includes **direct import and export functionality** with immediate processing - minimal background jobs and immediate feedback.

## Features

### 🔄 Import Job Positions

-   **Access**: Click "Import Job Positions" button in the Job Positions table header
-   **File Format**: CSV files with comma delimiter
-   **Max Rows**: 1,000 rows per import
-   **Processing**: **Immediate processing** with sync queue connection
-   **Options**: Toggle to update existing positions
-   **Validation**: Real-time validation with detailed error messages

### 📤 Export Job Positions

-   **Access**: Click "Export Job Positions" button in the Job Positions table header
-   **Format**: CSV export of all existing positions
-   **Processing**: **Immediate download** - no waiting, no background jobs
-   **Columns**: name, points, applies_for_tips
-   **File name**: `job_positions_export_YYYY-MM-DD_HH-mm-ss.csv`

### 📋 Download Template

-   **Access**: Click "Download Template" button in the Job Positions table header
-   **Purpose**: Get a sample CSV file with correct format and example data

## CSV Format

### Required Columns

```csv
name,points,applies_for_tips
```

### Column Specifications

| Column               | Type    | Required | Description                                    | Example Values             |
| -------------------- | ------- | -------- | ---------------------------------------------- | -------------------------- |
| **name**             | String  | Yes      | Position name (max 255 chars, must be unique)  | "Line Cook", "Server"      |
| **points**           | Numeric | Yes      | Tip points value (0-999.99, supports decimals) | 2.5, 3.0, 1.5              |
| **applies_for_tips** | Boolean | No       | Tips eligibility (defaults to true)            | true, false, 1, 0, yes, no |

### Sample Data

```csv
name,points,applies_for_tips
Line Cook,2.5,true
Server,3.0,true
Dishwasher,1.5,true
Manager,0,false
Host,1.0,true
```

## Import Options

### Update Existing Positions

-   **Default**: Disabled (prevents duplicates)
-   **When Enabled**: Updates existing positions with same name
-   **When Disabled**: Fails import for duplicate names

## Validation Rules

### Position Name

-   ✅ Required field
-   ✅ Maximum 255 characters
-   ✅ Must be unique (unless updating)

### Points Value

-   ✅ Required field
-   ✅ Must be numeric
-   ✅ Minimum value: 0
-   ✅ Maximum value: 999.99
-   ✅ Supports decimal places

### Applies for Tips

-   ✅ Must be boolean value
-   ✅ Accepts: true/false, 1/0, yes/no
-   ✅ Optional (defaults to true)

## Import Process

1. **Upload File**: Select CSV file in the import modal
2. **Map Columns**: Filament automatically maps columns by name
3. **Configure Options**: Choose whether to update existing positions
4. **Validate**: System validates all data before processing
5. **Process**: Import runs in background (queued job)
6. **Results**: Receive notification when complete

## Error Handling

### Failed Rows

-   Download CSV of failed rows after import
-   Common issues:
    -   Duplicate names (when update disabled)
    -   Invalid point values
    -   Missing required fields
    -   Invalid boolean values

### Success Notification

Shows count of:

-   Successfully imported rows
-   Failed rows (if any)

## Technical Details

### Queue Integration

-   Imports run as background jobs
-   Uses Laravel's queue system
-   Supports job batching for progress tracking

### File Storage

-   Temporary files stored securely
-   Auto-cleanup after processing
-   Maximum file size respects server limits

### Performance

-   Chunked processing for large files
-   Memory-efficient streaming
-   Progress tracking for long imports

## Best Practices

1. **Start Small**: Test with small files first
2. **Use Template**: Download and modify the template file
3. **Check Format**: Ensure CSV uses proper delimiters
4. **Validate Data**: Review data before import
5. **Backup First**: Export existing data before bulk imports
6. **Monitor Jobs**: Check queue processing if imports are slow

## Troubleshooting

### Import Not Starting

-   Check file format (must be CSV)
-   Verify file size limits
-   Ensure queue workers are running

### Validation Errors

-   Download failed rows CSV for details
-   Check column mapping
-   Verify data format matches requirements

### Performance Issues

-   Reduce file size
-   Check server resources
-   Monitor queue processing

## Support

For technical issues or questions, check the application logs or contact system administrators.
