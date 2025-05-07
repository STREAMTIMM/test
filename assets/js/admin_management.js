
// ======================
// GLOBAL VARIABLES
// ======================
const monthNames = ["January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"];
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
const today = new Date();
let currentEventDay = null;
let currentEventMonth = null;
let currentEventYear = null;
let selectedDayForNewEvent = null;
let selectedMonthForNewEvent = null;
let selectedYearForNewEvent = null;

let lessonFiles = [
    { name: "lesson1.pdf", url: "files/lesson1.pdf" },
    { name: "lesson2.pdf", url: "files/lesson2.pdf" },
    { name: "lesson3.pdf", url: "files/lesson3.pdf" }
];


const events = {
    10: {
        type: 'done',
        title: 'Software Engineering Study',
        location: 'QCU Library',
        note: 'Bring laptop, or any study material',
        time: '10:00 AM - 12:00 PM',
        attendees: ["Meatball_05", "ShakeNfrieS", "Jobilee"],
        declined: ["Jeremiah", "JebadiahRichardson", "Jocelyn"],
        notResponded: []
    },
    15: {
        type: 'pending',
        title: 'Project Discussion',
        location: 'Online',
        note: 'Prepare your project updates',
        time: '3:00 PM - 5:00 PM',
        attendees: ["John Doe", "Jane Smith"],
        declined: ["Mike Johnson"],
        notResponded: ["Sarah Williams", "Robert Brown", "Emily Davis"]
    },
    20: {
        type: 'canceled',
        title: 'Team Meeting',
        location: 'QCU Room 101',
        note: 'Monthly team sync',
        time: '2:00 PM - 3:00 PM',
        attendees: [],
        declined: [],
        notResponded: []
    }
};



// ======================
// FILE & IMAGE PREVIEW
// ======================
function initLessonFilePreview() {
    document.querySelectorAll('.lesson-file-tile').forEach(tile => {
        const fileUrl = tile.dataset.file;
        const downloadEl = tile.querySelector('.file-download');
        const previewEl = tile.querySelector('.file-preview');

        if (downloadEl) {
            downloadEl.addEventListener('click', handleFileDownload);
        }

        if (previewEl) {
            previewEl.addEventListener('click', handleFilePreview);
        }
    });
}

function handleFileDownload(e) {
    e.stopPropagation();
    const tile = e.currentTarget.closest('.lesson-file-tile');
    const fileUrl = tile?.dataset.file;
    if (!fileUrl) return;

    const link = document.createElement('a');
    link.href = fileUrl;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    link.remove();
}

