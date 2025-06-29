@auth
<div class="position-relative d-inline-block">
    <a href="{{ route('notifications.index') }}" class="text-decoration-none position-relative">
        <i class="fas fa-bell fs-4 text-primary"></i>
        <span id="global-notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.6rem;">
            0
        </span>
    </a>
</div>

<script>
// Function to update global notification count
function updateGlobalNotificationCount() {
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('global-notification-badge');
            if (badge && data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.style.display = 'inline';
            } else if (badge) {
                badge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching notification count:', error);
        });
}

// Update count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateGlobalNotificationCount();
});

// Update count every 30 seconds
setInterval(updateGlobalNotificationCount, 30000);
</script>
@endauth 