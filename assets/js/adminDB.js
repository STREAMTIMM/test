const monthNames =
     ["January", "February", "March", "April", "May", "June", 
    "July", "August", "September", "October", "November", "December"];

// Calendar functionality
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    const today = new Date();

// Sample events with the requested statuses
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

// Initialize calendar
    document.addEventListener('DOMContentLoaded', function() {
    generateCalendar(currentMonth, currentYear);
    generateReportCards();

// Month navigation
    document.getElementById('prev-month').addEventListener('click', function() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    generateCalendar(currentMonth, currentYear);
    });

    document.getElementById('next-month').addEventListener('click', function() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    generateCalendar(currentMonth, currentYear);
    });

// Burger menu functionality
    document.getElementById('burgerMenu').addEventListener('click', function(e) {
    e.stopPropagation(); // Prevent the click from bubbling
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('mainContent').classList.toggle('expanded');
    });

// Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const burgerMenu = document.getElementById('burgerMenu');

    if (window.innerWidth <= 991.98) {
    // If sidebar is shown and click is outside of it
    if (sidebar.classList.contains('show') && 
    !sidebar.contains(event.target) && 
    event.target !== burgerMenu) {
    sidebar.classList.remove('show');
    document.getElementById('mainContent').classList.remove('expanded');
    }
    }
    });
    });

    function generateCalendar(month, year) {
    const calendar = document.getElementById("calendar");
    const monthYear = document.getElementById("month-year");
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

// Set month and year title
    monthYear.innerText = `${monthNames[month]} ${year}`;

// Clear previous calendar
    calendar.innerHTML = "";

// Add days of the month in sequential order
    for (let day = 1; day <= daysInMonth; day++) {
    const dayElement = document.createElement("div");
    dayElement.classList.add("calendar-day");
    dayElement.innerText = day;

// Highlight today
    if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
    dayElement.classList.add("today");
    }

// Add event colors if the day has an event
    if (events[day]) {
    dayElement.classList.add(events[day].type);
    dayElement.addEventListener('click', (function(day, month, year) {
    return function() {
        openModal(day, month, year);
    };
    })(day, month, year));
    }

    calendar.appendChild(dayElement);
    }
    }
    function openModal(day, month, year) {
    currentEventDay = day;
    currentEventMonth = month;
    currentEventYear = year;

    const event = events[day] || {};

    const monthNames = ["January", "February", "March", "April", "May", "June", 
            "July", "August", "September", "October", "November", "December"];
    const formattedDate = `${monthNames[month]} ${day}, ${year}`;

// Hide all views first
    document.getElementById("pendingView").style.display = "none";
    document.getElementById("doneView").style.display = "none";
    document.getElementById("canceledView").style.display = "none";
    document.getElementById("changeForm").style.display = "none";
    document.getElementById("confirmView").style.display = "none";

// Clear main event details section
    document.getElementById("eventDate").textContent = "";
    document.getElementById("eventTime").textContent = "";
    document.getElementById("eventTitle").textContent = "";
    document.getElementById("eventLocation").textContent = "";
    document.getElementById("eventNotes").textContent = "";


    event.attendees = event.attendees || [];
    event.declined = event.declined || [];
    event.notResponded = event.notResponded || [];

// Show appropriate modal view
    if (event.type === 'pending') {
    showPendingView(event);
    } else if (event.type === 'done') {

// Only populate main event details for done events
    document.getElementById("eventDate").textContent = formattedDate;
    document.getElementById("eventTime").textContent = event.time || '2:00 PM - 4:00 PM';
    document.getElementById("eventTitle").textContent = event.title || 'No title';
    document.getElementById("eventLocation").textContent = event.location || 'No location';
    document.getElementById("eventNotes").textContent = event.note || 'No notes';
    showDoneView(event);
    } else if (event.type === 'canceled') {

// Only populate main event details for canceled events
    document.getElementById("eventDate").textContent = formattedDate;
    document.getElementById("eventTime").textContent = event.time || '2:00 PM - 4:00 PM';
    document.getElementById("eventTitle").textContent = event.title || 'No title';
    document.getElementById("eventLocation").textContent = event.location || 'No location';
    document.getElementById("eventNotes").textContent = event.note || 'No notes';
    showCanceledView(day, month, year);
    }

    document.getElementById("eventModal").style.display = "block";
    document.getElementById("modalOverlay").style.display = "block";
    }
    function showPendingView(event) {
    const view = document.getElementById("pendingView");
    view.style.display = "block";

// Populate only the pending event details
    const monthNames = ["January", "February", "March", "April", "May", "June", 
            "July", "August", "September", "October", "November", "December"];
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

// Populate with event data
    document.getElementById("attendedCount").textContent = (event.attendees || []).length;
    document.getElementById("notAttendedCount").textContent = 
    (event.declined || []).length + (event.notResponded || []).length;
    }

    function showCanceledView(day, month, year) {
    const view = document.getElementById("canceledView");
    view.style.display = "block";

// Show change button only if event is in the future
    const eventDate = new Date(year, month, day);
    document.getElementById("changeCanceledBtn").style.display = 
    eventDate > today ? "inline-block" : "none";
    }
    function showChangeForm() {
    const event = events[currentEventDay] || {};

// Populate the form with current event data
    document.getElementById("changeDate").value = `${currentYear}-${String(currentEventMonth + 1).padStart(2, '0')}-${String(currentEventDay).padStart(2, '0')}`;
    document.getElementById("changeTime").value = event.time || '14:00 - 16:00';
    document.getElementById("changeWhat").value = event.title || '';
    document.getElementById("changeWhere").value = event.location || '';
    document.getElementById("changeNotes").value = event.note || '';

// Show the form and hide other views
    document.getElementById("pendingView").style.display = "none";
    document.getElementById("doneView").style.display = "none";
    document.getElementById("canceledView").style.display = "none";
    document.getElementById("changeForm").style.display = "block";
    document.getElementById("confirmView").style.display = "none";
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
    const event = events[oldEventDay] || {};

// Get new date and parse it
    const newDate = new Date(document.getElementById("changeDate").value);
    const newDay = newDate.getDate();
    const newMonth = newDate.getMonth();
    const newYear = newDate.getFullYear();

// Update event properties
    event.title = document.getElementById("changeWhat").value;
    event.location = document.getElementById("changeWhere").value;
    event.note = document.getElementById("changeNotes").value;
    event.time = document.getElementById("changeTime").value;

// If date changed, move the event to new date
    if (newDay !== oldEventDay || newMonth !== currentEventMonth || newYear !== currentYear) {
// Remove from old date
    delete events[oldEventDay];

// Add to new date
    events[newDay] = event;

// Update current event tracking
    currentEventDay = newDay;
    currentEventMonth = newMonth;
    currentEventYear = newYear;
    }

// If event was canceled, change to pending (green)
    if (event.type === 'canceled') {
    event.type = 'pending';
    }


    event.attendees = event.attendees || [];
    event.declined = event.declined || [];
    event.notResponded = event.notResponded || [];


    generateCalendar(currentMonth, currentYear);
    closeModal();
}
    function cancelEvent() {
    const event = events[currentEventDay] || {};
    event.type = 'canceled';
    events[currentEventDay] = event;

    generateCalendar(currentMonth, currentYear);
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

    function closeModal() {
    document.getElementById("eventModal").style.display = "none";
    document.getElementById("modalOverlay").style.display = "none";
    }

// Store current event details for modal operations
    let currentEventDay = null;
    let currentEventMonth = null;
    let currentEventYear = null;


function openCreateGroupModal() {
    document.getElementById("createGroupModalOverlay").style.display = "block";
    document.getElementById("createGroupModal").style.display = "block";
}

function closeCreateGroupModal() {
    document.getElementById("createGroupModalOverlay").style.display = "none";
    document.getElementById("createGroupModal").style.display = "none";
// Clear the form when closing
    document.getElementById("groupName").value = "";
    document.getElementById("memberCount").value = "10";
}

function createNewGroup() {
    const groupName = document.getElementById("groupName").value.trim();
    const memberCount = document.getElementById("memberCount").value;

    if (!groupName) {
    alert("Please enter a group name");
    return;
    }

// Create the new group card
    const discoverContainer = document.querySelector(".discover-container");
    const newGroupCard = document.createElement("div");
    newGroupCard.className = "group-card text-decoration-none";
    newGroupCard.style.position = "relative";

// Create the anchor tag for the main card content
    const cardLink = document.createElement("a");
    cardLink.href = "grouppage.html";
    cardLink.style.display = "flex";
    cardLink.style.alignItems = "center";
    cardLink.style.justifyContent = "center";
    cardLink.style.textAlign = "center";
    cardLink.style.width = "100%";
    cardLink.style.height = "100%";
    cardLink.style.color = "inherit";
    cardLink.style.textDecoration = "none";
    cardLink.textContent = groupName;

// Create the three-dots menu
    const menuButton = document.createElement("button");
    menuButton.className = "group-menu";
    menuButton.innerHTML = "&#8942;"; // Three dots icon
    menuButton.onclick = function(e) {
    e.stopPropagation();
    e.preventDefault();
    showGroupMenu(this);
    };

// Create the menu options
    const menuOptions = document.createElement("div");
    menuOptions.className = "group-menu-options";
    menuOptions.innerHTML = `
    <button class="delete-option" onclick="initiateDelete(event, this)">Delete Group</button>
    `;

    newGroupCard.appendChild(cardLink);
    newGroupCard.appendChild(menuButton);
    newGroupCard.appendChild(menuOptions);

// Add hover effect
    newGroupCard.addEventListener("mouseenter", function() {
    this.style.backgroundColor = "#f4e1c6";
    this.style.transform = "scale(1.05)";
    });

    newGroupCard.addEventListener("mouseleave", function() {
    this.style.backgroundColor = "var(--card-bg)";
    this.style.transform = "scale(1)";
    });

// Insert the new card at the beginning of the container
    discoverContainer.insertBefore(newGroupCard, discoverContainer.firstChild);

// Close the modal
    closeCreateGroupModal();
    }
// Track which group is being deleted
    let groupToDelete = null;

    function showGroupMenu(button) {
// Hide any other open menus first
    document.querySelectorAll('.group-menu-options').forEach(menu => {
    if (menu !== button.nextElementSibling) {
    menu.style.display = 'none';
    }
    });

// Toggle the clicked menu
    const menu = button.nextElementSibling;
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';

// Close menu when clicking elsewhere
    const clickHandler = function(e) {
    if (!menu.contains(e.target) && e.target !== button) {
    menu.style.display = 'none';
    document.removeEventListener('click', clickHandler);
    }
    };

    document.addEventListener('click', clickHandler);
    }

    function initiateDelete(event, button) {
    event.stopPropagation();
    event.preventDefault();

// Find the group card element
    groupToDelete = button.closest('.group-card');

// Show confirmation box
    document.getElementById('confirmationOverlay').style.display = 'block';
    document.getElementById('confirmationBox').style.display = 'block';
    }

    function confirmDelete() {
    if (groupToDelete) {
    groupToDelete.remove();
    }
    cancelDelete();
    }

    function cancelDelete() {
    document.getElementById('confirmationOverlay').style.display = 'none';
    document.getElementById('confirmationBox').style.display = 'none';
    groupToDelete = null;
    }
// Generate report cards for previous months
    function generateReportCards() {
    const reportContainer = document.getElementById("reportContainer");
    reportContainer.innerHTML = "";

    const currentDate = new Date();
    const currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();

// Show reports for previous 3 months
    for (let i = 1; i <= 3; i++) {
    const reportDate = new Date(currentYear, currentMonth - i, 1);
    if (reportDate.getFullYear() < currentYear && currentMonth - i < 0) {
    break; // Don't show reports from previous years in this example
    }

    const monthName = monthNames[reportDate.getMonth()];
    const year = reportDate.getFullYear();

    const reportCard = document.createElement("div");
    reportCard.className = "report-card";
    reportCard.innerHTML = `<span class="report-title">${monthName}</span>`;
    reportCard.onclick = function() {
    openReportModal(reportDate.getMonth(), year);
    };

    reportContainer.appendChild(reportCard);
    }
    }

// Modified openReportModal to accept month and year parameters
    function openReportModal(month, year) {
// Set the report month
    document.getElementById("reportMonthYear").textContent = `${monthNames[month]} ${year}`;

// Get meetings done in specified month
    const doneMeetings = [];
    for (const [day, event] of Object.entries(events)) {
    if (event.type === 'done') {
    const eventDate = new Date(year, month, parseInt(day));
    if (eventDate.getMonth() === month && eventDate.getFullYear() === year) {
    doneMeetings.push(event.title);
    }
    }
    }   

// Display meetings done
    const meetingsList = document.getElementById("meetingsDoneList");
    if (doneMeetings.length > 0) {
    meetingsList.innerHTML = doneMeetings.map(meeting => 
    `<div>${meeting}</div>`).join('');
    } else {
    meetingsList.innerHTML = '<div>No meetings completed</div>';
    }

// Display materials uploaded (currently none)
    document.getElementById("materialsUploadedList").innerHTML = 
    '<div>No materials uploaded</div>';

// Display total groups created (currently none)
    document.getElementById("totalGroupsCreated").innerHTML = 
    '<div>No groups created</div>';

// Show the modal
    document.getElementById("reportModalOverlay").style.display = "block";
    document.getElementById("reportModal").style.display = "block";
}
function closeReportModal() {
    document.getElementById('reportModalOverlay').style.display = 'none';
    document.getElementById('reportModal').style.display = 'none';
    }
