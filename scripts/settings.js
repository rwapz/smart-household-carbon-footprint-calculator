document.addEventListener('DOMContentLoaded', function() {
    initPasswordToggles();
    initPasswordStrength();
    initModals();
    initFormHandlers();
});
function initPasswordToggles() {
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const wrapper = this.closest('.password-input-wrapper');
            if (!wrapper) return;
            const input = wrapper.querySelector('input');
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? '🙈' : '👁️';
        });
    });
}
function initPasswordStrength() {
    const newPasswordInput = document.getElementById('new_password');
    if (!newPasswordInput) return;
    newPasswordInput.addEventListener('input', function() {
        updatePasswordStrength(this.value);
    });
}
function calculatePasswordStrength(password) {
    let strength = 0;
    // Check minimum length
    if (password.length >= 6) strength += 20;
    if (password.length >= 8) strength += 10;
    if (password.length >= 12) strength += 10;
    // Check for uppercase
    if (/[A-Z]/.test(password)) strength += 15;
    // Check for lowercase
    if (/[a-z]/.test(password)) strength += 15;
    // Check for numbers
    if (/[0-9]/.test(password)) strength += 15;
    // Check for special characters
    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength += 15;
    return Math.min(strength, 100);
}
function getStrengthLevel(strength) {
    if (strength < 20) return 'Weak';
    if (strength < 40) return 'Fair';
    if (strength < 60) return 'Good';
    if (strength < 80) return 'Strong';
    return 'Very Strong';
}
function getStrengthColor(strength) {
    if (strength < 20) return '#ef4444'; // red
    if (strength < 40) return '#f97316'; // orange
    if (strength < 60) return '#eab308'; // yellow
    if (strength < 80) return '#3b82f6'; // blue
    return '#10b981'; // green
}
function updatePasswordStrength(password) {
    const strengthContainer = document.querySelector('.password-strength');
    if (!strengthContainer) return;
    const fill = strengthContainer.querySelector('.strength-fill');
    const text = strengthContainer.querySelector('.strength-text');
    if (!fill || !text) return;
    const strength = calculatePasswordStrength(password);
    const level = getStrengthLevel(strength);
    const color = getStrengthColor(strength);
    fill.style.width = strength + '%';
    fill.style.backgroundColor = color;
    text.innerHTML = `Strength: <strong>${level}</strong>`;
}
function initModals() {
    // Delete account confirmation
    const deleteBtn = document.getElementById('delete-account-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showModal('deleteModal');
        });
    }
    // Clear history confirmation
    const clearBtn = document.getElementById('clear-history-btn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showModal('historyModal');
        });
    }
    // Close buttons on modals
    document.querySelectorAll('.modal').forEach(modal => {
        // Close on background click
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAllModals();
            }
        });
    });
    // Confirm buttons
    document.getElementById('confirm-household')?.addEventListener('click', confirmHouseholdChange);
    document.getElementById('confirm-delete')?.addEventListener('click', confirmDeleteAccount);
    document.getElementById('confirm-history')?.addEventListener('click', confirmClearHistory);
}
function showModal(modalId) {
    closeAllModals();
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}
function closeAllModals() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.classList.remove('active');
    });
}
function confirmHouseholdChange() {
    closeAllModals();
    const householdId = document.getElementById('household_id').value;
    if (!householdId) {
        showMessage('Please select a household', 'error', 'change-household-form');
        return;
    }
    // AJAX submission
    const formData = new FormData();
    formData.append('action', 'update_household');
    formData.append('household_id', householdId);
    fetch('settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Household updated successfully!', 'success', 'change-household-form');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showMessage(data.message || 'Failed to update household', 'error', 'change-household-form');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred', 'error', 'change-household-form');
    });
}
function confirmDeleteAccount() {
    closeAllModals();
    const usernameInput = document.getElementById('delete-username');
    const username = usernameInput?.value || '';
    if (!username) {
        showMessage('Please confirm by typing your username', 'error', 'account-info');
        return;
    }
    // AJAX deletion
    const formData = new FormData();
    formData.append('action', 'delete_account');
    formData.append('username_confirmation', username);
    fetch('settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Account deleted successfully. Redirecting...', 'success', 'account-info');
            setTimeout(() => {
                window.location.href = 'logout.php';
            }, 2000);
        } else {
            showMessage(data.message || 'Failed to delete account', 'error', 'account-info');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred', 'error', 'account-info');
    });
}
function confirmClearHistory() {
    closeAllModals();
    // AJAX history clear
    const formData = new FormData();
    formData.append('action', 'clear_history');
    fetch('settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Activity history cleared successfully', 'success', 'account-info');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showMessage(data.message || 'Failed to clear history', 'error', 'account-info');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred', 'error', 'account-info');
    });
}
function initFormHandlers() {
    // Username change
    const usernameForm = document.getElementById('change-username-form');
    if (usernameForm) {
        usernameForm.addEventListener('submit', handleUsernameChange);
    }
    // Password change
    const passwordForm = document.getElementById('change-password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', handlePasswordChange);
    }
    // Household change - show modal on submit
    const householdForm = document.getElementById('change-household-form');
    if (householdForm) {
        householdForm.addEventListener('submit', function(e) {
            e.preventDefault();
            showModal('householdModal');
        });
    }
}
function handleUsernameChange(e) {
    e.preventDefault();
    const newUsername = document.getElementById('new_username').value.trim();
    // Validation
    if (!newUsername || newUsername.length < 3) {
        showMessage('Username must be at least 3 characters', 'error', 'change-username-form');
        return;
    }
    // AJAX submission
    const formData = new FormData();
    formData.append('action', 'update_username');
    formData.append('new_username', newUsername);
    fetch('settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Username updated successfully!', 'success', 'change-username-form');
            document.getElementById('new_username').value = '';
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showMessage(data.message || 'Failed to update username', 'error', 'change-username-form');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred', 'error', 'change-username-form');
    });
}
function handlePasswordChange(e) {
    e.preventDefault();
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_new_password').value;
    // Validation
    if (!currentPassword) {
        showMessage('Current password is required', 'error', 'change-password-form');
        return;
    }
    if (!newPassword || newPassword.length < 6) {
        showMessage('New password must be at least 6 characters', 'error', 'change-password-form');
        return;
    }
    if (newPassword !== confirmPassword) {
        showMessage('Passwords do not match', 'error', 'change-password-form');
        return;
    }
    if (currentPassword === newPassword) {
        showMessage('New password must be different from current password', 'error', 'change-password-form');
        return;
    }
    // AJAX submission
    const formData = new FormData();
    formData.append('action', 'update_password');
    formData.append('current_password', currentPassword);
    formData.append('new_password', newPassword);
    fetch('settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Password updated successfully!', 'success', 'change-password-form');
            document.getElementById('change-password-form').reset();
            document.querySelector('.strength-fill').style.width = '0%';
            document.querySelector('.strength-text').textContent = 'Strength: Weak';
        } else {
            showMessage(data.message || 'Failed to update password', 'error', 'change-password-form');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred', 'error', 'change-password-form');
    });
}
function showMessage(message, type, formId) {
    // Find the form or section
    let container = document.getElementById(formId);
    if (!container) {
        container = document.querySelector('.settings-section');
    }
    if (!container) return;
    let msgElement = container.querySelector('.message');
    if (!msgElement) {
        msgElement = document.createElement('div');
        msgElement.className = 'message';
        // Insert at the end of the form or section
        container.appendChild(msgElement);
    }
    msgElement.textContent = message;
    msgElement.className = `message ${type}`;
    msgElement.style.display = 'block';
    // Scroll to message
    msgElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    // Auto-hide after 5 seconds if success
    if (type === 'success') {
        setTimeout(() => {
            msgElement.style.display = 'none';
        }, 5000);
    }
}