function handleFilePreview(e) {
    e.stopPropagation();
    const tile = e.currentTarget.closest('.lesson-file-tile');
    const fileUrl = tile?.dataset.file;
    if (!fileUrl) return;

    const extension = fileUrl.split('.').pop().toLowerCase();
    const content = document.getElementById('filePreviewContent');

    if (['pdf'].includes(extension)) {
        content.innerHTML = `<iframe src="${fileUrl}" style="width:100%;height:80vh;" frameborder="0"></iframe>`;
    } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
        content.innerHTML = `<img src="${fileUrl}" style="max-width:100%; height:auto;">`;
    } else {
        content.innerHTML = `<p>Cannot preview this file. <a href="${fileUrl}" download>Download it instead</a>.</p>`;
    }

    const modal = document.getElementById('filePreviewModal');
    modal.style.display = 'block';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeFilePreview() {
    const modal = document.getElementById('filePreviewModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

function openImagePreview(imgElement) {
    galleryImages = Array.from(document.querySelectorAll('.image-tile img')).map(i => i.src);
    currentImageIndex = galleryImages.indexOf(imgElement.src);
    showImageAt(currentImageIndex);
    document.getElementById('previewModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePreview() {
    document.getElementById('previewModal').style.display = 'none';
    document.body.style.overflow = '';
}

function showImageAt(index) {
    if (index < 0) index = galleryImages.length - 1;
    if (index >= galleryImages.length) index = 0;
    currentImageIndex = index;
    document.getElementById('previewContent').innerHTML = `<img src="${galleryImages[index]}" alt="Preview">`;
}

function showPrevImage() {
    showImageAt(currentImageIndex - 1);
}

function showNextImage() {
    showImageAt(currentImageIndex + 1);
}

// ======================
// CALENDAR FUNCTIONS
// ======================
function generateCalendar(month, year) {
    const calendar = document.getElementById("calendar");
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    document.getElementById("month-year").textContent = `${monthNames[month]} ${year}`;
    calendar.innerHTML = "";

    // Add empty cells for days before the first day of month
    for (let i = 0; i < firstDay; i++) {
        const emptyDay = document.createElement("div");
        emptyDay.classList.add("calendar-day", "empty");
        calendar.appendChild(emptyDay);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement("div");
        dayElement.classList.add("calendar-day");
        dayElement.textContent = day;

        if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
            dayElement.classList.add("today");
        }

        if (events[day]) {
            dayElement.classList.add(events[day].type);
            dayElement.addEventListener('click', () => openModal(day, month, year));
        } else {
            dayElement.addEventListener('click', () => openAddEventModal(day, month, year));
        }

        calendar.appendChild(dayElement);
    }
}

function navigateMonth(change) {
    currentMonth += change;

    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }

    generateCalendar(currentMonth, currentYear);

}

// ======================
// EVENT MODAL FUNCTIONS
// ======================
function openModal(day, month, year) {
    currentEventDay = day;
    currentEventMonth = month;
    currentEventYear = year;
    const event = events[day] || {};

    document.getElementById("pendingView").style.display = "none";
    document.getElementById("doneView").style.display = "none";
    document.getElementById("canceledView").style.display = "none";
    document.getElementById("changeForm").style.display = "none";
    document.getElementById("confirmView").style.display = "none";



    if (event.type === 'pending') showPendingView(event);
    else if (event.type === 'done') showDoneView(event);
    else if (event.type === 'canceled') showCanceledView(day, month, year);

    document.getElementById("eventModal").style.display = "block";
    document.getElementById("modalOverlay").style.display = "block";
}

function closeModal() {
    document.getElementById("eventModal").style.display = "none";
    document.getElementById("modalOverlay").style.display = "none";
}

function showPendingView(event) {
    const view = document.getElementById("pendingView");
    view.style.display = "block";

    // Populate the pending event details
    document.getElementById("pendingEventDate").textContent =
        `${monthNames[currentEventMonth]} ${currentEventDay}, ${currentEventYear}`;
    document.getElementById("pendingEventTime").textContent = event.time || '3:00 PM - 5:00 PM';
    document.getElementById("pendingEventTitle").textContent = event.title || 'Project Discussion';
    document.getElementById("pendingEventLocation").textContent = event.location || 'Online';
    document.getElementById("pendingEventNotes").textContent = event.note || 'Prepare your project updates';

    // Populate accepted members
    document.getElementById("acceptedCount").textContent = event.attendees.length;
    const acceptedList = document.getElementById("acceptedList");
    acceptedList.innerHTML = event.attendees.map(member => `<li>${member}</li>`).join('');

    // Populate declined members
    document.getElementById("declinedCount").textContent = event.declined.length;
    const declinedList = document.getElementById("declinedList");
    declinedList.innerHTML = event.declined.map(member => `<li>${member}</li>`).join('');
}

function showDoneView(event) {
    const view = document.getElementById("doneView");
    view.style.display = "block";
    document.getElementById("attendedCount").textContent = (event.attendees || []).length;
    document.getElementById("notAttendedCount").textContent =
        (event.declined || []).length + (event.notResponded || []).length;
}

function showCanceledView(day, month, year) {
    const view = document.getElementById("canceledView");
    view.style.display = "block";
    const eventDate = new Date(year, month, day);
    document.getElementById("changeCanceledBtn").style.display =
        eventDate > today ? "inline-block" : "none";
}

// ======================
// EVENT MANAGEMENT
// ======================
function openAddEventModal(day, month, year) {
    const selectedDate = new Date(year, month, day);
    const comparableSelected = new Date(year, month, day).setHours(0, 0, 0, 0);
    const comparableToday = new Date().setHours(0, 0, 0, 0);

    if (comparableSelected < comparableToday) {
        alert("Cannot add events to past dates");
        return;
    }

    selectedDayForNewEvent = day;
    selectedMonthForNewEvent = month;
    selectedYearForNewEvent = year;

    const formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    document.getElementById("addEventDate").value = formattedDate;

    document.getElementById("addEventStartTime").value = "";
    document.getElementById("addEventEndTime").value = "";
    document.getElementById("addEventWhat").value = "";
    document.getElementById("addEventWhere").value = "";
    document.getElementById("addEventNotes").value = "";

    document.getElementById("addEventModal").style.display = "block";
    document.getElementById("modalOverlay").style.display = "block";
}

function closeAddEventModal() {
    document.getElementById("addEventModal").style.display = "none";
    document.getElementById("modalOverlay").style.display = "none";
}

function saveNewEvent() {
    const startTime = document.getElementById("addEventStartTime").value;
    const endTime = document.getElementById("addEventEndTime").value;

    const newEvent = {
        type: 'pending',
        title: document.getElementById("addEventWhat").value,
        location: document.getElementById("addEventWhere").value,
        note: document.getElementById("addEventNotes").value,
        time: `${formatTimeForDisplay(startTime)} - ${formatTimeForDisplay(endTime)}`,
        startTime: startTime,
        endTime: endTime,
        attendees: [],
        declined: [],
        notResponded: []
    };

    events[selectedDayForNewEvent] = newEvent;
    closeAddEventModal();
    updateEventDisplays();
}

function formatTimeForDisplay(timeString) {
    if (!timeString) return '';
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}

function updateEventDisplays() {
    generateCalendar(currentMonth, currentYear);

}

function showChangeForm() {
    const event = events[currentEventDay] || {};

    // Parse the existing time if available
    let startTime = '';
    let endTime = '';

    if (event.time) {
        const timeParts = event.time.split(' - ');
        if (timeParts.length === 2) {
            startTime = convertDisplayTimeTo24Hour(timeParts[0].trim());
            endTime = convertDisplayTimeTo24Hour(timeParts[1].trim());
        }
    }

    // Set form values
    document.getElementById("changeDate").value =
        `${currentYear}-${String(currentEventMonth + 1).padStart(2, '0')}-${String(currentEventDay).padStart(2, '0')}`;
    document.getElementById("changeStartTime").value = startTime || event.startTime || '';
    document.getElementById("changeEndTime").value = endTime || event.endTime || '';
    document.getElementById("changeWhat").value = event.title || '';
    document.getElementById("changeWhere").value = event.location || '';
    document.getElementById("changeNotes").value = event.note || '';

    // Show the form
    document.getElementById("pendingView").style.display = "none";
    document.getElementById("doneView").style.display = "none";
    document.getElementById("canceledView").style.display = "none";
    document.getElementById("changeForm").style.display = "block";
    document.getElementById("confirmView").style.display = "none";
}

// Helper function to convert displayed time to 24-hour format
function convertDisplayTimeTo24Hour(timeStr) {
    if (!timeStr) return '';
    const [time, modifier] = timeStr.split(' ');
    let [hours, minutes] = time.split(':');

    if (modifier === 'PM' && hours !== '12') {
        hours = parseInt(hours, 10) + 12;
    } else if (modifier === 'AM' && hours === '12') {
        hours = '00';
    }

    return `${hours}:${minutes}`;
}

function closeChangeForm() {
    document.getElementById("changeForm").style.display = "none";
    openModal(currentEventDay, currentEventMonth, currentEventYear);
}

function confirmChanges() {
    document.getElementById("changeForm").style.display = "none";
    document.getElementById("confirmView").style.display = "block";
}

function closeConfirmView() {
    document.getElementById("confirmView").style.display = "none";
    document.getElementById("changeForm").style.display = "block";
}

function saveChanges() {
    const oldEventDay = currentEventDay;
    const startTime = document.getElementById("changeStartTime").value;
    const endTime = document.getElementById("changeEndTime").value;
    const event = events[oldEventDay] || {};

    // Get the new date values
    const newDate = new Date(document.getElementById("changeDate").value);
    const newDay = newDate.getDate();
    const newMonth = newDate.getMonth();
    const newYear = newDate.getFullYear();

    // Update event properties
    event.title = document.getElementById("changeWhat").value;
    event.location = document.getElementById("changeWhere").value;
    event.note = document.getElementById("changeNotes").value;
    event.startTime = startTime;
    event.endTime = endTime;
    event.time = `${formatTimeForDisplay(startTime)} - ${formatTimeForDisplay(endTime)}`;

    // Handle status changes
    if (event.type === 'canceled') {
        event.type = 'pending';
    }

    // Move event if date changed
    if (newDay !== oldEventDay || newMonth !== currentEventMonth || newYear !== currentYear) {
        delete events[oldEventDay];
        events[newDay] = event;
        currentEventDay = newDay;
        currentEventMonth = newMonth;
        currentEventYear = newYear;
    }

    // Update displays
    generateCalendar(currentMonth, currentYear);
    closeModal();
}

function cancelEvent() {
    const event = events[currentEventDay];
    if (event) {
        event.type = 'canceled';
        event.attendees = [];
        event.declined = [];
        event.notResponded = [];
        updateEventDisplays();
    }
    closeModal();
}

function showAttendees() {
    const event = events[currentEventDay];
    if (event && event.attendees) {
        document.getElementById("listModalTitle").textContent = "Attendees";
        const listContainer = document.getElementById("listContainer");
        listContainer.innerHTML = `<ul>${event.attendees.map(a => `<li>${a}</li>`).join('')}</ul>`;
        document.getElementById("listModal").style.display = "block";
    }
}

function showNonAttendees() {
    const event = events[currentEventDay];
    if (event) {
        const nonAttendees = [...(event.declined || []), ...(event.notResponded || [])];
        document.getElementById("listModalTitle").textContent = "Did Not Attend";
        const listContainer = document.getElementById("listContainer");
        listContainer.innerHTML = `<ul>${nonAttendees.map(a => `<li>${a}</li>`).join('')}</ul>`;
        document.getElementById("listModal").style.display = "block";
    }
}

function closeListModal() {
    document.getElementById("listModal").style.display = "none";
}

function checkAndUpdateEventStatuses() {
    const now = new Date();

    Object.entries(events).forEach(([day, event]) => {
        if (event.type !== 'pending') return;

        const eventDate = new Date(currentYear, currentMonth, parseInt(day));
        if (event.endTime) {
            const [hours, minutes] = event.endTime.split(':');
            eventDate.setHours(parseInt(hours), parseInt(minutes));

            if (now > eventDate) {
                event.type = 'done';
            }
        }
    });

    generateCalendar(currentMonth, currentYear);
}

// ======================
// FILE MANAGEMENT
// ======================
function openFileUploadModal() {
    document.getElementById('fileUploadModalOverlay').style.display = 'block';
    document.getElementById('fileUploadModal').style.display = 'block';
}

function closeFileUploadModal() {
    document.getElementById('fileUploadModalOverlay').style.display = 'none';
    document.getElementById('fileUploadModal').style.display = 'none';
    document.getElementById('fileInput').value = '';
}

function uploadFile(id) {
    const fileInput = document.getElementById('fileInput');
    if (fileInput.files.length === 0) {
        alert('Please select a file to upload');
        return;
    }
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('id', id); // Ensure `id` is defined

    axios.post('../server/file.php', formData)
        .then(function (response) {
            if (response.data.success) {
                alert('Upload successful!');
            } else {
                alert('Error: ' + response.data.message);
            }
        })
        .catch(function (error) {
            console.error('Upload failed:', error);
            alert('An unexpected error occurred.');
        });
}



function updateLessonsAccordion() {
    const accordionBody = document.querySelector('#lessonsCollapse .accordion-body');
    accordionBody.innerHTML = '';

    lessonFiles.forEach(file => {
        const fileTile = document.createElement('div');
        fileTile.className = 'lesson-file-tile';
        fileTile.dataset.file = file.url;
        fileTile.innerHTML = `
     <span class="file-download">${file.name}</span>
     <span class="file-preview" title="Preview this file">↗️</span>
 `;
        accordionBody.appendChild(fileTile);
    });

    // Reinitialize the preview handlers
    initLessonFilePreview();
}


function openFileViewModal() {
    document.getElementById('fileViewModalOverlay').style.display = 'block';
    document.getElementById('fileViewModal').style.display = 'block';
    populateFileViewModal();
}

function closeFileViewModal() {
    document.getElementById('fileViewModalOverlay').style.display = 'none';
    document.getElementById('fileViewModal').style.display = 'none';
}

function populateFileViewModal() {
    const fileList = document.getElementById('lessonFilesList');
    fileList.innerHTML = '';

    lessonFiles.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-list-item';
        fileItem.innerHTML = `
<span>${file.name}</span>
<button class="delete-btn" onclick="confirmDeleteFile(${index}, '${file.name}')">
 <i class="fas fa-trash-alt"></i>
</button>
`;
        fileList.appendChild(fileItem);
    });
}

