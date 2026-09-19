<?php

require_once 'dbconfig.in.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: ticketsys.php');
    exit;
}

$errorMessage = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {

        $errorMessage = 'Please enter both email and password.';

    } else {

        $sql = "SELECT id, name, email, user_type
                FROM users
                WHERE email = :email
                AND password = :password
                AND user_type IN ('manager', 'customer')";

        $statement = $pdo->prepare($sql);

        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $password);

        $statement->execute();

        $user = $statement->fetch();

        if ($user) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];

            header('Location: ticketsys.php');
            exit;
        }

        $errorMessage = 'Invalid email or password.';
    }
}

$pageTitle = 'Login';

require_once 'includes/header.php';

?>

<main class="login-page">

    <section class="login-card">

        <h2>Login</h2>

        <p class="form-introduction">
            Sign in to access the SamaNova Maintenance Request System.
        </p>

        <?php if ($errorMessage !== ''): ?>

            <p class="message error-message">
                <?= escape($errorMessage) ?>
            </p>

        <?php endif; ?>

        <form class="login-form"
              action="login.php"
              method="post">

            <div class="form-group">

                <label class="form-label"
                       for="email">
                    Email:
                </label>

                <input class="form-control"
                       type="email"
                       id="email"
                       name="email"
                       placeholder="Enter your email"
                       value="<?= escape($email) ?>"
                       required
                       autofocus>

            </div>

            <div class="form-group">

                <label class="form-label"
                       for="password">
                    Password:
                </label>

                <input class="form-control"
                       type="password"
                       id="password"
                       name="password"
                       placeholder="Enter your password"
                       required>

            </div>

            <div class="form-actions">

                <button class="btn"
                        type="submit">
                    Login
                </button>

            </div>

        </form>

    </section>

</main>

<?php require_once 'includes/footer.php'; ?>