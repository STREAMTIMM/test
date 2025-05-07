<?php
if (!$db->isLoggedIn()) {
    echo "
    <script>
    window.location.href='../';
</script>
    ";
}
$position = $_SESSION['user'];
if (!$position == 'admin') {
    echo "
    <script>
    window.location.href='../';
</script>
    ";
}

$user_id = $_SESSION['id'];
$user_name = $_SESSION['name'];

?>
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
                echo '<a href="adminChatroom.php?group_id=' . htmlspecialchars($row['id']) . '" class="nav-btn">' . htmlspecialchars(ucwords($row['name'])) . '</a>';
            }
            ?>





            <?php
            if (basename($_SERVER['PHP_SELF']) !== 'adminChatroom.php') {

            ?>
                <!-- create group -->
                <div class="mt-4">

                    <a href="#" class="createbtn" onclick="openCreateGroupModal()">➕Create Group</a>
                </div>


            <?php } ?>
        </nav>
    </div>
</aside>