<?php return [
    'plugin' => [
        'name' => 'Mobile User',
        'description' => 'Mobile front-end user management.'
    ],
    'settings' => [
        'name' => 'Login settings',
        'description' => 'Manage mobile login configurations.',
        'provider_label' => 'Provider',
        'provider_comment' => 'Choose the login provider to use for your mobile application.',
        'activation_page_label' => 'Activation Page Location',
        'activation_page_comment' => 'Select the page where you have added the Account component for user activation.',
    ],
    'installs' => [
        'unregistered' => 'Unregistered'
    ],
    'variants' => [
        'allow_registration_label' => 'Disable Registration?',
        'allow_registration_comment' => 'When checked, user registration for this variant through the API is disabled.',
        'registration_disabled' => 'Registration is disabled for this package.'
    ],
    'users' => [
        'mobileuser_installs_label' => 'Mobile User Installs'
    ]
];
