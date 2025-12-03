# Student Export Functionality

## Overview
The admin panel now includes comprehensive Excel export functionality for students with batch filtering capabilities.

## Features Added

### 1. Export All Students
- **Location**: Header actions in the Students list page
- **Button**: "Export All Students" (green button with download icon)
- **Functionality**: Exports all students to Excel format
- **Filename**: `all_students_YYYY-MM-DD_HH-MM-SS.xlsx`

### 2. Export by Batch
- **Location**: Header actions in the Students list page
- **Button**: "Export by Batch" (orange button with funnel icon)
- **Functionality**: Opens a modal to select a specific batch, then exports only students from that batch
- **Filename**: `{batch_name}_students_YYYY-MM-DD_HH-MM-SS.xlsx`

### 3. Export Filtered Students
- **Location**: Table header actions (appears when filters are applied)
- **Button**: "Export Filtered Students" (orange button with funnel icon)
- **Functionality**: Exports only the students that match the current table filters
- **Filename**: `filtered_students_YYYY-MM-DD_HH-MM-SS.xlsx` or `batch_{id}_students_YYYY-MM-DD_HH-MM-SS.xlsx`

### 4. Export Selected Students
- **Location**: Bulk actions (checkbox selection)
- **Button**: "Export Selected Students" (green button with download icon)
- **Functionality**: Exports only the selected students
- **Filename**: `selected_students_YYYY-MM-DD_HH-MM-SS.xlsx`

## Filters Available

### 1. Batch Filter
- Filter students by specific batch
- Searchable dropdown with all available batches
- "All Batches" option to show all students

### 2. Has Batch Filter
- **With Batch**: Shows only students assigned to a batch
- **Without Batch**: Shows only students not assigned to any batch
- **All Students**: Shows all students regardless of batch assignment

## Excel Export Details

### Columns Included
1. ID
2. Full Name
3. Username
4. Email
5. Phone
6. Father's Name
7. Mother's Name
8. Date of Birth
9. Gender
10. Nationality
11. Category
12. Qualification
13. Experience (Months)
14. Passport Number
15. Address
16. Batch
17. Course Fee (₹)
18. Fees Paid (₹)
19. Balance Due (₹)
20. Father's WhatsApp
21. Mother's WhatsApp

### Excel Features
- **Auto-sized columns** for better readability
- **Styled headers** with blue background
- **Formatted dates** in DD/MM/YYYY format
- **Currency formatting** for fee-related columns
- **Batch information** included for each student

## Additional Table Features

### Search Functionality
- Global search across all student fields
- Real-time filtering as you type

### Status Badge
- **Green**: "Fees Paid" (when balance_fees_due = 0)
- **Red**: "Pending Fees" (when balance_fees_due > 0)

### Sorting
- All columns are sortable
- Default sort by creation date (newest first)

## Usage Instructions

1. **Navigate to Students**: Go to the admin panel and click on "Students" in the navigation
2. **Apply Filters** (optional): Use the batch filter or "Has Batch" filter to narrow down students
3. **Export Options**:
   - Click "Export All Students" to export all students
   - Click "Export by Batch" to select a specific batch and export those students
   - Use the table filters, then click "Export Filtered Students" (appears when filters are active)
   - Select specific students using checkboxes, then click "Export Selected Students" in bulk actions
4. **Download**: The Excel file will automatically download to your browser's default download folder

## Technical Implementation

- **Package Used**: `maatwebsite/excel` for Excel export functionality
- **Export Class**: `App\Exports\StudentsExport`
- **Integration**: Seamlessly integrated with Filament admin panel
- **Performance**: Optimized queries with eager loading for batch relationships

## File Locations

- **Export Class**: `app/Exports/StudentsExport.php`
- **Resource**: `app/Filament/Resources/StudentResource.php`
- **List Page**: `app/Filament/Resources/StudentResource/Pages/ListStudents.php`
- **Configuration**: `config/excel.php` 