function confirmDeleteFile(index, fileName) {
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    document.getElementById('confirmationTitle').textContent = 'Delete File';
    document.getElementById('confirmationBody').innerHTML = `
Do you want to delete <strong>${fileName}</strong> from lessons?
`;

    document.getElementById('confirmYes').onclick = function () {
        axios.post('../server/group.php', {
            id: index,
            type: 'delete_lesson'
        })
            .then(function (response) {
                if (response.data.success) {
                    alert('File Deleted...');
                } else {
                    alert('Error: ' + response.data.message);
                }
            })
            .catch(function (error) {
                console.error('An error occurred:', error);
                alert('An unexpected error occurred. Please try again.');
            });
        modal.hide();
    };

    modal.show();
}

function deleteFile(index) {
    lessonFiles.splice(index, 1);
    updateLessonsAccordion();
    populateFileViewModal();
    alert('File deleted successfully');
}


// ======================
// FILE PREVIEW FUNCTIONS
// ======================
function initLessonFilePreview() {
    document.querySelectorAll('.lesson-file-tile').forEach(tile => {
        const fileUrl = tile.dataset.file;
        const downloadEl = tile.querySelector('.file-download');
        const previewEl = tile.querySelector('.file-preview');

        if (downloadEl) {
            downloadEl.addEventListener('click', (e) => {
                e.stopPropagation();
                downloadFile(fileUrl, downloadEl.textContent);
            });
        }

        if (previewEl) {
            previewEl.addEventListener('click', (e) => {
                e.stopPropagation();
                previewFile(fileUrl);
            });
        }
    });
}

