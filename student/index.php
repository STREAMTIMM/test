<?php
include 'header.php'
?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>

<!-- Main Content -->
<script src="../assets/js/student_dashboard.js"></script>
<style>
    #calendarJs {
        max-width: 800px;
        margin: 0 auto;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendarJs');
        fetch(`../server/get_my_events.php`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const events = data.events.map(event => ({
                        title: event.title,
                        start: event.start,
                        end: event.end ? event.end : event.start,
                        id: event.id,
                    }));

                    const calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        events: events,
                        eventClick: function(info) {
                            const event = info.event;
                            // You can customize this with a modal instead of alert
                            Swal.fire({
                                icon: 'info',
                                title: event.title,
                                text: `Time: ${event.start.toLocaleString()}\n`,
                            });

                        }
                    });

                    calendar.render();
                } else {
                    console.error('Error fetching events:', data.error);
                }
            })
            .catch(error => {
                console.error('Error fetching events:', error);
            });
    });
</script>
<main class="main-content" id="mainContent">
    <!-- Top Navigation -->
    <div class="d-flex justify-content-end mb-4">
        <div class="top-nav d-flex align-items-center gap-3">
            <a href="#" class="text-decoration-none">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </a>

            <div class="username-box"><?= $user_name ?></div>
            <a href="profile.php">
                <img class="profile-icon" src="../assets/images/2.png" alt="">
            </a>

        </div>
    </div>

    <!-- Schedule Section -->
    <h3 class="section-title">THIS MONTH'S SCHEDULE:</h3>
    <div class="calendar-container mb-4">
        <div class="calendar-header" hidden>
            <h4 class="calendar-title" id="month-year">March 2025</h4>
            <div class="calendar-nav" hidden>
                <button id="prev-month"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg></button>
                <button id="next-month"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg></button>
            </div>
        </div>

        <div class="calendar-days" id="calendar" hidden></div>
        <div class="calendar">
            <div id='calendarJs'></div>
        </div>
    </div>

    <div class="discover-container">
        <?php

        $rows = $db->getRowsWhere('groups', ['status' => '1']);

        foreach ($rows as $row) {

    //         echo '
    //    <a class="group-card text-decoration-none" href="chatroom.php?group_id=' . htmlspecialchars($row['id']) . '">
    //         ' . ucwords($row['name']) . '
    //     </a>
    // ';

    $group_id = $row['id'];
    $data = $db->fetchOne("SELECT * FROM `student_groups` WHERE user_id = :user_id AND group_id = :group_id  AND status = 1", [
        'user_id' => $user_id,
        'group_id' => $group_id
    ]);

    if ($data) {
        echo '<a href="chatroom.php?group_id=' . htmlspecialchars($row['id']) . '" class="group-card text-decoration-none">' . htmlspecialchars(ucwords($row['name'])) . '</a>';
    } 
    else {
        echo '<a href="#" class="group-card text-decoration-none" onclick="askToJoin(\'' . htmlspecialchars(addslashes($row['name'])) . '\', ' . intval($row['id']) . ')">' . htmlspecialchars(ucwords($row['name'])) . '</a>';
    }


        }
        ?>


    </div>
    <!-- Reports Section -->
    <h3 class="section-title" hidden>MONTHLY REPORT:</h3>
    <div class="bg-white p-3 rounded shadow-sm" hidden>
        <div class="report-container" id="reportContainer">
            <!-- Will be populated dynamically -->
        </div>
    </div>
</main>

<!-- Join Group Confirmation Modal -->
<div class="modal-overlay hidden" id="joinGroupModalOverlay"></div>
<div class="popup join-popup hidden" id="joinGroupModal">
    <span class="close-btn" onclick="joinGroup(false)">×</span>
    <p>DO YOU WANT TO JOIN</p>
    <p class="popup-name" id="joinGroupQuestion">GROUP NAME</p>
    <div>
        <button class="yes-btn" onclick="joinGroup(true)">YES</button>
        <button class="no-btn" onclick="joinGroup(false)">NO</button>
    </div>
</div>

<!-- Joined Success Modal -->
<div class="modal-overlay hidden" id="joinedModalOverlay"></div>
<div class="popup joined-popup hidden" id="joinedModal">
    <span class="close-btn" onclick="closeJoinedModal()">×</span>
    <p>YOU HAVE JOINED</p>
    <p class="popup-name" id="joinedMessage">GROUP NAME</p>
</div>

