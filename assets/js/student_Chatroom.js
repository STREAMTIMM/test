document.addEventListener('DOMContentLoaded', () => {
  initSidebarToggle();
  initChatInput();
  initFilesSection();
  initImagePreview(); 
  initFileDownloads();
  // initLinks();
  // initLessonFilePreviews();  
  // initMemberKick();
  // updateMemberCount();
  // initCreateGroupModal();

 let currentMonth = new Date().getMonth();
 let currentYear = new Date().getFullYear();
});

let galleryImages = [];
let currentImageIndex = 0;
const fileCategories = {
    images: [],
    files: [],
    links: []
  };


// ─────────────────────────────────────────────────────────
// Sidebar toggle (mobile)
// ─────────────────────────────────────────────────────────
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

  // Prevent clicks inside sidebars from bubbling
  sidebar.addEventListener('click', e => e.stopPropagation());
  rightSidebar.addEventListener('click', e => e.stopPropagation());
}

// ─────────────────────────────────────────────────────────
// Dynamic Chat Functionality with Improved File Attachments
// ─────────────────────────────────────────────────────────
function initChatInput() {
  const chatBox = document.querySelector('.chat-box');
  const chatInput = document.querySelector('.chat-input');
  const sendBtn = document.querySelector('.send-btn');
  const fileInput = document.getElementById('file-input');
  const chatInputContainer = document.querySelector('.chat-input-container');
  
  // Create attachments preview container
  const attachmentsPreview = document.createElement('div');
  attachmentsPreview.className = 'attachments-preview';
  chatInputContainer.insertBefore(attachmentsPreview, chatInput);
  
  let attachedFiles = [];

  // Send message function
  const sendMessage = () => {
      const messageText = chatInput.value.trim();
      
      if (messageText || attachedFiles.length > 0) {
          // Create message element
          const messageDiv = document.createElement('div');
          messageDiv.className = 'chat-message right';
          
          const contentDiv = document.createElement('div');
          
          // Sender name
          const senderName = document.createElement('div');
          senderName.className = 'sender-name';
          senderName.textContent = 'You';
          contentDiv.appendChild(senderName);
          
          // Text message
          if (messageText) {
              const textBubble = document.createElement('div');
              textBubble.className = 'message-bubble';
              textBubble.innerHTML = messageText.replace(/\n/g, '<br>');
              contentDiv.appendChild(textBubble);
          }
          
          // Attachments
          if (attachedFiles.length > 0) {
              const attachmentsDiv = document.createElement('div');
              attachmentsDiv.className = 'message-attachments';
              
              attachedFiles.forEach(file => {
                  if (file.type.startsWith('image/')) {
                      const attachmentDiv = document.createElement('div');
                      attachmentDiv.className = 'attachment-item';
                      attachmentDiv.innerHTML = `
                          <img src="${URL.createObjectURL(file)}" class="attachment-image">
                          <span class="attachment-name">${file.name}</span>
                      `;
                      attachmentsDiv.appendChild(attachmentDiv);
                  } else {
                      const attachmentDiv = document.createElement('div');
                      attachmentDiv.className = 'attachment-item';
                      attachmentDiv.innerHTML = `
                          <div class="file-icon">
                              <i class="fas ${getFileIcon(file)}"></i>
                          </div>
                          <div class="file-info">
                              <span class="attachment-name">${file.name}</span>
                              <small>${formatFileSize(file.size)}</small>
                          </div>
                      `;
                      attachmentsDiv.appendChild(attachmentDiv);
                  }
              });
              
              contentDiv.appendChild(attachmentsDiv);
          }
          if (attachedFiles.length > 0) {
            attachedFiles.forEach(file => {
              if (file.type.startsWith('image/')) {
                // For images
                const imageUrl = URL.createObjectURL(file);
                fileCategories.images.push({
                  url: imageUrl,
                  name: file.name,
                  date: new Date()
                });
              } else {
                // For other files
                fileCategories.files.push({
                  name: file.name,
                  size: file.size,
                  type: file.type,
                  date: new Date()
                });
              }
            });
            updateFilesSidebar(); // Update the sidebar
          }
          
       
          function updateFilesSidebar() {
            // Update Images Gallery
            const imagesGallery = document.getElementById('imagesGallery');
            if (imagesGallery) {
              // Clear existing content except month headers
              const monthHeaders = imagesGallery.querySelectorAll('.gallery-month');
              imagesGallery.innerHTML = '';
              monthHeaders.forEach(header => imagesGallery.appendChild(header));
              
              // Group images by month
              const imagesByMonth = groupByMonth(fileCategories.images);
              
              // Add images to gallery
              Object.entries(imagesByMonth).forEach(([month, images]) => {
                const monthHeader = document.createElement('div');
                monthHeader.className = 'gallery-month';
                monthHeader.textContent = month;
                imagesGallery.appendChild(monthHeader);
                
                const imageGrid = document.createElement('div');
                imageGrid.className = 'image-grid';
                
                images.forEach(image => {
                  const imageTile = document.createElement('div');
                  imageTile.className = 'image-tile';
                  imageTile.innerHTML = `<img src="${image.url}" alt="${image.name}">`;
                  imageGrid.appendChild(imageTile);
                });
                
                imagesGallery.appendChild(imageGrid);
              });
            }
          
            // Update Files Gallery
            const filesGallery = document.getElementById('filesGallery');
            if (filesGallery) {
              // Similar structure as images gallery
              const monthHeaders = filesGallery.querySelectorAll('.gallery-month');
              filesGallery.innerHTML = '';
              monthHeaders.forEach(header => filesGallery.appendChild(header));
              
              const filesByMonth = groupByMonth(fileCategories.files);
              
              Object.entries(filesByMonth).forEach(([month, files]) => {
                const monthHeader = document.createElement('div');
                monthHeader.className = 'gallery-month';
                monthHeader.textContent = month;
                filesGallery.appendChild(monthHeader);
                
                const fileGrid = document.createElement('div');
                fileGrid.className = 'file-grid';
                
                files.forEach(file => {
                  const fileTile = document.createElement('div');
                  fileTile.className = 'lesson-file-tile';
                  fileTile.dataset.file = file.name;
                  fileTile.innerHTML = `
                    <span class="file-download">${file.name}</span>
                    <span class="file-preview" title="Preview this file">↗️</span>
                  `;
                  fileGrid.appendChild(fileTile);
                });
                
                filesGallery.appendChild(fileGrid);
              });
              
              // Reinitialize file preview handlers
              initLessonFilePreviews();
            }
          }
          
          // Helper function to group items by month
          function groupByMonth(items) {
            const groups = {};
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                               'July', 'August', 'September', 'October', 'November', 'December'];
            
            items.forEach(item => {
              const date = item.date;
              const monthYear = `${monthNames[date.getMonth()]} ${date.getFullYear()}`;
              
              if (!groups[monthYear]) {
                groups[monthYear] = [];
              }
              groups[monthYear].push(item);
            });
            
            return groups;
          }
          
          // For links, add this to your sendMessage() function where you handle text messages
          if (messageText) {
            const linkMatches = messageText.match(/\bhttps?:\/\/\S+/gi);
            if (linkMatches) {
              linkMatches.forEach(link => {
                fileCategories.links.push({
                  url: link,
                  date: new Date(),
                  title: link // You might want to extract a better title
                });
              });
              updateFilesSidebar(); // Update the sidebar
            }
          }
          
          // Profile pic
          const profilePic = document.createElement('div');
          profilePic.className = 'profile-pic';
          
          messageDiv.appendChild(contentDiv);
          messageDiv.appendChild(profilePic);
          chatBox.appendChild(messageDiv);
          scrollToBottom();
          
          // Reset input
          chatInput.value = '';
          chatInput.style.height = 'auto';
          attachedFiles = [];
          fileInput.value = '';
          attachmentsPreview.innerHTML = '';
      }
  };

  // File input handler
  fileInput.addEventListener('change', (e) => {
      const newFiles = Array.from(e.target.files);
      if (newFiles.length) {
          attachedFiles = [...attachedFiles, ...newFiles];
          updateAttachmentsPreview();
      }
  });

  // Update attachments preview UI
  function updateAttachmentsPreview() {
      attachmentsPreview.innerHTML = '';
      
      attachedFiles.forEach((file, index) => {
          const previewDiv = document.createElement('div');
          previewDiv.className = 'attachment-preview-item';
          
          if (file.type.startsWith('image/')) {
              previewDiv.innerHTML = `
                  <img src="${URL.createObjectURL(file)}" class="preview-thumbnail">
                  <span class="preview-filename">${file.name}</span>
                  <button class="remove-attachment" data-index="${index}">
                      <i class="fas fa-times"></i>
                  </button>
              `;
          } else {
              previewDiv.innerHTML = `
                  <div class="preview-file-icon">
                      <i class="fas ${getFileIcon(file)}"></i>
                  </div>
                  <div class="preview-file-info">
                      <span class="preview-filename">${file.name}</span>
                      <small>${formatFileSize(file.size)}</small>
                  </div>
                  <button class="remove-attachment" data-index="${index}">
                      <i class="fas fa-times"></i>
                  </button>
              `;
          }
          
          attachmentsPreview.appendChild(previewDiv);
      });

      // Add remove attachment handlers
      document.querySelectorAll('.remove-attachment').forEach(btn => {
          btn.addEventListener('click', (e) => {
              e.stopPropagation();
              const index = parseInt(btn.dataset.index);
              attachedFiles.splice(index, 1);
              updateAttachmentsPreview();
          });
      });
  }

  // Enter key handling
  chatInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          sendMessage();
      }
  });

  // Send button click
  // sendBtn.addEventListener('click', sendMessage);

  // Auto-resize textarea
  chatInput.addEventListener('input', function() {
      this.style.height = 'auto';
      this.style.height = this.scrollHeight + 'px';
  });

  function scrollToBottom() {
      chatBox.scrollTop = chatBox.scrollHeight;
  }
}

