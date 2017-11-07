<?php

return [

    // No items go public until the administrator approves it
    'administrator_approval' => true,

    // Moderation email address (the address of the administrator who approves items)
    'administrator_email' => 'administrator@example.com',

    // The "from" address in the emails sent to the user
    'success_email_from' => 'email@example.com',

    // The "from" address in the notifiction emails sent to the user
    'notification_email_from' => 'notification@example.com',

    // Number of images a user could upload
    'image_limit_per_user' => 5,

    // Categories - 'en' locale
    // Add new locales by copying this list and replacing '_en' in the new array key
    // Never delete the 'en' locale, as if a list with the site's locale isn't found, this will be the default one
    'item_categories_en' => [
        '1' => 'Bags, purses, wallets',
        '2' => 'Documents',
        '3' => 'Keys',
        '4' => 'Cars, motorcycles, bicycles',
        '5' => 'Cellphones',
        '6' => 'Animals',
        '7' => 'Misc',
    ],

];