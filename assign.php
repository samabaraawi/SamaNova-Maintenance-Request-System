<?php

require_once 'dbconfig.in.php';
require_once 'includes/auth.php';

requireManager();

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ticketId = filter_input(
        INPUT_POST,
        'ticket_id',
        FILTER_VALIDATE_INT
    );

} else {

    $ticketId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

$pageTitle = 'Assign Ticket';

if (!$ticketId) {

    require_once 'includes/header.php';
    ?>

    <main class="assign-page">

        <article class="message-card">

            <h1>Invalid Ticket</h1>

            <p class="message error-message">
                An invalid ticket ID was provided.
            </p>

            <div class="form-actions">

                <a class="btn reset-button"
                   href="ticketsys.php">
                    Return to Ticket List
                </a>

            </div>

        </article>

    </main>

    <?php
    require_once 'includes/footer.php';
    exit;
}

/*
 * Retrieve the selected ticket.
 */
$ticketSql = "SELECT
                  ticket.ticket_id,
                  ticket.description,
                  ticket.submitted_date,
                  ticket.status,
                  ticket.emergency_level,
                  customer.name AS customer_name
              FROM tickets AS ticket

              INNER JOIN users AS customer
                  ON ticket.customer_id = customer.id

              WHERE ticket.ticket_id = :ticketId";

$ticketStatement = $pdo->prepare($ticketSql);

$ticketStatement->bindValue(
    ':ticketId',
    $ticketId
);

$ticketStatement->execute();

$ticket = $ticketStatement->fetch();

if (!$ticket) {

    require_once 'includes/header.php';
    ?>

    <main class="assign-page">

        <article class="message-card">

            <h1>Ticket Not Found</h1>

            <p class="message error-message">
                The requested ticket does not exist.
            </p>

            <div class="form-actions">

                <a class="btn reset-button"
                   href="ticketsys.php">
                    Return to Ticket List
                </a>

            </div>

        </article>

    </main>

    <?php
    require_once 'includes/footer.php';
    exit;
}

/*
 * Do not allow an assigned or completed
 * ticket to be assigned again.
 */
if ($ticket['status'] !== 'Pending') {

    if ($ticket['status'] === 'Completed') {

        $_SESSION['flash_message'] =
            'This ticket has already been completed.';

    } else {

        $_SESSION['flash_message'] =
            'This ticket has already been assigned.';
    }

    $_SESSION['flash_type'] = 'warning';

    header(
        'Location: view.php?id=' . $ticketId
    );

    exit;
}

/*
 * Process the Assign Ticket form.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $staffId = filter_input(
        INPUT_POST,
        'staff_id',
        FILTER_VALIDATE_INT
    );

    if (!$staffId) {

        $errorMessage =
            'Please select a staff member.';

    } else {

        /*
         * Verify that the selected user
         * is actually a staff member.
         */
        $staffSql = "SELECT id
                     FROM users
                     WHERE id = :staffId
                     AND user_type = :staffType";

        $staffStatement =
            $pdo->prepare($staffSql);

        $staffStatement->bindValue(
            ':staffId',
            $staffId
        );

        $staffStatement->bindValue(
            ':staffType',
            'staff'
        );

        $staffStatement->execute();

        $validStaff = $staffStatement->fetch();

        if (!$validStaff) {

            $errorMessage =
                'The selected staff member is invalid.';

        } else {

            $updateSql = "UPDATE tickets
                          SET status = :newStatus,
                              assigned_staff_id = :staffId,
                              assigned_date = :assignedDate

                          WHERE ticket_id = :ticketId
                          AND status = :currentStatus
                          AND assigned_staff_id IS NULL";

            $updateStatement =
                $pdo->prepare($updateSql);

            $updateStatement->bindValue(
                ':newStatus',
                'Assigned'
            );

            $updateStatement->bindValue(
                ':staffId',
                $staffId
            );

            $updateStatement->bindValue(
                ':assignedDate',
                date('Y-m-d')
            );

            $updateStatement->bindValue(
                ':ticketId',
                $ticketId
            );

            $updateStatement->bindValue(
                ':currentStatus',
                'Pending'
            );

            $updateStatement->execute();

            if ($updateStatement->rowCount() === 1) {

                $_SESSION['flash_message'] =
                    'The ticket was assigned successfully.';

                $_SESSION['flash_type'] =
                    'success';

            } else {

                $_SESSION['flash_message'] =
                    'The ticket could not be assigned.';

                $_SESSION['flash_type'] =
                    'error';
            }

            header(
                'Location: view.php?id=' . $ticketId
            );

            exit;
        }
    }
}

/*
 * Generate the staff dropdown dynamically.
 */
$staffListSql = "SELECT id, name
                 FROM users
                 WHERE user_type = :staffType
                 ORDER BY name";

$staffListStatement =
    $pdo->prepare($staffListSql);

$staffListStatement->bindValue(
    ':staffType',
    'staff'
);

$staffListStatement->execute();

$staffMembers = $staffListStatement->fetchAll();

require_once 'includes/header.php';

?>

<main class="assign-page">

    <header class="page-heading">

        <h2>
            Assign Ticket
            #<?= escape($ticket['ticket_id']) ?>
        </h2>

        <p>
            Select a staff member for this request.
        </p>

    </header>

    <article class="assign-card">

        <?php if ($errorMessage !== ''): ?>

            <p class="message error-message">
                <?= escape($errorMessage) ?>
            </p>

        <?php endif; ?>

        <ul class="assign-ticket-details">

            <li class="detail-item">

                <strong>Customer:</strong>

                <span>
                    <?= escape($ticket['customer_name']) ?>
                </span>

            </li>

            <li class="detail-item">

                <strong>Issue Description:</strong>

                <span>
                    <?= escape($ticket['description']) ?>
                </span>

            </li>

            <li class="detail-item">

                <strong>Urgency Level:</strong>

                <span class="emergency-label
                             emergency-<?= escape(
                                 strtolower(
                                     $ticket['emergency_level']
                                 )
                             ) ?>">

                    <?= escape($ticket['emergency_level']) ?>

                </span>

            </li>

            <li class="detail-item">

                <strong>Date Submitted:</strong>

                <span>
                    <?= escape($ticket['submitted_date']) ?>
                </span>

            </li>

        </ul>

        <form class="assign-form"
              action="assign.php"
              method="post">

            <input type="hidden"
                   name="ticket_id"
                   value="<?= escape(
                       $ticket['ticket_id']
                   ) ?>">

            <div class="form-group">

                <label class="form-label"
                       for="staff_id">
                    Assign to Staff Member:
                </label>

                <select class="form-control"
                        id="staff_id"
                        name="staff_id"
                        required>

                    <option value="">
                        Select a staff member
                    </option>

                    <?php foreach (
                        $staffMembers as $staff
                    ): ?>

                        <option value="<?= escape(
                            $staff['id']
                        ) ?>">

                            <?= escape($staff['name']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-actions">

                <button class="btn"
                        type="submit">
                    Assign Ticket
                </button>

                <a class="btn reset-button"
                   href="ticketsys.php">
                    Cancel
                </a>

            </div>

        </form>

    </article>

</main>

<?php require_once 'includes/footer.php'; ?>