// Helper functions for file handling
function getFileIcon(file) {
  const extension = file.name.split('.').pop().toLowerCase();
  const fileTypes = {
      pdf: 'fa-file-pdf',
      doc: 'fa-file-word',
      docx: 'fa-file-word',
      xls: 'fa-file-excel',
      xlsx: 'fa-file-excel',
      ppt: 'fa-file-powerpoint',
      pptx: 'fa-file-powerpoint',
      zip: 'fa-file-archive',
      rar: 'fa-file-archive',
      mp3: 'fa-file-audio',
      wav: 'fa-file-audio',
      mp4: 'fa-file-video',
      mov: 'fa-file-video',
      txt: 'fa-file-alt',
  };
  return fileTypes[extension] || 'fa-file';
}

function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// ─────────────────────────────────────────────────────────
// Files/Images/Links panel & tabs
// ─────────────────────────────────────────────────────────
function initFilesSection() {
  const tabs = document.querySelectorAll('.file-tabs .tab');
  const backBtn = document.getElementById('backBtn');
  const mainSidebar = document.getElementById('mainSidebarContent');
  const filesSection = document.getElementById('filesSection');

  // Click "Files" in accordion
  document.querySelector('[onclick="showFilesSection()"]')
      .addEventListener('click', () => {
          mainSidebar.classList.add('hidden');
          filesSection.classList.remove('hidden');
          showTab('images');
          return false;
      });

  // Back arrow
  backBtn.addEventListener('click', () => {
      filesSection.classList.add('hidden');
      mainSidebar.classList.remove('hidden');
  });

  // Tab clicks
  tabs.forEach(tab => {
      tab.addEventListener('click', () => {
          showTab(tab.textContent.toLowerCase());
      });
  });
}

