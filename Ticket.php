<?php

class Ticket
{
    private $ticketId;
    private $customerName;
    private $contactEmail;
    private $location;
    private $description;
    private $submittedDate;
    private $status;
    private $emergencyLevel;
    private $assignedDate;
    private $assignedStaff;
    private $imageName;

    public function __construct(
        $ticketId,
        $customerName,
        $contactEmail,
        $location,
        $description,
        $submittedDate,
        $status,
        $emergencyLevel,
        $assignedDate,
        $assignedStaff,
        $imageName
    ) {
        $this->ticketId = $ticketId;
        $this->customerName = $customerName;
        $this->contactEmail = $contactEmail;
        $this->location = $location;
        $this->description = $description;
        $this->submittedDate = $submittedDate;
        $this->status = $status;
        $this->emergencyLevel = $emergencyLevel;
        $this->assignedDate = $assignedDate;
        $this->assignedStaff = $assignedStaff;
        $this->imageName = $imageName;
    }

    private function escapeValue($value)
    {
        return htmlspecialchars(
            $value ?? '',
            ENT_QUOTES,
            'UTF-8'
        );
    }

    public function getTicketId()
    {
        return $this->ticketId;
    }

    public function setTicketId($ticketId)
    {
        $this->ticketId = $ticketId;
    }

    public function getCustomerName()
    {
        return $this->customerName;
    }

    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
    }

    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getSubmittedDate()
    {
        return $this->submittedDate;
    }

    public function setSubmittedDate($submittedDate)
    {
        $this->submittedDate = $submittedDate;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getEmergencyLevel()
    {
        return $this->emergencyLevel;
    }

    public function setEmergencyLevel($emergencyLevel)
    {
        $this->emergencyLevel = $emergencyLevel;
    }

    public function getAssignedDate()
    {
        return $this->assignedDate;
    }

    public function setAssignedDate($assignedDate)
    {
        $this->assignedDate = $assignedDate;
    }

    public function getAssignedStaff()
    {
        return $this->assignedStaff;
    }

    public function setAssignedStaff($assignedStaff)
    {
        $this->assignedStaff = $assignedStaff;
    }

    public function getImageName()
    {
        return $this->imageName;
    }

    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    public function displayTable($allowAssign = false)
{
    $statusClass =
        'status-' . strtolower($this->status);

    $emergencyClass =
        'emergency-' . strtolower($this->emergencyLevel);

    ob_start();
    ?>

    <tr>

        <td class="ticket-id-cell">

            <a class="ticket-id-link"
               href="view.php?id=<?= $this->escapeValue(
                   $this->ticketId
               ) ?>">

                #<?= $this->escapeValue($this->ticketId) ?>

            </a>

        </td>

        <td class="description-cell">
            <?= $this->escapeValue($this->description) ?>
        </td>

        <td>
            <?= $this->escapeValue($this->submittedDate) ?>
        </td>

        <td>
            <?= $this->escapeValue($this->customerName) ?>
        </td>

        <td>

            <span class="emergency-label
                         <?= $this->escapeValue(
                             $emergencyClass
                         ) ?>">

                <?= $this->escapeValue(
                    $this->emergencyLevel
                ) ?>

            </span>

        </td>

        <td>

            <span class="status-label
                         <?= $this->escapeValue(
                             $statusClass
                         ) ?>">

                <?= $this->escapeValue($this->status) ?>

            </span>

        </td>

        <td>

            <div class="table-actions">

                <a class="action-link"
                   href="view.php?id=<?= $this->escapeValue(
                       $this->ticketId
                   ) ?>"
                   title="View Ticket">

                    <img class="action-icon"
                         src="images/view.svg"
                         alt="View Ticket">

                </a>

                <?php if ($allowAssign): ?>

                    <a class="action-link"
                       href="assign.php?id=<?= $this->escapeValue(
                           $this->ticketId
                       ) ?>"
                       title="Assign Ticket">

                        <img class="action-icon"
                             src="images/assign.svg"
                             alt="Assign Ticket">

                    </a>

                <?php endif; ?>

            </div>

        </td>

    </tr>

    <?php

    return ob_get_clean();
}

    public function displayTicketPage()
{
    $hasImage =
        $this->imageName !== null &&
        $this->imageName !== '';

    $statusClass =
        'status-' . strtolower($this->status);

    $emergencyClass =
        'emergency-' . strtolower($this->emergencyLevel);

    ob_start();
    ?>

    <main class="ticket-view-page">

        <header class="page-heading">

            <h2>
                View Ticket:
                #<?= $this->escapeValue($this->ticketId) ?>
            </h2>

            <p>
                Complete maintenance request information.
            </p>

        </header>

        <article class="ticket-card">

            <section class="ticket-details">

                <ul class="ticket-details-list">

                    <li class="detail-item">

                        <strong>Submitted by Customer:</strong>

                        <span>
                            <?= $this->escapeValue(
                                $this->customerName
                            ) ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Email:</strong>

                        <span>
                            <?= $this->escapeValue(
                                $this->contactEmail
                            ) ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Location:</strong>

                        <span>
                            <?= $this->escapeValue(
                                $this->location
                            ) ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Issue Description:</strong>

                        <span>
                            <?= $this->escapeValue(
                                $this->description
                            ) ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Urgency Level:</strong>

                        <span class="emergency-label
                                     <?= $this->escapeValue(
                                         $emergencyClass
                                     ) ?>">

                            <?= $this->escapeValue(
                                $this->emergencyLevel
                            ) ?>

                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Submitted Date:</strong>

                        <span>
                            <?= $this->escapeValue(
                                $this->submittedDate
                            ) ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Ticket Status:</strong>

                        <span class="status-label
                                     <?= $this->escapeValue(
                                         $statusClass
                                     ) ?>">

                            <?= $this->escapeValue(
                                $this->status
                            ) ?>

                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Assigned To:</strong>

                        <span>
                            <?= $this->assignedStaff
                                ? $this->escapeValue(
                                    $this->assignedStaff
                                )
                                : 'Not assigned' ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Assigned Date:</strong>

                        <span>
                            <?= $this->assignedDate
                                ? $this->escapeValue(
                                    $this->assignedDate
                                )
                                : 'Not assigned' ?>
                        </span>

                    </li>

                    <li class="detail-item">

                        <strong>Photo Uploaded:</strong>

                        <span>
                            <?= $hasImage ? 'Yes' : 'No' ?>
                        </span>

                    </li>

                </ul>

            </section>

            <section class="ticket-photo-section">

                <?php if ($hasImage): ?>

                    <figure class="ticket-figure">

                        <img class="ticket-photo"
                             src="images/<?= rawurlencode(
                                 basename($this->imageName)
                             ) ?>"
                             alt="Photo for ticket <?= $this->escapeValue(
                                 $this->ticketId
                             ) ?>">

                        <figcaption>
                            Uploaded maintenance issue photo
                        </figcaption>

                    </figure>

                <?php else: ?>

                    <p class="no-photo-message">
                        No photo was uploaded for this ticket.
                    </p>

                <?php endif; ?>

            </section>

            <div class="ticket-card-actions">

                <a class="btn reset-button"
                   href="ticketsys.php">
                    Return to Ticket List
                </a>

            </div>

        </article>

    </main>

    <?php

    return ob_get_clean();
}
}

?>