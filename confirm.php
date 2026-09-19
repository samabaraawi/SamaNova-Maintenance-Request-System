<?php

require_once 'dbconfig.in.php';
require_once 'includes/auth.php';
require_once 'classes/Ticket.php';

requireCustomer();

$ticketId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

$ticket = null;

if ($ticketId) {

    $statement = $pdo->prepare(
        'SELECT
            t.ticket_id,
            c.name AS customer_name,
            t.contact_email,
            t.location,
            t.description,
            t.submitted_date,
            t.status,
            t.emergency_level,
            t.assigned_date,
            s.name AS assigned_staff,
            t.image_name
         FROM tickets t
         JOIN users c
            ON t.customer_id = c.id
         LEFT JOIN users s
            ON t.assigned_staff_id = s.id
         WHERE t.ticket_id = :ticketId
           AND t.customer_id = :customerId'
    );

    $statement->execute([
        ':ticketId' => $ticketId,
        ':customerId' => $_SESSION['user_id']
    ]);

    $ticketData = $statement->fetch();

    if ($ticketData) {

        $ticket = new Ticket(
            $ticketData['ticket_id'],
            $ticketData['customer_name'],
            $ticketData['contact_email'],
            $ticketData['location'],
            $ticketData['description'],
            $ticketData['submitted_date'],
            $ticketData['status'],
            $ticketData['emergency_level'],
            $ticketData['assigned_date'],
            $ticketData['assigned_staff'],
            $ticketData['image_name']
        );
    }
}

$pageTitle = 'Request Confirmation';

require_once 'includes/header.php';

?>

<main class="confirmation-page">

    <?php if ($ticket === null): ?>

        <article class="message-card">

            <h1>Ticket Not Found</h1>

            <p class="message error-message">
                The requested ticket could not be found.
            </p>

            <a class="btn reset-button"
               href="ticketsys.php">
                Return to My Tickets
            </a>

        </article>

    <?php else: ?>

        <section class="page-heading confirmation-heading">

            <h1>Request Submitted Successfully</h1>

            <p>
                Dear <?= escape($ticket->getCustomerName()) ?>,
                thank you for submitting your maintenance request.
            </p>

        </section>

        <article class="confirmation-card">

            <section class="confirmation-details-section">

                <p class="ticket-reference">

                    Your ticket reference number is:

                    <strong class="ticket-number">
                        #<?= escape(
                            (string) $ticket->getTicketId()
                        ) ?>
                    </strong>

                </p>

                <ul class="confirmation-details">

                    <li class="detail-item">

                        <strong>Customer Name:</strong>

                        <span>
                            <?= escape(
                                $ticket->getCustomerName()
                            ) ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Contact Email:</strong>

                        <span>
                            <?= escape(
                                $ticket->getContactEmail()
                            ) ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Location:</strong>

                        <span>
                            <?= escape(
                                $ticket->getLocation()
                            ) ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Issue Description:</strong>

                        <span>
                            <?= escape(
                                $ticket->getDescription()
                            ) ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Emergency Level:</strong>

                        <span class="emergency-label
                                     emergency-<?= escape(
                                         strtolower(
                                             $ticket->getEmergencyLevel()
                                         )
                                     ) ?>">

                            <?= escape(
                                $ticket->getEmergencyLevel()
                            ) ?>

                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Submitted Date:</strong>

                        <span>
                            <?= escape(
                                $ticket->getSubmittedDate()
                            ) ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Ticket Status:</strong>

                        <span class="status-label
                                     status-<?= escape(
                                         strtolower(
                                             $ticket->getStatus()
                                         )
                                     ) ?>">

                            <?= escape($ticket->getStatus()) ?>

                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Photo Uploaded:</strong>

                        <span>
                            <?= $ticket->getImageName()
                                ? 'Yes'
                                : 'No' ?>
                        </span>

                    </li>

                </ul>

            </section>

            <section class="confirmation-photo-section">

                <?php if ($ticket->getImageName()): ?>

                    <figure class="ticket-figure">

                        <img class="ticket-photo"
                             src="images/<?= rawurlencode(
                                 basename(
                                     $ticket->getImageName()
                                 )
                             ) ?>"
                             alt="Maintenance request photo">

                        <figcaption>
                            Uploaded maintenance request photo
                        </figcaption>

                    </figure>

                <?php else: ?>

                    <p class="no-photo-message">
                        No photo was uploaded for this ticket.
                    </p>

                <?php endif; ?>

            </section>

            <div class="confirmation-actions">

                <p>
                    Our maintenance team will respond to your request shortly.
                </p>

                <a class="btn"
                   href="ticketsys.php">
                    View My Tickets
                </a>

            </div>

        </article>

    <?php endif; ?>

</main>

<?php require_once 'includes/footer.php'; ?>