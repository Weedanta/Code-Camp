<?php
// Set variabel untuk header dashboard
$current_dashboard_page = 'delete_account';
$page_title = 'Delete Account - Code Camp';

// Include header dashboard
include_once __DIR__ . '/../../includes/dashboard_header.php';
?>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar -->
        <div class="md:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Profile Akun</h2>
                <div class="space-y-2">
                    <a href="dashboard.php" class="sidebar-item block text-gray-700 font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Info Personal
                    </a>
                    <a href="change_password.php" class="sidebar-item block text-gray-700 font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7 6h-2m-6 0H4a3 3 0 01-3-3V9a3 3 0 013-3h2.25M15 7V4.5A2.5 2.5 0 0012.5 2h-1A2.5 2.5 0 009 4.5V7m6 0v3H9V7"></path>
                        </svg>
                        Password
                    </a>
                    <a href="delete_account.php" class="sidebar-item active block text-gray-700 font-medium text-red-500">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Akun
                    </a>
                </div>
            </div>
        </div>

        <!-- Delete Account Section -->
        <div class="md:w-3/4">
            <div class="bg-white rounded-lg shadow-md p-6 relative overflow-hidden">
                <div class="circle-bg"></div>

                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-800">Delete Account</h2>
                </div>
                <p class="text-gray-600 mb-6">Once you delete your account, there is no going back. Please be certain before proceeding.</p>

                <!-- Warning Section -->
                <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-6 rounded-r-lg">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h3 class="text-red-800 font-bold text-lg mb-2">⚠️ Warning: This action cannot be undone!</h3>
                            <div class="text-red-700 space-y-2">
                                <p class="font-medium">When you delete your account, all of the following will be permanently removed:</p>
                                <ul class="list-disc list-inside space-y-1 ml-4">
                                    <li>Your profile information and settings</li>
                                    <li>All your bootcamp enrollments and progress</li>
                                    <li>Your certificates and achievements</li>
                                    <li>All your personal data and activity history</li>
                                    <li>Your wishlist and saved items</li>
                                </ul>
                                <p class="font-medium mt-3">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    This action is irreversible and your data cannot be recovered.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alternative Options -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6 rounded-r-lg">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-blue-800 font-bold text-lg mb-2">💡 Consider these alternatives instead:</h3>
                            <div class="text-blue-700 space-y-3">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7 6h-2m-6 0H4a3 3 0 01-3-3V9a3 3 0 013-3h2.25M15 7V4.5A2.5 2.5 0 0012.5 2h-1A2.5 2.5 0 009 4.5V7m6 0v3H9V7"></path>
                                    </svg>
                                    <span><strong>Change your password</strong> if you're concerned about security</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span><strong>Update your profile</strong> to change your personal information</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span><strong>Contact support</strong> if you're having issues with your account</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3">Your Account Summary:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-gray-700">Account: <strong><?php echo htmlspecialchars($name); ?></strong></span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">Member since: <strong>2024</strong></span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">Status: <strong class="text-green-600">Active</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Delete Account Form -->
                <form action="../../../index.php?action=delete_account" method="post" id="deleteAccountForm" class="space-y-6">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <input type="checkbox" id="understand_permanent" name="understand_permanent" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded mt-1" required>
                            <label for="understand_permanent" class="ml-3 block text-gray-700">
                                I understand that <strong>deleting my account is permanent</strong> and cannot be undone.
                            </label>
                        </div>

                        <div class="flex items-start">
                            <input type="checkbox" id="data_loss_acknowledged" name="data_loss_acknowledged" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded mt-1" required>
                            <label for="data_loss_acknowledged" class="ml-3 block text-gray-700">
                                I acknowledge that <strong>all my data will be permanently lost</strong> and cannot be recovered.
                            </label>
                        </div>

                        <div class="flex items-start">
                            <input type="checkbox" id="confirm_delete" name="confirm_delete" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded mt-1" required>
                            <label for="confirm_delete" class="ml-3 block text-gray-700">
                                I want to <strong>permanently delete my account</strong> and all associated data.
                            </label>
                        </div>
                    </div>

                    <!-- Final Confirmation -->
                    <div class="border-2 border-red-200 rounded-lg p-4 bg-red-50">
                        <label for="confirmation_text" class="block text-gray-700 font-medium mb-2">
                            To confirm, please type <code class="bg-red-200 px-2 py-1 rounded text-red-800 font-mono">DELETE</code> in the box below:
                        </label>
                        <input type="text" id="confirmation_text" name="confirmation_text" 
                               class="w-full px-3 py-2 border border-red-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="Type DELETE to confirm"
                               required>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                        <a href="dashboard.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" id="deleteButton" 
                                class="px-6 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 opacity-50 cursor-not-allowed"
                                disabled>
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete My Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('deleteAccountForm');
        const deleteButton = document.getElementById('deleteButton');
        const checkboxes = form.querySelectorAll('input[type="checkbox"]');
        const confirmationText = document.getElementById('confirmation_text');

        function updateDeleteButton() {
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            const textConfirmed = confirmationText.value.trim().toUpperCase() === 'DELETE';
            
            if (allChecked && textConfirmed) {
                deleteButton.disabled = false;
                deleteButton.classList.remove('opacity-50', 'cursor-not-allowed');
                deleteButton.classList.add('hover:bg-red-700', 'transform', 'hover:scale-105');
            } else {
                deleteButton.disabled = true;
                deleteButton.classList.add('opacity-50', 'cursor-not-allowed');
                deleteButton.classList.remove('hover:bg-red-700', 'transform', 'hover:scale-105');
            }
        }

        // Add event listeners
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateDeleteButton);
        });

        confirmationText.addEventListener('input', updateDeleteButton);

        // Form submission confirmation
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Triple confirmation dialog
            const confirmations = [
                'Are you absolutely sure you want to delete your account?',
                'This will permanently delete ALL your data. This action CANNOT be undone. Continue?',
                'Last chance! Click OK to permanently delete your account and all data.'
            ];

            let proceed = true;
            for (let i = 0; i < confirmations.length && proceed; i++) {
                proceed = confirm(confirmations[i]);
            }

            if (proceed) {
                // Show loading state
                deleteButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Deleting Account...';
                deleteButton.disabled = true;
                
                // Submit the form
                form.submit();
            }
        });

        // Real-time validation feedback
        confirmationText.addEventListener('input', function() {
            if (this.value.trim().toUpperCase() === 'DELETE') {
                this.classList.remove('border-red-300');
                this.classList.add('border-green-500');
            } else {
                this.classList.remove('border-green-500');
                this.classList.add('border-red-300');
            }
        });
    });
</script>

</body>
</html>