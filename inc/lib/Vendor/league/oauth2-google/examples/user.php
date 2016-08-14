<?php

$provider = require __DIR__ . '/provider.php';

if (!empty($_SESSION['token'])) {
    $token = unserialize($_SESSION['token']);
}

if (empty($token)) {
    header('Location: /');
    exit;
}

try {

    // We got an access token, let's now get the user's details
    $userDetails = $provider->getUserDetails($token);

    // Use these details to create a new profile
    printf('Hello %s!', $userDetails->firstName);

} catch (Exception $e) {

    // Failed to get user details
    exit('Something went wrong: ' . $e->getMessage());

}

// Use this to interact with an API on the users behalf
echo $token->accessToken;

// Use this to get a new access token if the old one expires
echo $token->refreshToken;

// Number of seconds until the access token will expire, and need refreshing
echo $token->expires;
