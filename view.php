<?php

require_once 'dbconfig.in.php';
require_once 'includes/auth.php';
require_once 'classes/Ticket.php';

requireLogin();

$ticketId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

$pageTitle = 'View Ticket';

if (!$ticketId) {

    require_once 'includes/header.php';
    ?>

    <main class="ticket-view-page">

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

$sql = "SELECT
            ticket.ticket_id,
            customer.name AS customer_name,
            ticket.contact_email,
            ticket.location,
            ticket.description,
            ticket.submitted_date,
            ticket.status,
            ticket.emergency_level,
            ticket.assigned_date,
            staff.name AS assigned_staff,
            ticket.image_name
        FROM tickets AS ticket

        INNER JOIN users AS customer
            ON ticket.customer_id = customer.id

        LEFT JOIN users AS staff
            ON ticket.assigned_staff_id = staff.id

        WHERE ticket.ticket_id = :ticketId";

$parameters = [
    ':ticketId' => $ticketId
];

/*
 * A customer may only view a ticket
 * that belongs to that customer.
 */
if ($_SESSION['user_type'] === 'customer') {

    $sql .= " AND ticket.customer_id = :customerId";

    $parameters[':customerId'] =
        $_SESSION['user_id'];
}

$statement = $pdo->prepare($sql);

foreach ($parameters as $name => $value) {
    $statement->bindValue($name, $value);
}

$statement->execute();

$row = $statement->fetch();

if (!$row) {

    require_once 'includes/header.php';
    ?>

    <main class="ticket-view-page">

        <article class="message-card">

            <h1>Ticket Not Found</h1>

            <p class="message error-message">
                The requested ticket does not exist,
                or you are not allowed to view it.
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

$ticket = new Ticket(
    $row['ticket_id'],
    $row['customer_name'],
    $row['contact_email'],
    $row['location'],
    $row['description'],
    $row['submitted_date'],
    $row['status'],
    $row['emergency_level'],
    $row['assigned_date'],
    $row['assigned_staff'],
    $row['image_name']
);

require_once 'includes/header.php';

if (isset($_SESSION['flash_message'])) {

    $flashType =
        $_SESSION['flash_type'] ?? 'success';

    ?>

    <section class="flash-container">

        <p class="message
                  <?= escape($flashType) ?>-message">

            <?= escape($_SESSION['flash_message']) ?>

        </p>

    </section>

    <?php

    unset(
        $_SESSION['flash_message'],
        $_SESSION['flash_type']
    );
}

echo $ticket->displayTicketPage();

require_once 'includes/footer.php';

?>