<!-- Event Modal -->
<div class="modal-overlay" id="modalOverlay"></div>
<div class="event-modal" id="eventModal">
    <button class="btn-close" onclick="closeModal()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </button>

    <!-- Pending View (default view for new events) -->
    <div id="pendingView">
        <h3 id="eventDate">March 30, 2025</h3>
        <p><strong>Time:</strong> <span id="eventTime">2:00 PM - 4:00 PM</span></p>
        <p><strong>What:</strong> <span id="eventTitle">Software Engineering - midterms study session</span></p>
        <p><strong>Where:</strong> <span id="eventLocation">QCU library</span></p>
        <p><strong>Additional notes:</strong></p>
        <div class="notes-box" id="eventNotes">Bring laptop or any study material.</div>

        <div class="status-badge status-pending">Pending</div>

        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="declineEvent()">Decline</button>
            <button class="btn btn-confirm" onclick="acceptEvent()">Accept</button>
        </div>
    </div>

    <!-- Accepted View (shown after accepting a pending event) -->
    <div id="acceptedView" style="display:none;">
        <h3 id="acceptedEventDate">March 30, 2025</h3>
        <p><strong>Time:</strong> <span id="acceptedEventTime">2:00 PM - 4:00 PM</span></p>
        <p><strong>What:</strong> <span id="acceptedEventTitle">Software Engineering - midterms study session</span></p>
        <p><strong>Where:</strong> <span id="acceptedEventLocation">QCU library</span></p>
        <p><strong>Additional notes:</strong></p>
        <div class="notes-box" id="acceptedEventNotes">Bring laptop or any study material.</div>

        <div class="status-acknowledged"></div>

        <div class="modal-footer">
            <button class="btn btn-change" onclick="showChangeResponseFormFromAccepted()">Change Response</button>
        </div>
    </div>

    <!-- Declined View -->
    <div id="declinedView" style="display:none;">
        <h3 id="declinedEventDate">March 30, 2025</h3>
        <p><strong>Time:</strong> <span id="declinedEventTime">2:00 PM - 4:00 PM</span></p>
        <p><strong>What:</strong> <span id="declinedEventTitle">Software Engineering - midterms study session</span></p>
        <p><strong>Where:</strong> <span id="declinedEventLocation">QCU library</span></p>
        <p><strong>Additional notes:</strong></p>
        <div class="notes-box" id="declinedEventNotes">Bring laptop or any study material.</div>

        <div class="status-badge status-declined">Declined</div>

        <div class="modal-footer">
            <button class="btn btn-change" onclick="showChangeResponseForm()">Change Response</button>
        </div>
    </div>

    <!-- Done & Attended View -->
    <div id="doneAttendedView" style="display:none;">
        <h3 id="doneAttendedEventDate">March 30, 2025</h3>
        <p><strong>Time:</strong> <span id="doneAttendedEventTime">2:00 PM - 4:00 PM</span></p>
        <p><strong>What:</strong> <span id="doneAttendedEventTitle">Software Engineering - midterms study session</span></p>
        <p><strong>Where:</strong> <span id="doneAttendedEventLocation">QCU library</span></p>
        <p><strong>Additional notes:</strong></p>
        <div class="notes-box" id="doneAttendedEventNotes">Bring laptop or any study material.</div>

        <div class="status-badge status-done-attended">Done & Attended</div>

        <div class="modal-footer">
            <button class="btn btn-close" onclick="closeModal()"></button>
        </div>
    </div>

    <!-- Done & Declined View -->
    <div id="doneDeclinedView" style="display:none;">
        <h3 id="doneDeclinedEventDate">March 30, 2025</h3>
        <p><strong>Time:</strong> <span id="doneDeclinedEventTime">2:00 PM - 4:00 PM</span></p>
        <p><strong>What:</strong> <span id="doneDeclinedEventTitle">Software Engineering - midterms study session</span></p>
        <p><strong>Where:</strong> <span id="doneDeclinedEventLocation">QCU library</span></p>
        <p><strong>Additional notes:</strong></p>
        <div class="notes-box" id="doneDeclinedEventNotes">Bring laptop or any study material.</div>

        <div class="status-badge status-done-declined">Done & Declined</div>

        <div class="modal-footer">
            <button class="btn btn-close" onclick="closeModal()"></button>
        </div>
    </div>

    <!-- Canceled View (by admin) -->
    <div id="canceledView" style="display:none;">
        <h3 id="canceledEventDate">March 30, 2025</h3>
        <p><strong>Time:</strong> <span id="canceledEventTime">2:00 PM - 4:00 PM</span></p>
        <p><strong>What:</strong> <span id="canceledEventTitle">Software Engineering - midterms study session</span></p>
        <p><strong>Where:</strong> <span id="canceledEventLocation">QCU library</span></p>
        <p><strong>Additional notes:</strong></p>
        <div class="notes-box" id="canceledEventNotes">Bring laptop or any study material.</div>

        <div class="status-badge status-canceled">Canceled by Admin</div>

        <div class="modal-footer">
            <button class="btn btn-close" onclick="closeModal()"></button>
        </div>
    </div>

    <!-- Change Response Form (shown when changing from declined) -->
    <div id="changeResponseForm" style="display:none;">
        <h3>Change Your Response</h3>
        <p>Would you like to accept this event instead?</p>

        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="closeChangeResponseForm()">Keep Declined</button>
            <button class="btn btn-confirm" onclick="acceptFromChange()">Accept</button>
        </div>
    </div>

    <!-- Change Response Form (shown when changing from accepted) -->
    <div id="changeResponseFormFromAccepted" style="display:none;">
        <h3>Change Your Response</h3>
        <p>Would you like to decline this event?</p>

        <div class="modal-footer">
            <button class="btn btn-close" onclick="closeChangeResponseFormFromAccepted()">Keep Accepted</button>
            <button class="btn btn-cancel" onclick="declineFromAccepted()">Decline</button>
        </div>
    </div>
</div>

</div>

<!-- Report Modal -->
<div class="modal-overlay" id="reportModalOverlay" style="display: none;"></div>
<div class="event-modal" id="reportModal" style="display: none;">
    <button class="btn-close" onclick="closeReportModal()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </button>

    <h3>REPORT FOR THE MONTH OF:</h3>
    <h4 id="reportMonthYear">Month Year</h4>

    <div class="report-section">
        <h5>Meetings created and done:</h5>
        <div id="meetingsDoneList">
            <!-- Will be populated dynamically -->
        </div>
    </div>

    <div class="report-section">
        <h5>Materials uploaded:</h5>
        <div id="materialsUploadedList">
            <!-- Will be populated dynamically -->
        </div>
    </div>

    <div class="report-section">
        <h5>Total groups created:</h5>
        <div id="totalGroupsCreated">
            <!-- Will be populated dynamically -->
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-close" onclick="closeReportModal()">Close</button>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>