function showTab(tabName) {
  const galleryMap = {
      images: 'imagesGallery',
      files: 'filesGallery',
      links: 'linksGallery'
  };

  // Toggle active state
  document.querySelectorAll('.file-tabs .tab')
      .forEach(tab => {
          tab.classList.toggle('active', tab.textContent.toLowerCase() === tabName);
      });

  // Hide all galleries
  Object.values(galleryMap).forEach(id => {
      document.getElementById(id).classList.add('hidden');
  });

  // Show the selected gallery
  const targetId = galleryMap[tabName];
  if (targetId) {
      document.getElementById(targetId).classList.remove('hidden');
  }
}

// ─────────────────────────────────────────────────────────
// Image preview modal
// ─────────────────────────────────────────────────────────
function initImagePreview() {
  // Event delegation for gallery images
  document.querySelector('.image-gallery')?.addEventListener('click', (e) => {
      const imgElement = e.target.closest('.image-tile img');
      if (imgElement) openImagePreview(imgElement);
  });

  // Modal controls
  document.getElementById('previewClose')?.addEventListener('click', closePreview);
  document.getElementById('previewPrev')?.addEventListener('click', showPrevImage);
  document.getElementById('previewNext')?.addEventListener('click', showNextImage);
  
  // Close modal when clicking outside
  document.getElementById('previewModal')?.addEventListener('click', (e) => {
      if (e.target.id === 'previewModal') closePreview();
  });
}

