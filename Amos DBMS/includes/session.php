<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Check if user is moderator
function isModerator() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'moderator';
}

// Check if user has admin or moderator privileges
function hasModeratorPrivileges() {
    return isAdmin() || isModerator();
}

// Get current user's ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user's role
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

// Get current user's name
function getCurrentUserName() {
    return $_SESSION['name'] ?? null;
}
 
// Check if user is banned
function isBanned() {
    return isset($_SESSION['status']) && $_SESSION['status'] === 'banned';
}

// Check if user is active
function isActive() {
    return !isset($_SESSION['status']) || $_SESSION['status'] === 'active';
}

// Require login to access page
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Require admin privileges to access page
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: unauthorized.php');
        exit();
    }
}

// Require moderator privileges to access page
function requireModeratorPrivileges() {
    if (!hasModeratorPrivileges()) {
        header('Location: unauthorized.php');
        exit();
    }
}

// Require active account to access page
function requireActiveAccount() {
    if (!isActive()) {
        header('Location: account_suspended.php');
        exit();
    }
}

// Set flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Get flash message and clear it
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Display flash message HTML
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $type = $flash['type'] === 'error' ? 'danger' : $flash['type'];
        echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$flash['message']}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
}
