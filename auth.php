<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function escape(?string $value): string
{
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}

function isLoggedIn(): bool
{
    return isset(
        $_SESSION['user_id'],
        $_SESSION['user_name'],
        $_SESSION['user_type']
    );
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireManager(): void
{
    requireLogin();

    if ($_SESSION['user_type'] !== 'manager') {
        header('Location: ticketsys.php');
        exit;
    }
}

function requireCustomer(): void
{
    requireLogin();

    if ($_SESSION['user_type'] !== 'customer') {
        header('Location: ticketsys.php');
        exit;
    }
}

?>