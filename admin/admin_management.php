<?php
require_once '../conn/db.php';
$db = new DatabaseHandler();

if (!isset($_GET['group_id'])) {
    echo "<script>window.location.href = '../';</script>";
    exit;
}

$id = $_GET['group_id'];
$group_id = $id;
// Fetch the group
$data = $db->fetchOne("SELECT * FROM `groups` WHERE id = :id AND status = 1", ['id' => $id]);

// If group not found, redirect
if (!$data) {
    echo "<script>alert('Group not found or inactive.'); window.location.href = '../';</script>";
    exit;
}
$memberCount = 0;
$count = $db->fetchOne("SELECT COUNT(id) as count FROM `groups` WHERE id = :id AND status = 1", ['id' => $id])['count'];

if ($count) {
    $memberCount = $count;
}


$group_data = $db->fetchAll("
    SELECT a.*,b.name FROM student_groups AS a
    JOIN users AS b ON a.user_id = b.id 
    WHERE a.group_id = :group_id
    AND a.status = 1
    AND b.status = 1
", ['group_id' => $group_id]);

$group_data_images = $db->fetchAll("
    SELECT 
        DATE_FORMAT(uploaded_at, '%M %Y') AS month,  -- Format date as Month Year (e.g., April 2020)
        GROUP_CONCAT(file_path ORDER BY uploaded_at) AS files  -- Concatenate filenames, ordered by upload date
    FROM 
        group_files
    WHERE 
        group_id = :group_id
    GROUP BY 
        month
    ORDER BY 
        uploaded_at DESC  -- Order the result by uploaded_at descending (latest first)
", ['group_id' => $group_id]);



$lessonfiles  = $db->fetchAll("
    SELECT id,
        DATE_FORMAT(uploaded_at, '%M %Y') AS month,  
        file_path AS files  
    FROM 
        group_lessons
    WHERE 
        group_id = :group_id AND
        status = 1

    ORDER BY 
        uploaded_at DESC  -- Order the result by uploaded_at descending (latest first)
", ['group_id' => $group_id]);

$name = ucwords($data['name']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>QCUnnectED</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="../assets/css/admin_management.css">
    <script src="../assets/js/admin_management.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendarJs');
            const groupId = <?= json_encode($id) ?>;

            fetch(`../server/get_events.php?group_id=${groupId}`)
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

                            dateClick: function(info) {
                                const hasEvent = events.some((event) => event.start.split('T')[0] === info.dateStr);

                                if (hasEvent) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'This date already has an event!',
                                        text: 'Please choose another date.',
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Add Event',
                                        input: 'text',
                                        inputLabel: 'Event Title',
                                        inputPlaceholder: 'Enter event title...',
                                        showCancelButton: true,
                                        inputValidator: (value) => {
                                            if (!value) {
                                                return 'You need to write something!';
                                            }
                                        },
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            const newEvent = {
                                                title: result.value,
                                                start: info.dateStr,
                                                allDay: true,
                                                group_id: groupId,
                                            };

                                            axios.post('../server/events.php', newEvent)
                                                .then((response) => {
                                                    if (response.data.success) {
                                                        Swal.fire('Event added successfully!', '', 'success').then(() => {
                                                            window.location.reload();
                                                        });
                                                    } else {
                                                        Swal.fire('Event date already exists!', '', 'error');
                                                    }
                                                })
                                                .catch((error) => {
                                                    console.error('Error saving event:', error);
                                                    Swal.fire('Failed to add event!', '', 'error');
                                                });
                                        }
                                    });
                                }
                            },

                            eventClick: function(info) {
                                Swal.fire({
                                    title: 'Event Options',
                                    text: `What would you like to do with "${info.event.title}"?`,
                                    icon: 'question',
                                    showCancelButton: true,
                                    showDenyButton: true,
                                    confirmButtonText: 'Edit',
                                    denyButtonText: 'Delete',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // EDIT
                                        Swal.fire({
                                            title: 'Edit Event Title',
                                            input: 'text',
                                            inputValue: info.event.title,
                                            showCancelButton: true,
                                            inputValidator: (value) => {
                                                if (!value) return 'Title cannot be empty';
                                            }
                                        }).then(res => {
                                            if (res.isConfirmed) {
                                                axios.post('../server/edit_event.php', {
                                                    id: info.event.id,
                                                    title: res.value
                                                }).then(r => {
                                                    if (r.data.success) {
                                                        Swal.fire('Event updated!', '', 'success').then(() => window.location.reload());
                                                    } else {
                                                        Swal.fire('Failed to update event.', '', 'error');
                                                    }
                                                });
                                            }
                                        });
                                    } else if (result.isDenied) {
                                        // DELETE
                                        Swal.fire({
                                            title: 'Are you sure?',
                                            text: 'This will permanently delete the event.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonText: 'Yes, delete it!',
                                        }).then((del) => {
                                            if (del.isConfirmed) {
                                                axios.post('../server/delete_event.php', {
                                                    id: info.event.id
                                                }).then(r => {
                                                    if (r.data.success) {
                                                        Swal.fire('Event deleted!', '', 'success').then(() => window.location.reload());
                                                    } else {
                                                        Swal.fire('Failed to delete event.', '', 'error');
                                                    }
                                                });
                                            }
                                        });
                                    }
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
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        #calendarJs {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <?php include 'aside.php' ?>

    <!-- Main Content -->
    <main>
        <div class="main-container">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="top-bar">
                    <div class="top-bar-content">
                        <!-- Mobile Menu Buttons -->
                        <button class="mobile-menu-btn" id="leftMenuBtn">
                            <i class="fas fa-bars"></i>
                        </button>

                        <!-- Group Info -->
                        <div class="group-info-container">
                            <img src="../assets/images/group.webp" alt="Group Logo" class="group-logo">
                            <div class="group-info">
                                <span class="group-name">
                                    <h2><?= $name ?></h2>
                                </span>
                                <span class="member-count" id="memberCount">6 members</span>
                            </div>
                        </div>

                        <button class="mobile-menu-btn" id="rightMenuBtn">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </div>
                </div>
            </div>



            <div class="calendar-section">
                <h3 class="section-title">THIS MONTH'S SCHEDULE:</h3>
                <div class="calendar-container" hidden>
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
                    <div class="calendar-days" id="calendar"></div>
                </div>

                <div class="calendar">
                    <div id='calendarJs'></div>
                </div>
            </div>


            <div class="file-management-section">
                <h3 class="section-title">File Management</h3>
                <div class="file-management-buttons">
                    <button class="btn btn-primary" id="addFileBtn">
                        <i class="fas fa-plus-circle"></i> Add File
                    </button>
                    <button class="btn btn-secondary" id="viewFilesBtn">
                        <i class="fas fa-eye"></i> View Files
                    </button>
                </div>
            </div>

            <!-- File Upload Modal -->
            <div class="modal-overlay" id="fileUploadModalOverlay" style="display: none;"></div>
            <div class="event-modal" id="fileUploadModal" style="display: none;">
                <h3>Upload File to Lessons</h3>
                <div class="form-group">
                    <label for="fileInput">Select File:</label>
                    <input type="file" class="form-control" id="fileInput">
                </div>
                <div class="modal-footer">
                    <button class="btn-close" onclick="closeFileUploadModal()">
                        Cancel
                    </button>
                    <button class="btn btn-confirm" onclick='uploadFile(<?= json_encode($id) ?>)'>Upload</button>
                </div>
            </div>

            <!-- Add this modal for file viewing -->
            <div class="modal-overlay" id="fileViewModalOverlay" style="display: none;"></div>
            <div class="event-modal" id="fileViewModal" style="display: none;">
                <h3>Lesson Files</h3>
                <div class="file-list">
                    <?php
                    foreach ($lessonfiles as $index => $file):
                        $filename = basename($file['files'])
                    ?>
                        <div class="file-list-item">
                            <span><?= htmlspecialchars($filename) ?></span>
                            <button class="delete-btn" onclick="confirmDeleteFile(<?= $file['id'] ?>, '<?= $filename ?>')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>



                </div>
                <div class="modal-footer">
                    <button class="btn-close" onclick="closeFileViewModal()">
                        Close
                    </button>
                </div>
            </div>

    </main>


    <!-- Right Sidebar -->
    <?php include 'rightsidebar.php' ?>


    <!-- Enhanced Event Modal -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="event-modal" id="eventModal">

        <div id="pendingView" style="display:none;">
            <button style="background: none; border: none; cursor: pointer; position: absolute; top: 15px; right: 15px;" onclick="closeModal()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <h3 id="pendingEventDate"></h3>
            <p><strong>When:</strong> <span id="pendingEventTime"></span></p>
            <p><strong>What:</strong> <span id="pendingEventTitle"></span></p>
            <p><strong>Where:</strong> <span id="pendingEventLocation"></span></p>
            <p><strong></strong></p>
            <div class="notes-box" id="pendingEventNotes"></div>

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

        <div id="changeForm" style="display:none;">
            <div class="form-group">
                <label>Date:</label>
                <input type="date" class="form-control" id="changeDate">
            </div>
            <div class="form-group">
                <label>Start Time:</label>
                <input type="time" class="form-control" id="changeStartTime">
            </div>
            <div class="form-group">
                <label>End Time:</label>
                <input type="time" class="form-control" id="changeEndTime">
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
                <!-- List items will be added here -->
            </div>
            <button class="btn btn-close" onclick="closeListModal()">Back</button>
        </div>
    </div>


    <!-- Preview Modal (update HTML) -->
    <div class="preview-modal" id="previewModal">
        <button class="preview-close" id="previewClose"><i class="fas fa-times"></i></button>
        <button class="preview-nav preview-prev" id="previewPrev"><i class="fas fa-chevron-left"></i></button>
        <div class="preview-content" id="previewContent"></div>
        <button class="preview-nav preview-next" id="previewNext"><i class="fas fa-chevron-right"></i></button>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationTitle">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmationBody">
                    Are you sure you want to perform this action?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmYes">Yes</button>
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <!-- file preview modal -->

    <div class="modal" id="filePreviewModal" tabindex="-1" style="display:none;">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">File Preview</h5>
                    <button type="button" class="btn-close" onclick="closeFilePreview()"></button>
                </div>
                <div class="modal-body" id="filePreviewContent">
                    <p>Loading preview...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- new event -->
    <div class="event-modal" id="addEventModal" style="display: none;">
        <h3>Add New Event</h3>
        <div class="form-group">
            <label>Date:</label>
            <input type="date" class="form-control" id="addEventDate">
        </div>
        <div class="form-group">
            <label>Start Time:</label>
            <input type="time" class="form-control" id="addEventStartTime">
        </div>
        <div class="form-group">
            <label>End Time:</label>
            <input type="time" class="form-control" id="addEventEndTime">
        </div>
        <div class="form-group">
            <label>What:</label>
            <input type="text" class="form-control" id="addEventWhat">
        </div>
        <div class="form-group">
            <label>Where:</label>
            <input type="text" class="form-control" id="addEventWhere">
        </div>
        <div class="form-group">
            <label>Additional notes:</label>
            <textarea class="form-control" id="addEventNotes"></textarea>
        </div>
        <div class="modal-footer">
            <button class="btn-close" onclick="closeAddEventModal()">
                Cancel
            </button>
            <button class="btn btn-confirm" onclick="saveNewEvent()">Add Event</button>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationTitle">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmationBody">
                    Are you sure you want to perform this action?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmYes">Yes</button>
                    <button type="button" class="btn btn-success" id="confirmNo">Cancel</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>