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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../assets/js/adminChatroom.js"></script>
    <link rel="stylesheet" href="../assets/css/adminChatroom.css">
    <title>Admin-Chatroom</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

</head>

<body>

    <!-- Left Sidebar -->
    <?php include 'aside.php' ?>

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


                        <div class="group-info-container">
                            <img src="../assets/images/group.webp" alt="Group Logo" class="group-logo">
                            <div class="group-info">
                                <span class="group-name">
                                    <h2><?= $name ?></h2>
                                </span>
                                <span class="member-count"><?= $memberCount ?> members</span>
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
                        <div class="sender-name">Llysa Batumbakal</div>
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
                        <div class="sender-name">meatball_05</div>
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
                    style="display: none;"
                    onchange="handleFileSelect(this)"
                    multiple>

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
            <?php include 'rightsidebar.php' ?>



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





        </div>

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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>