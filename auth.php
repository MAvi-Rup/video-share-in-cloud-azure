<?php
// auth.php
// This file runs on every request to map Azure EasyAuth user -> $_SESSION['user']

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If we already have a user in session, no need to do anything
if (!isset($_SESSION['user'])) {
    // Azure App Service (EasyAuth) puts user info into this header
    $principalHeader = $_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL'] ?? null;
    $principalId     = $_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_ID'] ?? null;

    if ($principalHeader) {
        $decoded = base64_decode($principalHeader);
        $principal = json_decode($decoded, true);

        $claims = $principal['claims'] ?? [];

        $name  = null;
        $email = null;

        foreach ($claims as $claim) {
            $typ = $claim['typ'] ?? '';
            $val = $claim['val'] ?? '';

            // Try to detect a nice display name
            if ($typ === 'name' || str_ends_with($typ, '/name')) {
                $name = $val;
            }
            // Try to detect email
            if (stripos($typ, 'email') !== false) {
                $email = $val;
            }
        }

        // Fallbacks if name not found
        if (!$name && $email) {
            $name = $email;
        }
        if (!$name) {
            $name = 'User';
        }

        // Final userId fallback
        $userId = $principalId ?: $name;

        $_SESSION['user'] = [
            'userId' => $userId,
            'name'   => $name,
            'email'  => $email,
        ];
    }
}
