<aside class="right-sidebar" id="right-sidebar">


    <!-- Files Section -->
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
                        foreach ($group_data as $key => $value) {
                            echo '
                                   <div class="member-item">
                                    <span>' . $value['name'] . '</span>
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
                        Files
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
                        foreach ($group_data_images as $key => $value) {
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

            <!-- Manage Group Button -->
            <div class="d-grid mt-3">
                <?php
                if (basename($_SERVER['PHP_SELF']) === 'admin_management.php') {
                    echo
                    '
                    <div class=" d-grid mt-3">
                    <a href="adminChatroom.php?group_id=' . $id . '" class="btn btn-warning">
                        <i class="fas fa-cog me-2"></i> Back To Chatroom
                    </a>

                    <button class="btn btn-danger mt-2" onclick="deleteGroup(' . $id . ')">
                        <i class="fas fa-trash-alt me-2"></i> Delete Group 
                    </button>
                    </div>
                </div>
            ';
                } else {
                    echo '
                     <a href="admin_management.php?group_id=' . $id . '">
                    <button class="btn btn-warning">
                        <i class="fas fa-cog me-2"></i> Manage Group
                    </button> </a>
                    ';
                }
                ?>


            </div>
        </div>


        <script>
            function deleteGroup(id) {
                Swal.fire({
                    title: 'Are you sure you want to delete this group?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('../server/group.php', {
                                type: 'delete',
                                id: id
                            })
                            .then(function(response) {
                                if (response.data.success) {
                                    Swal.fire('Deleted!', 'The group has been deleted.', 'success')
                                        .then(() => {
                                            window.location.href="index.php"
                                        });
                                } else {
                                    Swal.fire('Error!', response.data.message || 'Failed to delete group.', 'error');
                                }
                            })
                            .catch(function(error) {
                                console.error('Error deleting group:', error);
                                Swal.fire('Error!', 'There was a server error while deleting.', 'error');
                            });
                    }
                });
            }
        </script>


</aside>