<?php
require_once '../conn/db.php';
$db = new DatabaseHandler();
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <button class="burger-menu" id="burgerMenu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>

    <?php include 'aside.php' ?>

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

        <!-- Profile Section -->
        <h3 class="section-title">Profile:</h3>
        <div class="text-center mb-4">
            <h3 id="usernameDisplay"><?= $user_name ?></h3>
            <!-- <img src="" alt="User Avatar" class="rounded-circle img-thumbnail" id="avatarImg" style="width: 120px; height: 120px;"> -->
        </div>

        <!-- Edit Section -->
        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <div class="mb-3">
                    <label for="usernameInput" class="form-label">Change Username:</label>
                    <input type="text" class="form-control" id="usernameInput" value="<?= $user_name ?>" required>
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary" id="updateBtn">Update</button>
                </div>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="text-center mt-4">
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</button>
        </div>



    </main>


    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to log out?
                </div>
                <div class="modal-footer">
                    <a href="logout.php" class="btn btn-success">Yes</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                </div>
            </div>
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
    document.getElementById('updateBtn').addEventListener('click', function() {
        var newUsername = document.getElementById('usernameInput').value;

        // Create the Axios POST request to update the username
        axios.post('../server/profile.php', {
                username: newUsername
            })
            .then(function(response) {
                if (response.data.success) {
                    // Update the username displayed on the page
                    document.getElementById('usernameDisplay').innerText = newUsername;
                    alert('Username updated successfully!');
                } else {
                    alert('Failed to update username. Please try again.');
                }
            })
            .catch(function(error) {
                console.error('There was an error updating the username:', error);
                alert('There was an error updating your username.');
            });
    });
</script>

</html>