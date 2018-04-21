<?php return [
    'plugin' => [
        'name' => 'Mobile',
        'description' => 'A plugin for mobile apps.',
        'full_name' => 'Mobile'
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
];
