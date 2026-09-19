<?php

require_once 'dbconfig.in.php';
require_once 'includes/auth.php';

requireCustomer();

$pageTitle = 'Submit Maintenance Request';

$errors = [];

$contactEmail = $_SESSION['user_email'];
$location = '';
$description = '';
$emergencyLevel = 'Low';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $contactEmail = trim($_POST['contact_email'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $emergencyLevel = $_POST['emergency_level'] ?? '';

    if (
        $contactEmail === ''
        || !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)
    ) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($location === '') {
        $errors[] = 'Please enter the maintenance location.';
    }

    if ($description === '') {
        $errors[] = 'Please enter the issue description.';
    }

    $allowedLevels = ['Low', 'Medium', 'High'];

    if (!in_array($emergencyLevel, $allowedLevels, true)) {
        $errors[] = 'Please select a valid emergency level.';
    }

    $imageUploaded = isset($_FILES['ticket_image'])
        && $_FILES['ticket_image']['error'] !== UPLOAD_ERR_NO_FILE;

    if ($imageUploaded) {

        if ($_FILES['ticket_image']['error'] !== UPLOAD_ERR_OK) {

            $errors[] = 'An error occurred while uploading the image.';

        } else {

            $imageExtension = strtolower(
                pathinfo(
                    $_FILES['ticket_image']['name'],
                    PATHINFO_EXTENSION
                )
            );

            if (
                $_FILES['ticket_image']['type'] !== 'image/jpeg'
                || !in_array(
                    $imageExtension,
                    ['jpg', 'jpeg'],
                    true
                )
            ) {
                $errors[] = 'Only JPEG images are accepted.';
            }
        }
    }

    if (empty($errors)) {

        $insertStatement = $pdo->prepare(
            'INSERT INTO tickets (
                customer_id,
                contact_email,
                location,
                description,
                submitted_date,
                status,
                emergency_level,
                assigned_date,
                assigned_staff_id,
                image_name
            )
            VALUES (
                :customerId,
                :contactEmail,
                :location,
                :description,
                :submittedDate,
                :status,
                :emergencyLevel,
                NULL,
                NULL,
                NULL
            )'
        );

        $insertStatement->execute([
            ':customerId' => $_SESSION['user_id'],
            ':contactEmail' => $contactEmail,
            ':location' => $location,
            ':description' => $description,
            ':submittedDate' => date('Y-m-d'),
            ':status' => 'Pending',
            ':emergencyLevel' => $emergencyLevel
        ]);

        $ticketId = (int) $pdo->lastInsertId();

        if ($imageUploaded) {

            $imageName = $ticketId . '.jpeg';

            $imageDestination =
                __DIR__ . '/images/' . $imageName;

            if (
                move_uploaded_file(
                    $_FILES['ticket_image']['tmp_name'],
                    $imageDestination
                )
            ) {

                $updateStatement = $pdo->prepare(
                    'UPDATE tickets
                     SET image_name = :imageName
                     WHERE ticket_id = :ticketId'
                );

                $updateStatement->execute([
                    ':imageName' => $imageName,
                    ':ticketId' => $ticketId
                ]);

            } else {

                $deleteStatement = $pdo->prepare(
                    'DELETE FROM tickets
                     WHERE ticket_id = :ticketId'
                );

                $deleteStatement->execute([
                    ':ticketId' => $ticketId
                ]);

                $errors[] = 'The image could not be uploaded.';
            }
        }

        if (empty($errors)) {

            $_SESSION['confirmation_ticket_id'] = $ticketId;

            header(
                'Location: confirm.php?id=' . $ticketId
            );

            exit;
        }
    }
}

require_once 'includes/header.php';

?>

<main class="request-page">

    <section class="page-heading">

        <h1>Submit Maintenance Request</h1>

        <p>
            Enter the maintenance request information below.
        </p>

    </section>

    <section class="request-card">

        <?php if (!empty($errors)): ?>

            <div class="message error-message error-list">

                <strong>Please correct the following:</strong>

                <ul>

                    <?php foreach ($errors as $error): ?>

                        <li>
                            <?= escape($error) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>

        <form class="request-form"
              action="request.php"
              method="post"
              enctype="multipart/form-data">

            <div class="request-fields">

                <div class="form-group">

                    <label class="form-label"
                           for="customer_name">
                        Customer Name:
                    </label>

                    <input class="form-control disabled-control"
                           type="text"
                           id="customer_name"
                           value="<?= escape(
                               $_SESSION['user_name']
                           ) ?>"
                           disabled>

                </div>

                <div class="form-group">

                    <label class="form-label"
                           for="contact_email">
                        Contact Email:
                    </label>

                    <input class="form-control"
                           type="email"
                           id="contact_email"
                           name="contact_email"
                           value="<?= escape($contactEmail) ?>"
                           required>

                </div>

                <div class="form-group full-width-field">

                    <label class="form-label"
                           for="location">
                        Location:
                    </label>

                    <input class="form-control"
                           type="text"
                           id="location"
                           name="location"
                           value="<?= escape($location) ?>"
                           required>

                </div>

                <div class="form-group full-width-field">

                    <label class="form-label"
                           for="description">
                        Issue Description:
                    </label>

                    <textarea class="form-control"
                              id="description"
                              name="description"
                              rows="6"
                              required><?= escape(
                                  $description
                              ) ?></textarea>

                </div>

                <div class="form-group">

                    <label class="form-label"
                           for="emergency_level">
                        Emergency Level:
                    </label>

                    <select class="form-control"
                            id="emergency_level"
                            name="emergency_level"
                            required>

                        <?php foreach (
                            ['Low', 'Medium', 'High'] as $level
                        ): ?>

                            <option
                                value="<?= escape($level) ?>"
                                <?= $emergencyLevel === $level
                                    ? 'selected'
                                    : '' ?>>

                                <?= escape($level) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label class="form-label"
                           for="ticket_image">
                        Upload Photo (JPEG only):
                    </label>

                    <input class="form-control file-control"
                           type="file"
                           id="ticket_image"
                           name="ticket_image"
                           accept=".jpg,.jpeg,image/jpeg">

                </div>

            </div>

            <div class="form-actions request-actions">

                <button class="btn"
                        type="submit">
                    Submit Request
                </button>

                <a class="btn reset-button"
                   href="ticketsys.php">
                    Cancel
                </a>

            </div>

        </form>

    </section>

</main>

<?php require_once 'includes/footer.php'; ?>