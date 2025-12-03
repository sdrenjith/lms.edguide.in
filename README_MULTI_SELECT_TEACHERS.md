# Custom Multi-Select Teachers Component for Filament

This document describes the custom multi-select teachers component that has been implemented to replace the standard Filament Select component for teacher assignments.

## Overview

The custom component provides a click-to-select/deselect interface for teachers without requiring Ctrl+click functionality. It integrates seamlessly with Filament forms, Livewire, and validation.

## Features

- **Click-to-Toggle**: Single click to select/deselect teachers (no Ctrl key required)
- **Visual Feedback**: Selected teachers are highlighted with blue/amber background
- **Filament Integration**: Fully integrated with Filament forms and validation
- **Dark Mode Support**: Automatically adapts to Filament's dark mode theme
- **Responsive Design**: Adapts to different screen sizes
- **Custom Scrollbar**: Styled scrollbar for better user experience

## Files Created/Modified

### 1. Custom Field Component
**File**: `app/Forms/Components/MultiSelectTeachers.php`
- Extends Filament's `Field` class
- Handles state management and teacher data retrieval
- Configurable visible options count

### 2. Blade Template
**File**: `resources/views/filament/forms/components/multi-select-teachers.blade.php`
- Custom HTML structure with Alpine.js integration
- Click-to-toggle functionality
- Responsive design with dark mode support

### 3. Subject Resource
**File**: `app/Filament/Resources/SubjectResource.php`
- Updated form schema to use the new component
- Fixed course relationship issues

### 4. Resource Pages
**Files**: 
- `app/Filament/Resources/SubjectResource/Pages/CreateSubject.php`
- `app/Filament/Resources/SubjectResource/Pages/EditSubject.php`
- Added methods to handle teacher relationship syncing

### 5. Subject Model
**File**: `app/Models/Subject.php`
- Added `teachers` to fillable array
- Many-to-many relationship with User model already exists

## Usage

### Basic Implementation

```php
use App\Forms\Components\MultiSelectTeachers;

// In your form schema
MultiSelectTeachers::make('teachers')
    ->label('Assign Teachers')
    ->helperText('Click to select/deselect teachers')
    ->columnSpanFull()
```

### Configuration Options

```php
MultiSelectTeachers::make('teachers')
    ->label('Assign Teachers')
    ->helperText('Click to select/deselect teachers')
    ->visibleOptions(8) // Show 8 options at once
    ->columnSpanFull()
```

## Technical Details

### State Management
- Uses Alpine.js `@entangle()` for Livewire state synchronization
- Automatically handles form validation
- Supports both creating and editing records

### Database Integration
- Many-to-many relationship via `subject_teacher` pivot table
- Automatic syncing of teacher relationships on create/update
- Pre-loads existing selections when editing

### Styling
- Follows Filament's design system
- Responsive design with proper spacing
- Custom scrollbar styling
- Dark mode support with CSS variables

## Database Requirements

The component requires the following database structure:

### Pivot Table: `subject_teacher`
```sql
CREATE TABLE subject_teacher (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id BIGINT UNSIGNED NOT NULL,
    teacher_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_subject_teacher (subject_id, teacher_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### User Model Requirements
- Users must have a `role` field
- Teachers are identified by `role = 'teacher'`

## Browser Compatibility

- Modern browsers with ES6 support
- Alpine.js 3.x required
- Filament 3.x required

## Troubleshooting

### Common Issues

1. **Component not loading**: Ensure the component class is properly autoloaded
2. **Teachers not saving**: Check that the `teachers` field is in the model's fillable array
3. **Styling issues**: Verify that Tailwind CSS is properly loaded
4. **Alpine.js errors**: Ensure Alpine.js is included in your Filament panel

### Debug Mode

To debug the component, you can add logging to the Alpine.js data:

```javascript
x-data="{
    selectedValues: @entangle($getStatePath()).defer,
    teachers: @js($getTeachers()),
    toggleSelection(teacherId) {
        console.log('Toggling teacher:', teacherId);
        // ... rest of the logic
    }
}"
```

## Future Enhancements

Potential improvements for future versions:

1. **Search functionality**: Add search/filter capability for large teacher lists
2. **Grouping**: Group teachers by department or subject area
3. **Bulk operations**: Select/deselect all teachers at once
4. **Keyboard navigation**: Arrow key navigation support
5. **Drag and drop**: Reorder selected teachers

## Support

For issues or questions regarding this component, please check:

1. Filament documentation: https://filamentphp.com/docs
2. Alpine.js documentation: https://alpinejs.dev/
3. Laravel relationships: https://laravel.com/docs/eloquent-relationships
