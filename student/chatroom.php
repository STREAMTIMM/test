<?php
include 'header.php';

if (!isset($_GET['group_id'])) {
    echo "<script>window.location.href = '../';</script>";
    exit;
}

$id = $_GET['group_id'];

// Fetch the group
$data = $db->fetchOne("SELECT * FROM `groups` WHERE id = :id AND status = 1", ['id' => $id]);
$memberCount = 0;
$count = $db->fetchOne("SELECT COUNT(id) as count FROM `groups` WHERE id = :id AND status = 1", ['id' => $id])['count'];

if ($count) {
    $memberCount = $count;
}
// If group not found, redirect
if (!$data) {
    echo "<script>alert('Group not found or inactive.'); window.location.href = '../';</script>";
    exit;
}
$group_id = $id;

$isJoined = $db->fetchOne("SELECT * FROM `student_groups` WHERE user_id = :user_id AND group_id = :group_id  AND status = 1", [
    'user_id' => $user_id,
    'group_id' => $id
]);

if (!$isJoined) {
    echo "<script>alert('Group not found or inactive.'); window.location.href = '../';</script>";
    exit;
}

$members = $db->fetchAll("
    SELECT a.*,b.name as user_name FROM student_groups AS a
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
<script src="../assets/js/student_Chatroom.js"></script>
<link rel="stylesheet" href="../assets/css/student_chatroom.css">
<!-- Main Content -->
<main>
    <div class="chat-container">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="top-bar">
                <div class="top-bar-content">
                    <!-- Mobile Menu Buttons -->
                    <button class="mobile-menu-btn" id="leftMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>

                    <!-- Group Info - Wrapped in a container -->
                    <div class="group-info-container">
                        <img src="../assets/images/group.webp" alt="Group Logo" class="group-logo">
                        <div class="group-info">
                            <span class="group-name">
                                <h2><?= $name ?></h2>
                            </span>
                            <span class="member-count" id="memberCount"><?= $memberCount ?> members</span>
                        </div>


                    </div>

                    <button class="mobile-menu-btn" id="rightMenuBtn">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>


                </div>

            </div>
        </div>

        <!-- CHAT MESSAGES -->
        <div class="chat-box">
            <!-- Left Message -->
            <div class="chat-message left">
                <div class="profile-pic"></div>
                <div>
                    <div class="sender-name">Llysa Batumbazzkal</div>
                    <div class="message-bubble">
                        Good day everyone! <br />
                        I am Llysa Batumbakal the overseer for this group lead by College
                        of Computer Studies to help our fellow classmates understand
                        CALC101. <br /><br />
                        Gorabells may kwento aksikis gorabells ang lola nyey para mag
                        bayad
                    </div>
                </div>
            </div>

            <!-- Right Message -->
            <div class="chat-message right">
                <div>
                    <div class="sender-name"><?= $user_name ?></div>
                    <div class="message-bubble">noted!</div>
                </div>
                <div class="profile-pic"></div>
            </div>
        </div>
        <!-- input area -->

        <div class="chat-input-area">
            <button class="attach-btn" aria-label="Attach File" onclick="document.getElementById('file-input').click()">
                <i class="fas fa-paperclip"></i>
            </button>

            <!-- Hidden file input -->
            <input
                type="file"
                id="file-input"
                accept="image/*"
                style="display: none;"
                onchange="handleFileSelect(this)">

            <div class="chat-input-container">
                <textarea
                    class="chat-input"
                    placeholder="Type your message..."
                    rows="1"
                    style="white-space: pre-wrap; word-wrap: break-word;"></textarea>
            </div>

            <button class="send-btn" aria-label="Send Message">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>


        <!-- Right Sidebar -->
        <aside class="right-sidebar" id="right-sidebar">
            <!-- Files Section (hidden by default) -->
            <div class="files-section hidden" id="filesSection">
                <div class="files-header">
                    <button id="backBtn" class="back-btn">
                        <span class="back-icon">
                            <i class="fas fa-arrow-left"></i>
                        </span>
                        <span class="back-text">Back to Group</span>
                    </button>
                    <div class="file-tabs">
                        <button class="tab active">Images</button>
                        <!-- <button class="tab">Files</button> -->
                        <!-- <button class="tab">Links</button> -->
                    </div>
                </div>

                <div class="image-gallery" id="imagesGallery">
                    <?php
                    foreach ($group_data_images as $key => $value) {
                        // Get the formatted month (e.g., "April 2024")
                        $month = $value['month'];
                        // Get the concatenated file names (e.g., "file1.jpg,file2.jpg")
                        $files = explode(',', $value['files']); // Convert the concatenated file names into an array

                        echo '
                        <div class="gallery-month">' . $month . '</div>
                        <div class="image-grid">';

                        // Loop through each file and display it as an image tile
                        foreach ($files as $file) {
                            echo '
                            <div class="image-tile">
                                <img src="' . htmlspecialchars($file) . '" alt="Gallery image">
                            </div>';
                        }

                        echo '</div>';
                    }
                    ?>

                </div>

                <div class="files-gallery hidden" id="filesGallery">

                    <?php
                    foreach ($group_data_images as $key => $value) {
                        // Get the formatted month (e.g., "April 2024")
                        $month = $value['month'];
                        // Get the concatenated file names (e.g., "file1.jpg,file2.jpg")
                        $files = explode(',', $value['files']);


                        echo '
                         <div class="gallery-month">' . htmlspecialchars($month) . '</div>
                            <div class="file-grid">';

                        foreach ($files as $file) {
                            $filename = basename($file);

                            echo '<div class="lesson-file-tile" data-file="' . htmlspecialchars($file) . '">
                            <span class="file-download">' . htmlspecialchars($filename) . '</span>
                            <span class="file-preview" title="Preview this file">↗️</span>
                        </div> ';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>

                <div class="links-gallery hidden" id="linksGallery">

                    <?php
                    foreach ($group_data_images as $key => $value) {
                        // Get the formatted month (e.g., "April 2024")
                        $month = $value['month'];
                        // Get the concatenated file names (e.g., "file1.jpg,file2.jpg")
                        $files = explode(',', $value['files']);


                        echo '
                         <div class="gallery-month">' . htmlspecialchars($month) . '</div>
                            <div class="file-grid">';

                        foreach ($files as $file) {
                            $filename = basename($file);

                            echo ' <div class="link-tile" data-url="' . htmlspecialchars($month) . '">
                            <i class="fas fa-link"></i>
                            <span>Helpful Resource</span>
                        </div>';
                        }
                        echo '</div>';
                    }
                    ?>

                    <!-- <div class="gallery-month">April 2024</div>
                    <div class="link-grid">
                        <div class="link-tile" data-url="https://example.com/resource1">
                            <i class="fas fa-link"></i>
                            <span>Helpful Resource</span>
                        </div>
                        <div class="link-tile" data-url="https://example.com/tutorial">
                            <i class="fas fa-link"></i>
                            <span>Video Tutorial</span>
                        </div>
                    </div>

                    <div class="gallery-month">March 2024</div>
                    <div class="link-grid">
                        <div class="link-tile" data-url="https://example.com/old-resource">
                            <i class="fas fa-link"></i>
                            <span>Reference Material</span>
                        </div>
                    </div> -->
                </div>
            </div>


            <!-- Main Sidebar Content -->
            <div class="right-sidebar-content" id="mainSidebarContent">
                <!-- Profile Section -->
                <div class="profile-section">
                    <img src="../assets/images/group.webp" alt="Group Image" class="profile-img">
                    <h2 class="text-white"><?= $name ?></h2>
                    <h6 class="text-muted">Group Admin:</h6>
                    <h4 class="text-white">🔑Llysa Batumbakal</h4>
                </div>

                <!-- Accordion -->
                <div class="accordion" id="groupAccordion">
                    <!-- Members Accordion Item -->
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#membersCollapse">
                                Members
                            </button>
                        </h2>
                        <div id="membersCollapse" class="accordion-collapse collapse" data-bs-parent="#groupAccordion">
                            <div class="accordion-body member-list">

                                <?php
                                foreach ($members as $key => $value) {
                                    echo '
                                   <div class="member-item">
                                    <span>' . $value['user_name'] . '</span>
                                </div>
                                   ';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Files Accordion Item -->
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filesCollapse" onclick="showFilesSection()">
                                Images
                            </button>
                        </h2>
                    </div>



                    <!-- Lessons Accordion Item -->
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lessonsCollapse">
                                Lessons
                            </button>
                        </h2>
                        <div id="lessonsCollapse" class="accordion-collapse collapse" data-bs-parent="#groupAccordion">
                            <div class="accordion-body">
                                <?php
                                foreach ($lessonfiles as $key => $value) {
                                    // Get the formatted month (e.g., "April 2024")
                                    $month = $value['month'];
                                    // Get the concatenated file names (e.g., "file1.jpg,file2.jpg")
                                    $files = explode(',', $value['files']);

                                    foreach ($files as $file) {
                                        $filename = basename($file);

                                        echo '
                                                <div class="lesson-file-tile" data-file="' . htmlspecialchars($file) . '">
                                                    <span class="file-download">' . htmlspecialchars($filename) . '</span>
                                                    <span class="file-preview" title="Preview this file">↗️</span>
                                                </div>
                                        
                                        ';
                                    }
                                }
                                ?>


                            </div>
                        </div>
                    </div>
                    <!-- leave group -->
                    <button type="button"
                        <?php
                        echo 'onclick="leaveGroup(\'' . htmlspecialchars(addslashes($name)) . '\', ' . intval($group_id) . ')"'
                        ?>
                        id="buttonLeave" class="btn btn-danger ">Leave Group</button>



                    <!-- Preview Modal -->
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




        </aside>




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







        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        </body>

        </html>
        <script>
            function leaveGroup(groupName, groupId) {
                Swal.fire({
                    title: 'Leave Group?',
                    text: `Do you want to Leave "${groupName}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Leave',
                    cancelButtonText: 'Cancel',
                    toast: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('../server/join.php', {
                                id: groupId,
                                type: 'leave'
                            })
                            .then(function(response) {
                                if (response.data.success) {
                                    alert('Joined successfully...');
                                    window.location.href = "index.php";
                                } else {
                                    alert('Error: ' + response.data.message);
                                }
                            })
                            .catch(function(error) {
                                console.error('An error occurred:', error);
                                alert('An unexpected error occurred. Please try again.');
                            });
                    }
                });
            }
        </script>
        <script>
            const sendBtn = document.querySelector('.send-btn');
            const textarea = document.querySelector('.chat-input');
            const fileInput = document.getElementById('file-input');
            const groupId = <?= json_encode($id) ?>;

            async function sendMessage() {
                const message = textarea.value.trim();
                if (!message && fileInput.files.length === 0) return;

                const formData = new FormData();
                formData.append('message', message);
                formData.append('group_id', groupId);

                for (let file of fileInput.files) {
                    formData.append('files[]', file);
                }

                try {
                    const response = await fetch('../server/send_message.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        textarea.value = '';
                        fileInput.value = '';
                        window.location.href=""
                        // getMessages();
                    } else {
                        alert('Failed to send message.');
                    }
                } catch (err) {
                    console.error('Upload failed:', err);
                }
            }

            // Click to send
            sendBtn.addEventListener('click', sendMessage);

            // Enter to send, Shift+Enter for newline
            textarea.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault(); // Prevent newline
                    sendMessage();
                }
            });

            getMessages(); // call once immediately
            setInterval(getMessages, 5000);
            async function getMessages() {
                const groupId = <?= json_encode($id) ?>; // PHP-embed group_id
                fetch(`../server/get_messages.php?group_id=${groupId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const chatBox = document.querySelector('.chat-box');
                            chatBox.innerHTML = ''; // Clear current messages

                            data.messages.forEach(msg => {
                                const isCurrentUser = msg.user_id == <?= json_encode($user_id) ?>;

                                const messageEl = document.createElement('div');
                                messageEl.classList.add('chat-message', isCurrentUser ? 'right' : 'left');

                                // const profilePic = document.createElement('div');
                                // profilePic.classList.add('profile-pic');

                                const profilePic = document.createElement('div');
                                profilePic.classList.add('profile-pic');

                                const img = document.createElement('img');
                                img.src = '../assets/images/' + msg.profile;
                                img.classList.add('profile-pic');

                                profilePic.appendChild(img);

                                const nameDiv = document.createElement('div');
                                nameDiv.classList.add('sender-name');
                                if (isCurrentUser) {
                                    nameDiv.textContent = 'You';
                                } else {
                                    nameDiv.textContent = msg.name;
                                }


                                const bubbleDiv = document.createElement('div');
                                bubbleDiv.classList.add('message-bubble');

                                bubbleDiv.textContent = msg.message;

                                // ✅ ADD file attachment if exists
                                if (msg.file_name) {
                                    const fileImg = document.createElement('img');
                                    fileImg.src = '../uploads/' + msg.file_name; // adjust path as needed
                                    fileImg.classList.add('attached-file');
                                    fileImg.style.maxWidth = '200px'; // optional styling
                                    fileImg.style.display = 'block';
                                    fileImg.style.marginTop = '8px';

                                    bubbleDiv.appendChild(fileImg);
                                }



                                const innerWrapper = document.createElement('div');
                                innerWrapper.appendChild(nameDiv);
                                innerWrapper.appendChild(bubbleDiv);

                                if (isCurrentUser) {
                                    messageEl.appendChild(innerWrapper);
                                    messageEl.appendChild(profilePic);
                                } else {
                                    messageEl.appendChild(profilePic);
                                    messageEl.appendChild(innerWrapper);
                                }

                                chatBox.appendChild(messageEl);
                            });

                            // Optional: auto-scroll to bottom
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    })
                    .catch(error => console.error('Error fetching messages:', error));

            }
        </script>

        <style>
            .mobile-menu-btn {
                color: black !important;
            }
        </style>