<?php

return [
    // Page
    'title' => 'Profile Settings',
    'description' => 'Manage your personal information and preferences',

    // Display Name Section
    'display_name_section' => 'Display Name',
    'display_name' => 'Display Name',
    'display_name_placeholder' => 'Enter your display name',
    'display_name_help' => 'This name will be shown to other users. Leave empty to use your username.',

    // Username Section
    'username_section' => 'Account Settings',
    'username' => 'Username',
    'username_placeholder' => 'Enter your username',
    'username_help' => 'This is your login username. It must be unique.',

    // Password Section
    'password_section' => 'Change Password',
    'current_password' => 'Current Password',
    'new_password' => 'New Password',
    'password_help' => 'Leave both fields empty if you don\'t want to change your password.',

    // Messages
    'updated' => 'Profile updated successfully.',
    'password_incorrect' => 'Current password is incorrect.',

    // Validation Messages
    'validation' => [
        'username_unique' => 'This username is already taken.',
        'username_required' => 'Username is required.',
        'display_name_max' => 'Display name cannot exceed 255 characters.',
        'current_password_required_with' => 'Current password is required to change password.',
        'new_password_required_with' => 'New password is required.',
        'new_password_different' => 'New password must be different from current password.',
        'current_password_size' => 'Invalid password format.',
        'new_password_size' => 'Invalid password format.',
    ],
];
