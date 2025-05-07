<?php
require_once '../conn/db.php';
$db = new DatabaseHandler();

$groups = $db->fetchAll("
    SELECT * FROM groups where status = 1
",);
$files = $db->fetchAll("
    SELECT * FROM group_files where status = 1
");
$lessons = $db->fetchAll("
    SELECT * FROM group_lessons where status = 1
");
$messages = $db->fetchAll("
    SELECT * FROM group_messages where status = 1
");

// Prepare chart data
$chartData = [];

foreach ($groups as $group) {
    $groupId = $group['id'];
    $chartData[] = [
        'group_name' => $group['name'],
        'files' => count(array_filter($files, fn($f) => $f['group_id'] == $groupId)),
        'lessons' => count(array_filter($lessons, fn($l) => $l['group_id'] == $groupId)),
        'messages' => count(array_filter($messages, fn($m) => $m['group_id'] == $groupId)),
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/adminDB.js"></script>
    <link rel="stylesheet" href="../assets/css/adminDB.css">
    <title>AdminHomepage</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendarJs');
        fetch(`../server/get_event_all.php`)
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
<style>
    #calendarJs {
        max-width: 800px;
        margin: 0 auto;
    }
</style>

<body>
    <button class="burger-menu" id="burgerMenu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>

    <?php include 'aside.php' ?>
    <style>
    .summary { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; margin-bottom: 20px; }
    .summary-item { background: #f4f4f4; padding: 10px 20px; border-radius: 6px; min-width: 120px; text-align: center; }
    .chart-container { max-width: 900px; margin: auto; margin-bottom: 40px; }
    .date-picker { text-align: center; margin-bottom: 30px; }
  </style>
    <!-- Main Content -->
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

                <div class="username-box">🔑<?= $user_name ?></div>
                <a href="admin_Profile.php">
                    <img class="profile-icon" src="../assets/images/2.png" alt="">
                </a>

            </div>
        </div>

        <!-- Schedule Section -->
        <h3 class="section-title">THIS MONTH'S SCHEDULE:</h3>
        <div class="calendar-container mb-4">
            <div class="calendar-header">
                <h4 class="calendar-title" id="month-year">March 2025</h4>
                <div class="calendar-nav">
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

        <!-- group Section -->
        <h3 class="section-title">All Groups:</h3>
        <div class="bg-white p-3 rounded shadow-sm mb-4">
            <div class="discover-container">


                <?php

                $rows = $db->getRowsWhere('groups');

                foreach ($rows as $row) {
                    echo '
                    <div class="group-card text-decoration-none" style="position: relative;">
                    <a href="adminChatroom.php?group_id=' . $row['id'] . '" style="display: flex; align-items: center; justify-content: center; text-align: center; width: 100%; height: 100%; color: inherit; text-decoration: none;">
                        ' . htmlspecialchars(ucwords($row['name'])) . '
                    </a>
                    <button class="group-menu">&#8942;</button>
                    <div class="group-menu-options">
                        <button class="delete-option" onclick="initiateDelete(event, this,' . htmlspecialchars($row['id']) . ')">Delete Group</button>
                    </div>
                </div>
                    ';
                }
                ?>

            </div>
        </div>

        <!-- Reports Section -->
        <h3 class="section-title" hidden>MONTHLY REPORT:</h3>
        <div class="bg-white p-3 rounded shadow-sm" hidden>
            <div class="report-container" id="reportContainer">
            </div>
        </div>
        <h3 class="section-title">Group Activity Report:</h3>
        <div class="bg-white p-3 rounded shadow-sm">

            <h2>📊 Group Reports</h2>

            <div class="date-picker">
                <input id="dateRange" placeholder="Select date range" style="padding: 8px; width: 250px;">
            </div>

            <div class="summary" id="summaryBox">
                <!-- Summary will be filled dynamically -->
            </div>

            <div class="chart-container"><canvas id="filesChart"></canvas></div>
            <div class="chart-container"><canvas id="lessonsChart"></canvas></div>
            <div class="chart-container"><canvas id="messagesChart"></canvas></div>
            <div class="chart-container"><canvas id="summaryChart"></canvas></div>

        </div>

    </main>

    <!-- calendar events -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="event-modal" id="eventModal">
        <h3 id="eventDate">March 30, 2025</h3>
        <span id="eventTime">2:00 PM - 4:00 PM</span></p>
        <span id="eventTitle">Software Engineering - midterms study session</span></p>
        <span id="eventLocation">QCU library</span></p>
        <div class="notes-box" id="eventNotes">Bring laptop or any study material.</div>

        <!-- pending status/ green highlight -->
        <div id="pendingView" style="display:none;">
            <button style="background: none; border: none; cursor: pointer; position: absolute; top: 15px; right: 15px;" onclick="closeModal()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <h3 id="pendingEventDate">March 25, 2025</h3>
            <p><strong>When:</strong> <span id="pendingEventTime">2:00 PM - 4:00 PM</span></p>
            <p><strong>What:</strong> <span id="pendingEventTitle">Software Engineering - midterms study session</span></p>
            <p><strong>Where:</strong> <span id="pendingEventLocation">QCU library</span></p>
            <p><strong>Additional notes:</strong></p>
            <div class="notes-box" id="pendingEventNotes">Bring laptop or any study material.</div>

            <div class="response-group">
                <h4>Members Accepted (<span id="acceptedCount">0</span>)</h4>
                <div class="response-container">
                    <ul class="member-list" id="acceptedList">
                        <!-- Will be populated dynamically -->
                    </ul>
                </div>
            </div>

            <div class="response-group">
                <h4>Members Declined (<span id="declinedCount">0</span>)</h4>
                <div class="response-container">
                    <ul class="member-list" id="declinedList">
                        <!-- Will be populated dynamically -->
                    </ul>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="cancelEvent()">Cancel Event</button>
                <button class="btn btn-change" onclick="showChangeForm()">Change</button>
            </div>
        </div>
        <!-- done status/ yellow highlight -->
        <div id="doneView" style="display:none;">
            <div class="response-group">
                <h4>Attendance Summary</h4>
                <p>Attended: <span id="attendedCount">0</span>
                    <button class="btn btn-sm btn-change" onclick="showAttendees()">View List</button>
                </p>
                <p>Did Not Attend: <span id="notAttendedCount">0</span>
                    <button class="btn btn-sm btn-change" onclick="showNonAttendees()">View List</button>
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-close" style="background-color:#9b2226" style="color: #FFFFF0;" ; onclick="closeModal()">back</button>
            </div>
        </div>

        <div id="canceledView" style="display:none;">
            <p>This event has been canceled.</p>
            <div class="modal-footer">
                <button class="btn btn-change" id="changeCanceledBtn" onclick="showChangeForm()">Change</button>
                <button class="btn btn-close" onclick="closeModal()">Close</button>
            </div>
        </div>
        <!-- change form for pending and canceled events -->
        <div id="changeForm" style="display:none;">
            <div class="form-group">
                <label>Date:</label>
                <input type="date" class="form-control" id="changeDate">
            </div>
            <div class="form-group">
                <label>Time:</label>
                <input type="text" class="form-control" id="changeTime" placeholder="e.g. 14:00 - 16:00">
            </div>
            <div class="form-group">
                <label>What:</label>
                <input type="text" class="form-control" id="changeWhat">
            </div>
            <div class="form-group">
                <label>Where:</label>
                <input type="text" class="form-control" id="changeWhere">
            </div>
            <div class="form-group">
                <label>Additional notes:</label>
                <textarea class="form-control" id="changeNotes"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-close" onclick="closeChangeForm()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <button class="btn btn-confirm" onclick="confirmChanges()">Save Changes</button>
            </div>
        </div>


        <div id="confirmView" style="display:none;">
            <p>Confirm schedule changes?</p>
            <div class="modal-footer">
                <button class="btn btn-close" onclick="closeConfirmView()">Back</button>
                <button class="btn btn-confirm" onclick="saveChanges()">Yes</button>
            </div>
        </div>
    </div>

    <div class="list-modal" id="listModal">
        <div class="list-modal-content">
            <h4 id="listModalTitle">Attendees</h4>
            <div class="list-container" id="listContainer">

            </div>
            <button class="btn btn-close" onclick="closeListModal()">Back</button>
        </div>
    </div>

    <!-- Create Group pop-up -->
    <div class="modal-overlay" id="createGroupModalOverlay" style="display: none;"></div>
    <div class="event-modal" id="createGroupModal" style="display: none;">
        <button class="btn-close" onclick="closeCreateGroupModal()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <h3>Create New Group</h3>

        <form id="signup-form">
            <div class="form-group">
                <label for="name">Group Name:</label>
                <input type="text" required class="form-control" id="name" placeholder="Enter group name">
            </div>

            <div class="form-group">
                <label for="group_number">Total Number of Members:</label>
                <select required class="form-control" id="group_number">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-close" onclick="closeCreateGroupModal()">Cancel</button>
                <button type="submit" class="btn btn-confirm">Create Group</button>
            </div>
        </form>
    </div>

    <!-- Confirmation Box -->
    <div class="modal-overlay" id="confirmationOverlay" style="display: none;"></div>
    <div class="confirmation-box" id="confirmationBox">
        <p>Are you sure you want to delete this group?</p>
        <div class="confirmation-buttons">
            <button class="btn btn-close" onclick="cancelDelete()">Cancel</button>
            <button class="btn btn-confirm" id="confirmdelete" onclick="confirmDelete()">Yes, Delete</button>
        </div>
    </div>
    <!-- monthly reports -->
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

            </div>
        </div>

        <div class="report-section">
            <h5>Materials uploaded:</h5>
            <div id="materialsUploadedList">

            </div>
        </div>

        <div class="report-section">
            <h5>Total groups created:</h5>
            <div id="totalGroupsCreated">

            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-close" onclick="closeReportModal()">Close</button>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
<style>
    .group-card {
        position: relative;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
        background-color: #f8f9fa;
    }

    .group-menu {
        position: absolute;
        top: 10px;
        right: 10px;
        background: transparent;
        border: none;
        font-size: 18px;
        cursor: pointer;
    }

    .group-menu-options {
        display: none;
        position: absolute;
        top: 35px;
        right: 10px;
        background: white;
        border: 1px solid #ccc;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 4px;
        z-index: 999;
    }

    .group-menu-options button {
        background: none;
        border: none;
        padding: 8px 12px;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .group-menu-options button:hover {
        background-color: #f0f0f0;
    }
</style>

<script>
    document.getElementById('signup-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const name = document.getElementById('name').value.trim();
        const group_number = document.getElementById('group_number').value.trim();

        axios.post('../server/group.php', {
                name: name,
                group_number: group_number,
                type: 'add'
            })
            .then(function(response) {
                if (response.data.success) {
                    alert('Group added...');
                    window.location.href = "";
                } else {
                    alert('Error: ' + response.data.message);
                }
            })
            .catch(function(error) {
                console.error('An error occurred:', error);
                alert('An unexpected error occurred. Please try again.');
            });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menus = document.querySelectorAll('.group-menu');

        menus.forEach(menu => {
            menu.addEventListener('click', function(e) {
                e.stopPropagation();
                // Close other open menus
                document.querySelectorAll('.group-menu-options').forEach(opt => {
                    if (opt !== this.nextElementSibling) opt.style.display = 'none';
                });

                // Toggle this one
                const options = this.nextElementSibling;
                options.style.display = (options.style.display === 'block') ? 'none' : 'block';
            });
        });

        // Hide menus when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.group-menu-options').forEach(opt => opt.style.display = 'none');
        });
    });

    function initiateDelete(event, btn, id) {
        event.stopPropagation();
        if (confirm('Are you sure you want to delete this group?')) {
            // implement your delete logic here (e.g., AJAX or redirect)
            axios.post('../server/group.php', {
                    id: id,
                    type: 'delete'
                })
                .then(function(response) {
                    if (response.data.success) {
                        alert('Group deleted...');
                        window.location.href = "";
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                })
                .catch(function(error) {
                    console.error('An error occurred:', error);
                    alert('An unexpected error occurred. Please try again.');
                });
        }
    }
</script>

<script>
    let filesChart, lessonsChart, messagesChart, summaryChart;

    flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        onClose: function(selectedDates) {
            if (selectedDates.length === 2) {
                const start = selectedDates[0].toISOString();
                const end = selectedDates[1].toISOString();
                fetchReportData(start, end);
            }
        }
    });

    function fetchReportData(startDate, endDate) {
        axios.post('../server/report_data.php', {
            start_date: startDate,
            end_date: endDate
        }).then(response => {
            const {
                summary,
                chartData
            } = response.data;

            // Update summary
            document.getElementById("summaryBox").innerHTML = `
        <div class="summary-item"><strong>Groups:</strong><br>${summary.total_groups}</div>
        <div class="summary-item"><strong>Files:</strong><br>${summary.total_files}</div>
        <div class="summary-item"><strong>Lessons:</strong><br>${summary.total_lessons}</div>
        <div class="summary-item"><strong>Messages:</strong><br>${summary.total_messages}</div>
      `;

            // Update charts
            drawChart('filesChart', 'Files per Group', chartData.labels, chartData.files, filesChart, c => filesChart = c);
            drawChart('lessonsChart', 'Lessons per Group', chartData.labels, chartData.lessons, lessonsChart, c => lessonsChart = c);
            drawChart('messagesChart', 'Messages per Group', chartData.labels, chartData.messages, messagesChart, c => messagesChart = c);
            drawChart('summaryChart', 'Total Activity per Group', chartData.labels, chartData.totals, summaryChart, c => summaryChart = c);
        }).catch(err => {
            console.error('Error loading report data:', err);
            alert('Failed to load data');
        });
    }

    function drawChart(canvasId, title, labels, data, chartRef, saveCallback) {
        if (chartRef) chartRef.destroy();
        const ctx = document.getElementById(canvasId).getContext('2d');
        const newChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: title,
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: title
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        saveCallback(newChart);
    }

    // Load today’s data by default
    const todayz = new Date();
    const iso = todayz.toISOString();
    fetchReportData(iso, iso);
</script>

</html>