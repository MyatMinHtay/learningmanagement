# 🔔 Laravel Notification Scheduler System

## Overview

This system provides **automated deadline reminder notifications** that run reliably in the background using Laravel's task scheduling. The system has been **completely redesigned** to eliminate performance issues and ensure notifications are sent consistently.

## ✨ Key Improvements

### **Before (Old System)**
- ❌ Notifications only sent when users visited specific pages
- ❌ Heavy processing blocked page loading
- ❌ Unreliable - students could miss deadlines
- ❌ Not scalable for large user bases

### **After (New System)**
- ✅ **Reliable**: Runs every minute in the background
- ✅ **Performance**: No impact on page load times
- ✅ **Scalable**: Handles any number of users
- ✅ **Consistent**: Works 24/7 without user interaction

## 🏗️ System Architecture

```
Laravel Scheduler (every minute)
    ↓
CheckNotificationReminders Command
    ↓
Process All Active Deadline Notifications
    ↓
Send Reminder Notifications to Students
    ↓
Log Results to notification-reminders.log
```

## 🚀 Quick Start

### **1. Development/Testing (Immediate)**

For immediate testing, run the scheduler manually:

#### **Windows:**
```bash
# Start the scheduler (runs continuously)
run-scheduler.bat

# Or run once manually
php artisan schedule:run

# Or run the command directly
php artisan notifications:check-reminders
```

#### **Linux/Mac:**
```bash
# Make script executable (first time only)
chmod +x run-scheduler.sh

# Start the scheduler (runs continuously)
./run-scheduler.sh

# Or run once manually
php artisan schedule:run

# Or run the command directly
php artisan notifications:check-reminders
```

### **2. Production Deployment**

For production servers, add this to your crontab:

```bash
# Edit crontab
crontab -e

# Add this line to run Laravel scheduler every minute
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

## 🔧 How It Works

### **1. Creating Deadline Notifications**

Teachers can create deadline notifications via:
- **URL**: `/admin/notifications/create-deadline`
- **Navigation**: Admin Dashboard → Notifications → Create Deadline

### **2. Notification Processing**

The system processes notifications with these settings:
- **Frequency**: Every minute
- **Overlap Prevention**: Won't start if already running
- **Background Processing**: Doesn't block other operations
- **Logging**: All activity logged to `storage/logs/notification-reminders.log`

### **3. Smart Reminder Logic**

**Timing Examples:**
- Set reminder for "2 hours before deadline"
- System checks every minute
- Sends reminder exactly when deadline is 2 hours away
- Includes grace period to handle slight timing variations

**Urgency Levels:**
- **URGENT** 🚨: Within 1 day/2 hours/30 minutes/5 minutes
- **REMINDER** ⏰: Standard reminder timing

## 📋 Testing the System

### **1. Manual Test**
```bash
# Run the command once to see current status
php artisan notifications:check-reminders
```

### **2. Create Test Notification**
1. Go to `/admin/notifications/create-deadline`
2. Create a notification with deadline in 2 minutes
3. Set reminder for "1 minute before"
4. Run the scheduler: `run-scheduler.bat` (Windows) or `./run-scheduler.sh` (Linux/Mac)
5. Check notifications page after 1 minute

### **3. Check Logs**
```bash
# View scheduler logs
tail -f storage/logs/notification-reminders.log

# View Laravel logs
tail -f storage/logs/laravel.log
```

## 🎯 Notification Types

### **Quiz Deadline Reminders**
- **Type**: `quiz_deadline_urgent`
- **Checks**: If student completed quiz
- **Message**: Includes quiz title and course name
- **Urgency**: Based on time remaining

### **Assignment Deadline Reminders**
- **Type**: `assignment_deadline_urgent`
- **Checks**: If student submitted assignment
- **Message**: Includes course name and deadline
- **Urgency**: Based on time remaining

## 🔍 Monitoring & Debugging

### **Check Scheduler Status**
```bash
# See what's scheduled
php artisan schedule:list

# Run scheduler once with verbose output
php artisan schedule:run --verbose
```

### **View Logs**
```bash
# Real-time log monitoring
tail -f storage/logs/notification-reminders.log

# View recent entries
tail -20 storage/logs/notification-reminders.log
```

### **Common Issues & Solutions**

**Issue**: No notifications being sent
**Solution**: 
1. Check if scheduler is running
2. Verify deadline notifications exist in database
3. Ensure students are enrolled in courses
4. Check log files for errors

**Issue**: Duplicate reminders
**Solution**: System prevents duplicates automatically - check logs for confirmation

## 📊 Performance Impact

### **Resource Usage**
- **CPU**: Minimal - only runs when needed
- **Memory**: Low - processes in batches
- **Database**: Efficient queries with proper indexing
- **Network**: None - all processing local

### **Scaling Considerations**
- **Small**: 1-100 users - runs in seconds
- **Medium**: 100-1000 users - runs in under 30 seconds
- **Large**: 1000+ users - consider database optimization

## 🛡️ Security Features

- **Permission Validation**: Only course owners can create notifications
- **Data Isolation**: Students only see their own notifications
- **Input Validation**: All form data properly validated
- **SQL Injection Protection**: Using Laravel's Eloquent ORM

## 📈 Future Enhancements

- **Email Integration**: Send email notifications
- **SMS Notifications**: Mobile alerts
- **WebSocket Integration**: Real-time browser notifications
- **Advanced Scheduling**: Multiple reminders per deadline
- **Analytics Dashboard**: Notification statistics

## 🔄 Migration Notes

The old system methods have been **completely removed**:
- `NotificationController::checkAndSendReminders()` - Deleted
- Calls from CourseController, DashboardController - Removed
- Page-blocking notification checks - Eliminated

## 📞 Support

If you need help with the notification system:
1. Check the logs first: `storage/logs/notification-reminders.log`
2. Test the command manually: `php artisan notifications:check-reminders`
3. Verify your cron job is running (production)
4. Check Laravel logs for any errors

---

## 🎉 System Status: **ACTIVE** ✅

The notification scheduler is now **fully operational** and running every minute to ensure your students never miss important deadlines! 