function downloadFile(url, filename) {
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function previewFile(url) {
    const extension = url.split('.').pop().toLowerCase();
    const content = document.getElementById('filePreviewContent');

    if (['pdf'].includes(extension)) {
        content.innerHTML = `<iframe src="${url}" style="width:100%;height:80vh;" frameborder="0"></iframe>`;
    } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
        content.innerHTML = `<img src="${url}" style="max-width:100%; height:auto;">`;
    } else {
        content.innerHTML = `<p>Cannot preview this file. <a href="${url}" download>Download it instead</a>.</p>`;
    }

    const modal = document.getElementById('filePreviewModal');
    modal.style.display = 'block';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeFilePreview() {
    const modal = document.getElementById('filePreviewModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}


// ======================
// SIDEBAR & UI FUNCTIONS
// ======================
function initSidebarToggle() {
    const leftMenuBtn = document.getElementById('leftMenuBtn');
    const rightMenuBtn = document.getElementById('rightMenuBtn');
    const sidebar = document.getElementById('sidebar');
    const rightSidebar = document.getElementById('right-sidebar');

    leftMenuBtn.addEventListener('click', e => {
        e.stopPropagation();
        sidebar.classList.toggle('active');
        if (sidebar.classList.contains('active')) rightSidebar.classList.remove('active');
    });

    rightMenuBtn.addEventListener('click', e => {
        e.stopPropagation();
        rightSidebar.classList.toggle('active');
        if (rightSidebar.classList.contains('active')) sidebar.classList.remove('active');
    });

    // Close when clicking outside
    document.addEventListener('click', e => {
        if (!sidebar.contains(e.target) && !leftMenuBtn.contains(e.target)) {
            sidebar.classList.remove('active');
        }
        if (!rightSidebar.contains(e.target) && !rightMenuBtn.contains(e.target)) {
            rightSidebar.classList.remove('active');
        }
    });
}

function initFilesSection() {
    const tabs = document.querySelectorAll('.file-tabs .tab');
    const backBtn = document.getElementById('backBtn');
    const mainSidebar = document.getElementById('mainSidebarContent');
    const filesSection = document.getElementById('filesSection');

    document.querySelector('[onclick="showFilesSection()"]').addEventListener('click', () => {
        mainSidebar.classList.add('hidden');
        filesSection.classList.remove('hidden');
        showTab('images');
    });

    backBtn.addEventListener('click', () => {
        filesSection.classList.add('hidden');
        mainSidebar.classList.remove('hidden');
    });

    tabs.forEach(tab => {
        tab.addEventListener('click', () => showTab(tab.textContent.toLowerCase()));
    });
}

function showTab(tabName) {
    const galleries = {
        images: 'imagesGallery',
        files: 'filesGallery',
        links: 'linksGallery'
    };

    document.querySelectorAll('.file-tabs .tab').forEach(tab => {
        tab.classList.toggle('active', tab.textContent.toLowerCase() === tabName);
    });

    Object.values(galleries).forEach(id => {
        document.getElementById(id).classList.add('hidden');
    });

    document.getElementById(galleries[tabName]).classList.remove('hidden');
}

function updateMemberCount() {
    const count = document.querySelectorAll('.member-item').length;
    const el = document.getElementById('memberCount');
    if (el) el.textContent = `${count} ${count === 1 ? 'member' : 'members'}`;
}

function initMemberKick() {
    document.querySelectorAll('.member-item .kick-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const memberName = this.closest('.member-item').querySelector('span').textContent;
            confirmKick(memberName, 'Software Engineering');
        });
    });
}

