<?php
require_once '../conn/db.php';
$db = new DatabaseHandler();

if (!$db->isLoggedIn()) {
    echo "
    <script>
    window.location.href='../';
</script>
    ";
}

$user_id = $_SESSION['id'];
$user_name = $_SESSION['name'];

$user = $db->fetchOne("SELECT * FROM `users` WHERE id = :id AND status = 1", ['id' => $user_id]);
$profile_picture = $user['profile'];
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
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <title>student dashboard</title>
</head>

<body>
    <button class="burger-menu" id="burgerMenu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-content">
            <a href="index.php" class="text-decoration-none text-white">
                <div class="logocontainer">
                    <img src="/images/roundlogo.png" alt="logo" id="roundlogo" class="img-fluid">
                    <h2 id="title">QCUnnectED</h2>
                </div>
            </a>
            <!-- searchbar -->
            <?php
            if (!(isset($_GET['group_id']))) {
            ?>
                <form method="GET">

                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white border-end-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1E3D50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    </div>
                </form>

            <?php }

            ?>

            <!-- recent chats -->
            <h3 class="sidebar-title">Recent Chats</h3>
            <nav>

                <?php
                $search = $_GET['search'] ?? '';
                $conditions = ['status' => 1];
                $likeColumns = [];

                if (!empty($search)) {
                    $conditions['name'] = $search;
                    $likeColumns[] = 'name';  // tell it to use LIKE for 'name'
                }

                $rows = $db->getRowsWhere('groups', $conditions, 'id DESC', 5, $likeColumns);

                foreach ($rows as $row) {

                    $group_id = $row['id'];
                    $data = $db->fetchOne("SELECT * FROM `student_groups` WHERE user_id = :user_id AND group_id = :group_id  AND status = 1", [
                        'user_id' => $user_id,
                        'group_id' => $group_id
                    ]);

                    if ($data) {
                        echo '<a href="chatroom.php?group_id=' . htmlspecialchars($row['id']) . '" class="nav-btn">' . htmlspecialchars(ucwords($row['name'])) . '</a>';
                    } 
                    // else {
                    //     echo '<a href="#" class="nav-btn" onclick="askToJoin(\'' . htmlspecialchars(addslashes($row['name'])) . '\', ' . intval($row['id']) . ')">' . htmlspecialchars(ucwords($row['name'])) . '</a>';
                    // }
                }
                ?>

            </nav>
        </div>
    </aside>
    <script>
        function askToJoin(groupName, groupId) {
            Swal.fire({
                title: 'Join Group?',
                text: `Do you want to join "${groupName}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Join',
                cancelButtonText: 'Cancel',
                toast: false
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('../server/join.php', {
                            id: groupId,
                            type: 'join'
                        })
                        .then(function(response) {
                            if (response.data.success) {
                                alert('Joined successfully...');
                                window.location.href = "chatroom.php?group_id="+groupId;
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