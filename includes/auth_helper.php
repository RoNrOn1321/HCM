<?php
/**
 * Authentication Helper Functions
 * Provides utility functions for API-based authentication
 */

/**
 * Check if user is authenticated via session
 */
function isAuthenticated() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true
           && isset($_SESSION['access_token']);
}

/**
 * Get current user data from session
 */
function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'employee_email' => $_SESSION['employee_email'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'first_name' => $_SESSION['first_name'] ?? null,
        'last_name' => $_SESSION['last_name'] ?? null,
        'access_token' => $_SESSION['access_token'] ?? null,
        'refresh_token' => $_SESSION['refresh_token'] ?? null
    ];
}

/**
 * Validate token with API and refresh if needed
 */
function validateAndRefreshToken() {
    if (!isAuthenticated()) {
        return false;
    }

    // Make API call to validate token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/HCM/api/auth.php/validate");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['access_token']
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        return true;
    }

    // Token is invalid, try to refresh
    if (isset($_SESSION['refresh_token'])) {
        return refreshAccessToken();
    }

    return false;
}

/**
 * Refresh access token using refresh token
 */
function refreshAccessToken() {
    if (!isset($_SESSION['refresh_token'])) {
        return false;
    }

    $data = ['refresh_token' => $_SESSION['refresh_token']];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/HCM/api/auth.php/refresh");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        // Extract JSON from response (in case there are PHP warnings before JSON)
        $jsonStart = strpos($response, '{');
        if ($jsonStart !== false) {
            $jsonResponse = substr($response, $jsonStart);
            $responseData = json_decode($jsonResponse, true);
        } else {
            $responseData = json_decode($response, true);
        }

        if ($responseData && isset($responseData['success']) && $responseData['success']) {
            // Update session with new token
            $_SESSION['access_token'] = $responseData['data']['access_token'];
            return true;
        }
    }

    return false;
}

/**
 * Require authentication - redirect to login if not authenticated
 */
function requireAuth() {
    session_start();

    if (!isAuthenticated() || !validateAndRefreshToken()) {
        // Clear invalid session
        session_destroy();
        header('Location: login.php');
        exit();
    }
}

/**
 * Check if user has specific role
 */
function hasRole($role) {
    $user = getCurrentUser();
    return $user && $user['role'] === $role;
}

/**
 * Check if user has any of the specified roles
 */
function hasAnyRole($roles) {
    $user = getCurrentUser();
    return $user && in_array($user['role'], $roles);
}

/**
 * Make authenticated API request
 */
function makeAuthenticatedRequest($url, $method = 'GET', $data = null) {
    if (!isAuthenticated()) {
        return false;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['access_token'],
        'Content-Type: application/json'
    ]);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return false;
    }

    // Extract JSON from response (in case there are PHP warnings before JSON)
    $jsonStart = strpos($response, '{');
    if ($jsonStart !== false) {
        $jsonResponse = substr($response, $jsonStart);
        $responseData = json_decode($jsonResponse, true);
    } else {
        $responseData = json_decode($response, true);
    }

    return [
        'status_code' => $httpCode,
        'data' => $responseData
    ];
}
?>