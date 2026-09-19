<?php

require_once __DIR__ . '/auth.php';

$pageTitle = $pageTitle ?? 'SamaNova';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?= escape($pageTitle) ?> | SamaNova
    </title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="site-header">

    <div class="header-content">

        <div class="site-brand">

            <a href="ticketsys.php">

                <img class="site-logo"
                     src="images/SN.svg"
                     alt="SamaNova Logo">

            </a>

            <div class="brand-text">

                <a class="site-name"
                   href="ticketsys.php">
                    SamaNova
                </a>

                <p class="site-description">
                    Maintenance Request System
                </p>

            </div>

        </div>

        <nav class="main-navigation"
             aria-label="Main Navigation">

            <?php if (isLoggedIn()): ?>

                <a class="nav-link"
                   href="ticketsys.php">
                    Home
                </a>

                <a class="nav-link"
                   href="ticketsys.php">
                    View Tickets
                </a>

                <?php if ($_SESSION['user_type'] === 'customer'): ?>

                    <a class="nav-link"
                       href="request.php">
                        Submit Maintenance Request
                    </a>

                <?php endif; ?>

                <span class="user-welcome">

                    Welcome,
                    <strong>
                        <?= escape($_SESSION['user_name']) ?>
                    </strong>

                    <?php if ($_SESSION['user_type'] === 'manager'): ?>

                        <span class="user-role role-manager">
                            Manager
                        </span>

                    <?php else: ?>

                        <span class="user-role role-customer">
                            Customer
                        </span>

                    <?php endif; ?>

                </span>

                <a class="btn logout-button"
                   href="logout.php">
                    Logout
                </a>

            <?php else: ?>

                <a class="btn login-button"
                   href="login.php">
                    Login
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>