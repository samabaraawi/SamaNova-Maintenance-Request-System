<?php

require_once 'dbconfig.in.php';
require_once 'includes/auth.php';
require_once 'classes/Ticket.php';

requireLogin();

$isManager = $_SESSION['user_type'] === 'manager';

$searchText = trim($_POST['search_text'] ?? '');
$searchBy = $_POST['search_by'] ?? 'description';
$submittedDate = trim($_POST['submitted_date'] ?? '');
$status = $_POST['status'] ?? '';
$emergencyLevel = $_POST['emergency_level'] ?? '';

if (!in_array(
    $searchBy,
    ['description', 'customer_name'],
    true
)) {
    $searchBy = 'description';
}

/*
 * Generate the status dropdown dynamically
 * using data retrieved from the database.
 */
$statusSql = "SELECT DISTINCT status
              FROM tickets
              ORDER BY status";

$statusStatement = $pdo->prepare($statusSql);
$statusStatement->execute();

$statusOptions =
    $statusStatement->fetchAll(PDO::FETCH_COLUMN);

/*
 * Generate the emergency level dropdown
 * dynamically using PHP and the database.
 */
$emergencySql = "SELECT DISTINCT emergency_level
                 FROM tickets
                 ORDER BY emergency_level";

$emergencyStatement = $pdo->prepare($emergencySql);
$emergencyStatement->execute();

$emergencyOptions =
    $emergencyStatement->fetchAll(PDO::FETCH_COLUMN);

/*
 * Main ticket query.
 */
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

        WHERE 1 = 1";

$parameters = [];

/*
 * Customers can only view their own tickets.
 */
if (!$isManager) {

    $sql .= " AND ticket.customer_id = :customerId";

    $parameters[':customerId'] =
        $_SESSION['user_id'];
}

/*
 * On the first manager dashboard visit,
 * show only pending unassigned tickets.
 */
if (
    $isManager &&
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {

    $sql .= " AND ticket.status = :defaultStatus
              AND ticket.assigned_staff_id IS NULL";

    $parameters[':defaultStatus'] = 'Pending';
}

/*
 * Apply the submitted search filters.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($searchText !== '') {

        if ($searchBy === 'customer_name') {

            $sql .=
                " AND customer.name LIKE :searchText";

        } else {

            $sql .=
                " AND ticket.description LIKE :searchText";
        }

        $parameters[':searchText'] =
            '%' . $searchText . '%';
    }

    if ($submittedDate !== '') {

        $sql .=
            " AND ticket.submitted_date = :submittedDate";

        $parameters[':submittedDate'] =
            $submittedDate;
    }

    if (
        $status !== '' &&
        in_array($status, $statusOptions, true)
    ) {

        $sql .= " AND ticket.status = :status";

        $parameters[':status'] = $status;
    }

    if (
        $emergencyLevel !== '' &&
        in_array(
            $emergencyLevel,
            $emergencyOptions,
            true
        )
    ) {

        $sql .=
            " AND ticket.emergency_level = :emergencyLevel";

        $parameters[':emergencyLevel'] =
            $emergencyLevel;
    }
}

$sql .= " ORDER BY ticket.submitted_date DESC,
                   ticket.ticket_id DESC";

$statement = $pdo->prepare($sql);

foreach ($parameters as $name => $value) {
    $statement->bindValue($name, $value);
}

$statement->execute();

$tickets = [];

while ($row = $statement->fetch()) {

    $tickets[] = new Ticket(
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
}

$pageTitle = $isManager
    ? 'Manager Dashboard'
    : 'Customer Dashboard';

require_once 'includes/header.php';

?>

<main class="dashboard-page">

    <section class="page-heading">

        <h2>
            <?= $isManager
                ? 'Manager Dashboard'
                : 'Customer Dashboard' ?>
        </h2>

        <p>
            Welcome <?= escape($_SESSION['user_name']) ?>.
            Search and review maintenance tickets below.
        </p>

    </section>

    <section class="dashboard-section search-section">

        <h2>Advanced Ticket Search</h2>

        <form class="search-form"
              action="ticketsys.php"
              method="post">

            <fieldset class="search-options">

                <legend>Search by:</legend>

                <label class="radio-option">

                    <input type="radio"
                           name="search_by"
                           value="description"
                        <?= $searchBy === 'description'
                            ? 'checked' : '' ?>>

                    Ticket Description

                </label>

                <label class="radio-option">

                    <input type="radio"
                           name="search_by"
                           value="customer_name"
                        <?= $searchBy === 'customer_name'
                            ? 'checked' : '' ?>>

                    Customer Name

                </label>

            </fieldset>

            <div class="search-fields">

                <div class="form-group">

                    <label class="form-label"
                           for="search_text">
                        Search Text:
                    </label>

                    <input class="form-control"
                           type="text"
                           id="search_text"
                           name="search_text"
                           placeholder="Search..."
                           value="<?= escape($searchText) ?>">

                </div>

                <div class="form-group">

                    <label class="form-label"
                           for="submitted_date">
                        Submission Date:
                    </label>

                    <input class="form-control"
                           type="date"
                           id="submitted_date"
                           name="submitted_date"
                           value="<?= escape($submittedDate) ?>">

                </div>

                <div class="form-group">

                    <label class="form-label"
                           for="status">
                        Status:
                    </label>

                    <select class="form-control"
                            id="status"
                            name="status">

                        <option value="">
                            All
                        </option>

                        <?php foreach (
                            $statusOptions as $statusOption
                        ): ?>

                            <option
                                value="<?= escape($statusOption) ?>"
                                <?= $status === $statusOption
                                    ? 'selected' : '' ?>>

                                <?= escape($statusOption) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label class="form-label"
                           for="emergency_level">
                        Emergency Level:
                    </label>

                    <select class="form-control"
                            id="emergency_level"
                            name="emergency_level">

                        <option value="">
                            All
                        </option>

                        <?php foreach (
                            $emergencyOptions as $emergencyOption
                        ): ?>

                            <option
                                value="<?= escape($emergencyOption) ?>"
                                <?= $emergencyLevel === $emergencyOption
                                    ? 'selected' : '' ?>>

                                <?= escape($emergencyOption) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>

            <div class="form-actions search-actions">

                <button class="btn"
                        type="submit">
                    Filter
                </button>

                <a class="btn reset-button"
                   href="ticketsys.php">
                    Reset
                </a>

            </div>

        </form>

    </section>

    <section class="dashboard-section ticket-section">

        <h2>Ticket List</h2>

        <div class="table-container">

            <table class="ticket-table">

                <thead>

                    <tr>
                        <th>Ticket ID</th>
                        <th>Issue Description</th>
                        <th>Date Submitted</th>
                        <th>Customer Name</th>
                        <th>Urgency Level</th>
                        <th>Status</th>
                        <th>Ticket Actions</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (count($tickets) === 0): ?>

                        <tr>

                            <td class="empty-table-message"
                                colspan="7">

                                No tickets match the search criteria.

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($tickets as $ticket): ?>

                            <?= $ticket->displayTable($isManager) ?>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>


<?php require_once 'includes/footer.php'; ?>