function openImagePreview(imgElement) {
  galleryImages = Array.from(document.querySelectorAll('.image-tile img')).map(img => img.src);
  currentImageIndex = galleryImages.indexOf(imgElement.src);
  
  if (currentImageIndex === -1) {
      console.error('Image not found in gallery!');
      return;
  }
  
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

// ─────────────────────────────────────────────────────────
// File downloads
// ─────────────────────────────────────────────────────────
function initFileDownloads() {
  document.querySelectorAll('.file-tile').forEach(tile => {
      tile.addEventListener('click', () => {
          const filename = tile.dataset.file;
          if (!filename) return;

          const link = document.createElement('a');
          link.href = `https://example.com/download/${filename}`;
          link.download = filename;
          document.body.appendChild(link);
          link.click();
          link.remove();
      });
  });
}

// ─────────────────────────────────────────────────────────
// Links
// ─────────────────────────────────────────────────────────
function initLinks() {
  document.querySelectorAll('.link-tile').forEach(tile => {
      tile.addEventListener('click', () => {
          const url = tile.dataset.url;
          if (url) window.open(url, '_blank');
      });
  });
}

// ─────────────────────────────────────────────────────────
// Lesson file previews
// ─────────────────────────────────────────────────────────
function initLessonFilePreviews() {
  document.querySelectorAll('.lesson-file-tile').forEach(tile => {
      const downloadEl = tile.querySelector('.file-download');
      const previewEl = tile.querySelector('.file-preview');

      if (downloadEl) {
          downloadEl.addEventListener('click', (e) => handleFileDownload(e, tile));
      }

      if (previewEl) {
          previewEl.addEventListener('click', (e) => handleFilePreview(e, tile));
      }
  });
}

function handleFileDownload(e, tile) {
  e.stopPropagation();
  const fileUrl = tile?.dataset.file;
  if (!fileUrl) return;

  const link = document.createElement('a');
  link.href = fileUrl;
  link.download = '';
  document.body.appendChild(link);
  link.click();
  link.remove();
}

function handleFilePreview(e, tile) {
  e.stopPropagation();
  const fileUrl = tile?.dataset.file;
  if (!fileUrl) return;

  const extension = fileUrl.split('.').pop().toLowerCase();
  const content = document.getElementById('filePreviewContent');
  const modal = document.getElementById('filePreviewModal');

  if (!content || !modal) return;

  if (['pdf'].includes(extension)) {
      content.innerHTML = `<iframe src="${fileUrl}" style="width:100%;height:80vh;" frameborder="0"></iframe>`;
  } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
      content.innerHTML = `<img src="${fileUrl}" style="max-width:100%; height:auto;">`;
  } else {
      content.innerHTML = `<p>Preview not available. <a href="${fileUrl}" download>Download file</a>.</p>`;
  }

  modal.style.display = 'block';
  document.body.style.overflow = 'hidden';
}

