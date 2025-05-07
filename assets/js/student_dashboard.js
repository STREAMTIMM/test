
        // Month names for display
        const monthNames = ["January", "February", "March", "April", "May", "June", 
            "July", "August", "September", "October", "November", "December"];

// Calendar functionality
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
const today = new Date();

// Sample events with the requested statuses
const events = {
2: { 
  type: 'done-declined', 
  title: 'Physics Study Group', 
  location: 'QCU Science Building', 
  note: 'Bring textbooks and calculators',
  time: '1:00 PM - 3:00 PM',
  response: 'declined'
},
15: { 
  type: 'done-attended', 
  title: 'Project Discussion', 
  location: 'Online', 
  note: 'Prepare your project updates',
  time: '3:00 PM - 5:00 PM',
  response: 'accepted'
},
29: { 
  type: 'pending', 
  title: 'Team Meeting', 
  location: 'QCU Room 101', 
  note: 'Monthly team sync',
  time: '2:00 PM - 3:00 PM',
  response: null
},
21: { 
  type: 'canceled', 
  title: 'Study Group', 
  location: 'QCU Cafeteria', 
  note: 'Cancelled due to room unavailability',
  time: '1:00 PM - 2:00 PM',
  response: null
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
      const event = events[day];
      
      // Determine the class based on event type and response
      if (event.type === 'pending') {
          if (event.response === 'declined') {
              dayElement.classList.add("declined");
          } else {
              dayElement.classList.add("pending");
          }
      } else if (event.type === 'done-attended') {
          dayElement.classList.add("done-attended");
      } else if (event.type === 'done-declined') {
          dayElement.classList.add("done-declined");
      } else if (event.type === 'canceled') {
          dayElement.classList.add("canceled");
      }
      
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
const formattedDate = `${monthNames[month]} ${day}, ${year}`;

// Hide all views first
document.getElementById("pendingView").style.display = "none";
document.getElementById("declinedView").style.display = "none";
document.getElementById("doneAttendedView").style.display = "none";
document.getElementById("doneDeclinedView").style.display = "none";
document.getElementById("canceledView").style.display = "none";
document.getElementById("changeResponseForm").style.display = "none";

// Show appropriate view based on event status
if (event.type === 'pending') {
  if (event.response === 'declined') {
      // Show declined view for pending events that were declined
      showDeclinedView(event, formattedDate);
  } else {
      // Show pending view (with accept/decline buttons)
      showPendingView(event, formattedDate);
  }
} else if (event.type === 'done-attended') {
  showDoneAttendedView(event, formattedDate);
} else if (event.type === 'done-declined') {
  showDoneDeclinedView(event, formattedDate);
} else if (event.type === 'canceled') {
  showCanceledView(event, formattedDate);
}

document.getElementById("eventModal").style.display = "block";
document.getElementById("modalOverlay").style.display = "block";
}

function showPendingView(event, formattedDate) {
const view = document.getElementById("pendingView");
view.style.display = "block";

// Populate event details
document.getElementById("eventDate").textContent = formattedDate;
document.getElementById("eventTime").textContent = event.time || 'No time specified';
document.getElementById("eventTitle").textContent = event.title || 'No title';
document.getElementById("eventLocation").textContent = event.location || 'No location';
document.getElementById("eventNotes").textContent = event.note || 'No additional notes';
}

function showDeclinedView(event, formattedDate) {
// Hide all other views
document.getElementById("pendingView").style.display = "none";
document.getElementById("acceptedView").style.display = "none";
document.getElementById("doneAttendedView").style.display = "none";
document.getElementById("doneDeclinedView").style.display = "none";
document.getElementById("canceledView").style.display = "none";
document.getElementById("changeResponseForm").style.display = "none";
document.getElementById("changeResponseFormFromAccepted").style.display = "none";

// Show declined view
const view = document.getElementById("declinedView");
view.style.display = "block";

// Populate event details
document.getElementById("declinedEventDate").textContent = formattedDate;
document.getElementById("declinedEventTime").textContent = event.time || 'No time specified';
document.getElementById("declinedEventTitle").textContent = event.title || 'No title';
document.getElementById("declinedEventLocation").textContent = event.location || 'No location';
document.getElementById("declinedEventNotes").textContent = event.note || 'No additional notes';

// Set appropriate status badge
const statusBadge = view.querySelector('.status-badge');
if (event.type === 'done-declined') {
statusBadge.className = 'status-badge status-done-declined';
statusBadge.textContent = 'Done & Declined';
} else {
statusBadge.className = 'status-badge status-declined';
statusBadge.textContent = 'Declined';
}
}

function showDoneAttendedView(event, formattedDate) {
const view = document.getElementById("doneAttendedView");
view.style.display = "block";

// Populate event details
document.getElementById("doneAttendedEventDate").textContent = formattedDate;
document.getElementById("doneAttendedEventTime").textContent = event.time || 'No time specified';
document.getElementById("doneAttendedEventTitle").textContent = event.title || 'No title';
document.getElementById("doneAttendedEventLocation").textContent = event.location || 'No location';
document.getElementById("doneAttendedEventNotes").textContent = event.note || 'No additional notes';
}

function showDoneDeclinedView(event, formattedDate) {
const view = document.getElementById("doneDeclinedView");
view.style.display = "block";

// Populate event details
document.getElementById("doneDeclinedEventDate").textContent = formattedDate;
document.getElementById("doneDeclinedEventTime").textContent = event.time || 'No time specified';
document.getElementById("doneDeclinedEventTitle").textContent = event.title || 'No title';
document.getElementById("doneDeclinedEventLocation").textContent = event.location || 'No location';
document.getElementById("doneDeclinedEventNotes").textContent = event.note || 'No additional notes';
}

function showCanceledView(event, formattedDate) {
const view = document.getElementById("canceledView");
view.style.display = "block";

// Populate event details
document.getElementById("canceledEventDate").textContent = formattedDate;
document.getElementById("canceledEventTime").textContent = event.time || 'No time specified';
document.getElementById("canceledEventTitle").textContent = event.title || 'No title';
document.getElementById("canceledEventLocation").textContent = event.location || 'No location';
document.getElementById("canceledEventNotes").textContent = event.note || 'No additional notes';
}

function showChangeResponseForm() {
document.getElementById("declinedView").style.display = "none";
document.getElementById("changeResponseForm").style.display = "block";
}

function closeChangeResponseForm() {
document.getElementById("changeResponseForm").style.display = "none";
document.getElementById("declinedView").style.display = "block";
}

function showAcceptedView(event, formattedDate) {
// Hide all views first
document.getElementById("pendingView").style.display = "none";
document.getElementById("declinedView").style.display = "none";
document.getElementById("doneAttendedView").style.display = "none";
document.getElementById("doneDeclinedView").style.display = "none";
document.getElementById("canceledView").style.display = "none";
document.getElementById("changeResponseForm").style.display = "none";
document.getElementById("changeResponseFormFromAccepted").style.display = "none";

// Show accepted view
document.getElementById("acceptedView").style.display = "block";

// Populate event details
document.getElementById("acceptedEventDate").textContent = formattedDate;
document.getElementById("acceptedEventTime").textContent = event.time || 'No time specified';
document.getElementById("acceptedEventTitle").textContent = event.title || 'No title';
document.getElementById("acceptedEventLocation").textContent = event.location || 'No location';
document.getElementById("acceptedEventNotes").textContent = event.note || 'No additional notes';

// Show appropriate status message
const eventDate = new Date(currentEventYear, currentEventMonth, currentEventDay);
if (eventDate < today) {
document.getElementById("acceptedView").querySelector(".status-acknowledged").innerHTML = 
'<div class="status-badge status-done-attended">Done & Attended</div>';
} else {
document.getElementById("acceptedView").querySelector(".status-acknowledged").innerHTML = 
'You\'ve accepted this event';
}
}

function showChangeResponseFormFromAccepted() {
document.getElementById("acceptedView").style.display = "none";
document.getElementById("changeResponseFormFromAccepted").style.display = "block";
}

function closeChangeResponseFormFromAccepted() {
document.getElementById("changeResponseFormFromAccepted").style.display = "none";
document.getElementById("acceptedView").style.display = "block";
}

function declineFromAccepted() {
const event = events[currentEventDay];
if (event) {
event.response = 'declined';

// Update the event type based on date
const eventDate = new Date(currentEventYear, currentEventMonth, currentEventDay);
if (eventDate < today) {
event.type = 'done-declined'; // Past event
} else {
event.type = 'pending'; // Future event with declined response
}

// Close the change response form and show declined view
closeChangeResponseFormFromAccepted();
const formattedDate = `${monthNames[currentEventMonth]} ${currentEventDay}, ${currentEventYear}`;
showDeclinedView(event, formattedDate);

// Refresh calendar to show updated status
generateCalendar(currentMonth, currentYear);
}
}




function acceptEvent() {
const event = events[currentEventDay];
if (event) {
event.response = 'accepted';
const formattedDate = `${monthNames[currentEventMonth]} ${currentEventDay}, ${currentEventYear}`;
showAcceptedView(event, formattedDate);

// If event is in the past, mark as done-attended
const eventDate = new Date(currentEventYear, currentEventMonth, currentEventDay);
if (eventDate < today) {
event.type = 'done-attended';
generateCalendar(currentMonth, currentYear);
}
}
}

function declineEvent() {
const event = events[currentEventDay];
if (event) {
  event.response = 'declined';
  // If event is in the past, mark as done-declined
  const eventDate = new Date(currentEventYear, currentEventMonth, currentEventDay);
  if (eventDate < today) {
      event.type = 'done-declined';
  }
  generateCalendar(currentMonth, currentYear);
  closeModal();
}
}

function acceptFromChange() {
const event = events[currentEventDay];
if (event) {
event.response = 'accepted';

// If event is in the past, mark as done-attended
const eventDate = new Date(currentEventYear, currentEventMonth, currentEventDay);
if (eventDate < today) {
event.type = 'done-attended';
}

generateCalendar(currentMonth, currentYear);

// Show the accepted view immediately
const formattedDate = `${monthNames[currentEventMonth]} ${currentEventDay}, ${currentEventYear}`;
showAcceptedView(event, formattedDate);
}
}

function closeModal() {
document.getElementById("eventModal").style.display = "none";
document.getElementById("modalOverlay").style.display = "none";
}

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

function openReportModal(month, year) {
// Set the report month
document.getElementById("reportMonthYear").textContent = `${monthNames[month]} ${year}`;

// Get meetings done in specified month
const doneMeetings = [];
for (const [day, event] of Object.entries(events)) {
  if (event.type === 'done-attended' || event.type === 'done-declined') {
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

// Store current event details for modal operations
let currentEventDay = null;
let currentEventMonth = null;
let currentEventYear = null;

let currentGroupToJoin = null;

function showJoinGroupModal(groupName) {
currentGroupToJoin = groupName;
document.getElementById("joinGroupQuestion").textContent = groupName.toUpperCase();
document.getElementById("joinGroupModalOverlay").classList.remove("hidden");
document.getElementById("joinGroupModal").classList.remove("hidden");
}

function joinGroup(confirmJoin) {
document.getElementById("joinGroupModalOverlay").classList.add("hidden");
document.getElementById("joinGroupModal").classList.add("hidden");

if (confirmJoin && currentGroupToJoin) {
document.getElementById("joinedMessage").textContent = currentGroupToJoin.toUpperCase();
document.getElementById("joinedModalOverlay").classList.remove("hidden");
document.getElementById("joinedModal").classList.remove("hidden");
}

currentGroupToJoin = null;
}

function closeJoinedModal() {
document.getElementById("joinedModalOverlay").classList.add("hidden");
document.getElementById("joinedModal").classList.add("hidden");
}
