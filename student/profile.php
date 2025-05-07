<?php include 'header.php'; ?>
<!-- Main Content -->
<main class="main-content">
  <!-- Top Navigation -->
  <div class="d-flex justify-content-end mb-4">
    <div class="top-nav d-flex align-items-center gap-3">
      <a href="#" class="text-decoration-none">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
      </a>

      <div class="username-box"><?=$user_name?></div>
      <a href="profile.php">
        <img class="profile-icon" src="../assets/images/2.png" alt="">
      </a>
    </div>
  </div>

  <!-- Profile Section -->
  <h3 class="section-title">Profile:</h3>
  <div class="text-center mb-4">
    <h3 id="usernameDisplay"><?=$user_name?></h3>
    <!-- <img src="" alt="User Avatar" class="rounded-circle img-thumbnail" id="avatarImg" style="width: 120px; height: 120px;"> -->
  </div>

  <!-- Edit Section -->
  <div class="card mx-auto" style="max-width: 400px;">
    <div class="card-body">
      <div class="mb-3">
        <label for="usernameInput" class="form-label">Change Username:</label>
        <input type="text" class="form-control" id="usernameInput" value="<?=$user_name?>" required>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

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
</body>
</html>
