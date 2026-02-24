// Resolve alert function
function resolveAlert(alertId) {
    if (confirm('Are you sure you want to mark this alert as resolved?')) {
        fetch('../includes/resolve_alert.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'alert_id=' + alertId
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
                alert('Alert resolved successfully!');
                location.reload();
            } else {
                alert('Error resolving alert: ' + data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resolving the alert');
        });
    }
}

// Auto-refresh dashboard every 60 seconds
setTimeout(function() {
    location.reload();
}, 60000);