// ======================
// MEMBER KICK FIX
// ======================
function confirmKick(memberName, groupName) {
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    document.getElementById('confirmationBody').textContent = `Kick ${memberName} from ${groupName}?`;

    // Store reference to the member item
    const memberItem = document.querySelector(`.member-item[data-member="${memberName}"]`);

    // Create fresh event handler
    const yesBtn = document.getElementById('confirmYes');
    const newYesBtn = yesBtn.cloneNode(true);
    yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);

    newYesBtn.addEventListener('click', function () {
        if (memberItem) {
            memberItem.remove();
            updateMemberCount();
        }
        modal.hide(); // This will properly remove the modal backdrop
    });

    modal.show();
}

// ======================
// GROUP DELETION FUNCTIONS
// ======================
function confirmDeleteGroup(id) {
    const groupName = "Software Engineering"; // Or get this dynamically
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));

    // Customize the confirmation modal
    document.getElementById('confirmationTitle').textContent = 'Delete Group';
    document.getElementById('confirmationBody').innerHTML = `
<p>Do you want to delete <strong>${groupName}</strong>?</p>
<p class="text-danger"><small>This action cannot be undone!</small></p>
`;

    // Style the buttons
    const yesBtn = document.getElementById('confirmYes');
    const noBtn = document.getElementById('confirmNo');

    yesBtn.textContent = 'Yes';
    yesBtn.className = 'btn btn-danger';
    noBtn.textContent = 'Cancel';
    noBtn.className = 'btn btn-success';

    // Remove any existing handlers
    const newYesBtn = yesBtn.cloneNode(true);
    const newNoBtn = noBtn.cloneNode(true);
    yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);
    noBtn.parentNode.replaceChild(newNoBtn, noBtn);

    // Set up new handlers
    newYesBtn.addEventListener('click', function () {
        deleteGroup(id);
        modal.hide();
    });

    newNoBtn.addEventListener('click', function () {
        modal.hide();
    });

    modal.show();
}

