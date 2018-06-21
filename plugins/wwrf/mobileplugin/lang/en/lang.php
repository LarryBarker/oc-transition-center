<?php return [
    'plugin' => [
        'name' => 'Mobile',
        'description' => 'A plugin for Transition Center mobile app.',
        'full_name' => 'WWRF Transition Center Mobile App Plugin'
    ],
    'permission' => [
        'access_campaigns' => 'Allow access to add/delete campaigns.',
        'access_activations' => 'Allow to view the activations.',
    ],
    'install' => [
        'id' => 'ID',
        'label' => 'Install',
        'install_id' => 'Install ID',
        'view_installs' => 'Allow Viewing Installs',
        'manufacturer' => 'Manufacturer',
        'model' => 'Model',
        'installed_on' => 'Installed On',
        'last_opened_on' => 'Last Opened On',
        'return_to_installs' => 'Back to installs list',
    ],
    'variant' => [
        'is_maintenance' => 'In Maintenance?',
        'is_maintenance_comment' => 'When enabled, users running this variant will recieve the maintenance mode message.'
    ],
    'app' => [
        'name_label' => 'Name',
        'description_label' => 'Description',
        'maintenance_message_label' => 'Maintenance Mode Message',
        'maintenance_message_comment' => 'The message to be displayed when the app is put in maintenance mode.',
        'variants_label' => 'Variants'
    ],
    'platform' => [
        'is_reserved' => 'This is a reserved keyword. To activate install :name plugin.'
    ],
    'widgets' => [
        'title_installs' => 'App Installs Overview',
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
