# Toastify Notifications Usage Guide

This project now uses Toastify library for all notifications instead of Bootstrap alerts.

## Automatic Session Notifications

The `<x-toastify-notifications />` component automatically handles Laravel session messages:

- `session('success')` - Green toast with ✅ icon
- `session('error')` - Red toast with ❌ icon  
- `session('danger')` - Red toast with ❌ icon
- `session('warning')` - Pink toast with ⚠️ icon
- `session('info')` - Blue toast with ℹ️ icon
- Validation errors - Red toasts with ❌ icon

## Manual Notifications

You can still use the `<x-alert>` component for manual notifications:

```blade
<x-alert type="success">Operation completed successfully!</x-alert>
<x-alert type="error">Something went wrong!</x-alert>
<x-alert type="warning">Please check your input!</x-alert>
<x-alert type="info">Here's some information!</x-alert>
```

## Controller Usage

In your controllers, continue using Laravel's session flash messages:

```php
// Success message
return redirect()->back()->with('success', 'Data saved successfully!');

// Error message
return redirect()->back()->with('error', 'Failed to save data!');

// Warning message
return redirect()->back()->with('warning', 'Please review your input!');

// Info message
return redirect()->back()->with('info', 'Additional information available!');
```

## Features

- **Auto-dismiss**: Toasts automatically disappear after 5 seconds
- **Manual close**: Users can click the X button to close
- **Responsive**: Works on all screen sizes
- **Styled**: Beautiful gradient backgrounds with icons
- **Position**: Appears in top-right corner
- **Pause on hover**: Stops auto-dismiss when user hovers over toast

## Setup

The Toastify library is loaded via CDN automatically when using the components. No additional setup required.

## Migration Notes

- All Bootstrap alert classes have been removed
- Session handling remains the same in controllers
- Visual appearance is now consistent across the application
- Better user experience with non-intrusive notifications