function deleteGroup(id) {
    // console.log(id)
    // Add your actual group deletion logic here
    // console.log('Group deleted');
    axios.post('../server/group.php', {
        id: id,
        type: 'delete'
    })
        .then(function (response) {
            if (response.data.success) {
                alert('Group deleted...');
                window.location.href = "index.php";
            } else {
                alert('Error: ' + response.data.message);
            }
        })
        .catch(function (error) {
            console.error('An error occurred:', error);
            alert('An unexpected error occurred. Please try again.');
        });



    // // For demonstration - redirect after deletion
    // alert('Group deleted successfully!');
    // window.location.href = 'grouppage.html'; // Redirect to groups list
}

/// ======================
// INITIALIZATION
// ======================
document.addEventListener('DOMContentLoaded', function () {
    generateCalendar(currentMonth, currentYear);


    document.getElementById('prev-month').addEventListener('click', () => navigateMonth(-1));
    document.getElementById('next-month').addEventListener('click', () => navigateMonth(1));

    initSidebarToggle();
    // initFilesSection();
    // initMemberKick();
    // updateMemberCount();
    // checkAndUpdateEventStatuses();
    // initLessonFilePreview();
    // updateLessonsAccordion();

    // Image preview handlers
    document.querySelectorAll('.image-tile img').forEach(img => {
        img.addEventListener('click', () => openImagePreview(img));
    });

    document.getElementById('previewClose').addEventListener('click', closePreview);
    document.getElementById('previewPrev').addEventListener('click', showPrevImage);
    document.getElementById('previewNext').addEventListener('click', showNextImage);

    // File management buttons
    document.getElementById('addFileBtn').addEventListener('click', openFileUploadModal);
    document.getElementById('viewFilesBtn').addEventListener('click', openFileViewModal);
    // Set interval for checking event statuses
    setInterval(checkAndUpdateEventStatuses, 60000);
});
