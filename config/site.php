<?php

return [

    'name' => env('SITE_NAME', 'Streaming Platform'),

    'motto' => env('SITE_MOTTO', 'Your Entertainment Destination'),

    'tagline' => env('SITE_TAGLINE', 'Stream Anytime, Anywhere'),

    'company_acronym' => env('COMPANY_ACRONYM', 'SP'),

    'owner' => [

        'name' => env('OWNER_NAME', 'Rashid Ibrahim'),

        'welcome_note' => env('OWNER_WELCOME_NOTE', 'Welcome to our streaming platform. Enjoy unlimited access to premium content and discover your next favorite show. Thank you for being part of our community. ✨'),

    ],

   'contact' => [

        'phone' => env('SITE_PHONE', '+255 785 847 225'),

        'phone_short' => env('SITE_PHONE_SHORT', '+255 785 847 225'),

        'email' => env('SITE_EMAIL', 'rashidibrahim@gmail.com'),

        'address' => env('SITE_ADDRESS', 'CBE POSTA , P.O. Box 619 Dar Es Salaam, Tanzania'),

        'latitude' => env('SITE_LATITUDE', '-6.144365'),

        'longitude' => env('SITE_LONGITUDE', '35.898974'),

    ],

    'opening_hours' => [

        'weekdays' => [

            'label' => 'Monday - Friday',

            'hours' => '24/7 Available',

        ],

        'saturday' => [

            'label' => 'Saturday',

            'hours' => '24/7 Available',

        ],

        'sunday' => [

            'label' => 'Sunday',

            'hours' => '24/7 Available',

        ],

    ],

    'social' => [

        'facebook' => env('SOCIAL_FACEBOOK', '#'),

        'twitter' => env('SOCIAL_TWITTER', '#'),

        'google' => env('SOCIAL_GOOGLE', '#'),

        'instagram' => env('SOCIAL_INSTAGRAM', '#'),

        'youtube' => env('SOCIAL_YOUTUBE', '#'),

        'linkedin' => env('SOCIAL_LINKEDIN', '#'),

    ],

];