function closeFilePreview() {
  const modal = document.getElementById('filePreviewModal');
  if (modal) {
      modal.style.display = 'none';
      document.body.style.overflow = '';
  }
}
 


   


   


   function showDayDetails(day) {
       const dayDetailOverlay = document.getElementById("dayDetailOverlay");
       const eventDate = document.getElementById("eventDate");
       
       // Format the date
       const monthNames = ["January", "February", "March", "April", "May", "June", 
                          "July", "August", "September", "October", "November", "December"];
       const dateString = `${monthNames[currentMonth]} ${day}, ${currentYear}`;
       
       // Update the popup with the selected date
       eventDate.textContent = dateString;
       
       // Show the popup
       document.getElementById("popupOverlay").style.display = "none";
       dayDetailOverlay.style.display = "flex";
   }
    // Initialize all event detail popups
    document.addEventListener("DOMContentLoaded", () => {
        const dayDetailOverlay = document.getElementById("dayDetailOverlay");
        const dayDetailCloseBtn = document.getElementById("dayDetailCloseBtn");
        const dayAcceptedOverlay = document.getElementById("dayAcceptedOverlay");
        const dayAcceptedCloseBtn = document.getElementById("dayAcceptedCloseBtn");
        const dayDeclinedOverlay = document.getElementById("dayDeclinedOverlay");
        const dayDeclinedCloseBtn = document.getElementById("dayDeclinedCloseBtn");
        const dayAttendedOverlay = document.getElementById("dayAttendedOverlay");
        const dayAttendedCloseBtn = document.getElementById("dayAttendedCloseBtn");
        const dayDeclined2Overlay = document.getElementById("dayDeclined2Overlay");
        const dayDeclined2CloseBtn = document.getElementById("dayDeclined2CloseBtn");
        const dayCanceledOverlay = document.getElementById("dayCanceledOverlay");
        const dayCanceledCloseBtn = document.getElementById("dayCanceledCloseBtn");


        // Close buttons for all popups
        if (dayDetailCloseBtn) dayDetailCloseBtn.addEventListener("click", () => dayDetailOverlay.style.display = "none");
        if (dayAcceptedCloseBtn) dayAcceptedCloseBtn.addEventListener("click", () => dayAcceptedOverlay.style.display = "none");
        if (dayDeclinedCloseBtn) dayDeclinedCloseBtn.addEventListener("click", () => dayDeclinedOverlay.style.display = "none");
        if (dayAttendedCloseBtn) dayAttendedCloseBtn.addEventListener("click", () => dayAttendedOverlay.style.display = "none");
        if (dayDeclined2CloseBtn) dayDeclined2CloseBtn.addEventListener("click", () => dayDeclined2Overlay.style.display = "none");
        if (dayCanceledCloseBtn) dayCanceledCloseBtn.addEventListener("click", () => dayCanceledOverlay.style.display = "none");


        // Close when clicking outside
        if (dayDetailOverlay) dayDetailOverlay.addEventListener("click", (e) => { if (e.target === dayDetailOverlay) dayDetailOverlay.style.display = "none"; });
        if (dayAcceptedOverlay) dayAcceptedOverlay.addEventListener("click", (e) => { if (e.target === dayAcceptedOverlay) dayAcceptedOverlay.style.display = "none"; });
        if (dayDeclinedOverlay) dayDeclinedOverlay.addEventListener("click", (e) => { if (e.target === dayDeclinedOverlay) dayDeclinedOverlay.style.display = "none"; });
        if (dayAttendedOverlay) dayAttendedOverlay.addEventListener("click", (e) => { if (e.target === dayAttendedOverlay) dayAttendedOverlay.style.display = "none"; });
        if (dayDeclined2Overlay) dayDeclined2Overlay.addEventListener("click", (e) => { if (e.target === dayDeclined2Overlay) dayDeclined2Overlay.style.display = "none"; });
        if (dayCanceledOverlay) dayCanceledOverlay.addEventListener("click", (e) => { if (e.target === dayCanceledOverlay) dayCanceledOverlay.style.display = "none"; });


        // Accept button handler
        const acceptBtn = document.getElementById("acceptBtn");
        if (acceptBtn) {
            acceptBtn.addEventListener("click", () => {
                dayDetailOverlay.style.display = "none";
                dayAcceptedOverlay.style.display = "flex";


                // Transfer all data
                document.getElementById("eventDateAccepted").textContent = document.getElementById("eventDate").textContent;
                document.getElementById("eventTitleAccepted").textContent = document.getElementById("eventTitle").textContent;
                document.getElementById("eventLocationAccepted").textContent = document.getElementById("eventLocation").textContent;
                document.getElementById("leaderNoteAccepted").value = document.getElementById("leaderNote").value;
            });
        }


        // Decline button handler
        const declineBtn = document.querySelector(".decline-btn");
        if (declineBtn) {
            declineBtn.addEventListener("click", () => {
                dayDetailOverlay.style.display = "none";
                dayDeclinedOverlay.style.display = "flex";


                // Transfer all data
                document.getElementById("eventDateDeclined").textContent = document.getElementById("eventDate").textContent;
                document.getElementById("eventTitleDeclined").textContent = document.getElementById("eventTitle").textContent;
                document.getElementById("eventLocationDeclined").textContent = document.getElementById("eventLocation").textContent;
                document.getElementById("leaderNoteDeclined").value = document.getElementById("leaderNote").value;
            });
        }


        // Change button handlers
        const changeBtnAccepted = document.querySelector("#dayAcceptedOverlay .change-btn");
        if (changeBtnAccepted) {
            changeBtnAccepted.addEventListener("click", () => {
                dayAcceptedOverlay.style.display = "none";
                dayDetailOverlay.style.display = "flex";
            });
        }


        const changeBtnDeclined = document.querySelector("#dayDeclinedOverlay .change-btn");
        if (changeBtnDeclined) {
            changeBtnDeclined.addEventListener("click", () => {
                dayDeclinedOverlay.style.display = "none";
                dayDetailOverlay.style.display = "flex";
            });
        }
    });
     




   