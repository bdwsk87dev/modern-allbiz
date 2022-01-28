<?php
return [
    //Environment=> test/production
    'env' => 'mainsettings',
    'mainsettings' => [
        'developerToken' => "AIzaSyBhDN9QgTsGzBGd2dziDQrCjudfYL4pjFs",
        'clientCustomerId' => "",
        'clientId' => "810250485083-gdjfromr1lnu6u9e5lha60rsp4ka0gcp.apps.googleusercontent.com",   // Id приложения in adw
        'clientSecret' => "wNb6ypQp5Wu2_CQcvlpJX7Os", // Secret приложения in adw
        'refreshToken' => "",
        'userAgent' => "" // Random
    ],
    'oAuth2' => [
        'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
        // 'redirectUri' => 'urn:ietf:wg:oauth:2.0:oob',
        'redirectUri' => 'http://localhost:8000/',
        'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
        'scope' => 'https://www.googleapis.com/auth/adwords'
    ]
];