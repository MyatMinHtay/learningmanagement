

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to show toast notification
        function showToast(message, type) {
            if (!message || !message.trim()) return;
            
            // Define colors and icons based on alert type
            let backgroundColor, icon;
            switch(type) {
                case 'success':
                    backgroundColor = 'linear-gradient(to right, #00b09b, #96c93d)';
                    icon = '✅';
                    break;
                case 'danger':
                case 'error':
                    backgroundColor = 'linear-gradient(to right, #ff5f6d, #ffc371)';
                    icon = '❌';
                    break;
                case 'warning':
                    backgroundColor = 'linear-gradient(to right, #f093fb, #f5576c)';
                    icon = '⚠️';
                    break;
                case 'info':
                    backgroundColor = 'linear-gradient(to right, #4facfe, #00f2fe)';
                    icon = 'ℹ️';
                    break;
                default:
                    backgroundColor = 'linear-gradient(to right, #00b09b, #96c93d)';
                    icon = '📢';
            }
            
            Toastify({
                text: icon + ' ' + message,
                duration: 5000,
                close: true,
                gravity: "bottom",
                position: "right",
                stopOnFocus: true,
                style: {
                    background: backgroundColor,
                    borderRadius: "8px",
                    fontSize: "14px",
                    fontWeight: "500",
                    boxShadow: "0 4px 12px rgba(0,0,0,0.15)"
                },
                onClick: function(){}
            }).showToast();
        }
        
        // Check for Laravel session messages
        @if (session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        
        @if (session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
        
        @if (session('danger'))
            showToast('{{ session('danger') }}', 'danger');
        @endif
        
        @if (session('warning'))
            showToast('{{ session('warning') }}', 'warning');
        @endif
        
        @if (session('info'))
            showToast('{{ session('info') }}', 'info');
        @endif
        
        // Handle validation errors
        @if (isset($errors) && $errors->any())
            @foreach ($errors->all() as $error)
                showToast('{{ $error }}', 'error');
            @endforeach
        @endif
    });
</script>