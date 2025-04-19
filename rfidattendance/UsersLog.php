<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users Logs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/userslog.css">
    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/bootbox.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script>
    $(document).ready(function () {
        // Function to load logs dynamically
        function loadLogs() {
            const selectedUser = $('#card_sel').val();
            const selectedDate = $('#log_date').val();
            const selectedTime = $('#log_time').val();

            $.ajax({
                url: "user_log_up.php",
                type: "POST",
                data: {
                    'filter_user': selectedUser,
                    'filter_date': selectedDate,
                    'filter_time': selectedTime
                },
                success: function (data) {
                    $('#dataBody').html(data);
                },
                error: function () {
                    $('#dataBody').html('<tr><td colspan="5">Error fetching logs. Please try again later.</td></tr>');
                }
            });
        }

        // Open modal to apply filters
        $('#filter_logs').click(function () {
            $('#Filter-modal').modal('show');
        });

        // Apply filters when the filter button is clicked
        $('#apply_filters').click(function () {
            $('#Filter-modal').modal('hide');
            loadLogs();
        });

        // Load logs initially
        loadLogs();
    });
    </script>
</head>
<body>
<?php include 'header.php'; ?>
<section class="container py-lg-5">
  <h1 class="slideInDown animated">Users Daily Logs</h1>

  <!-- Button to open filter modal -->
  <button type="button" id="filter_logs" class="btn btn-success mb-3">Filter/Export Logs</button>

  <!-- Logs Table -->
  <div class="table-responsive" style="max-height: 500px;">
    <table class="table">
      <thead class="table-primary">
        <tr>
          <th>UID</th>
          <th>Name</th>
          <th>Date</th>
          <th>Time In</th>
          <th>Time Out</th>
        </tr>
      </thead>
      <tbody class="table-secondary" id="dataBody">
        <!-- Logs will be dynamically loaded here via AJAX -->
      </tbody>
    </table>
  </div>
</section>

<!-- Filter Modal -->
<div class="modal fade" id="Filter-modal" tabindex="-1" role="dialog" aria-labelledby="Filter Logs Modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Filter Logs</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="card_sel">Filter By User:</label>
          <select name="card_sel" id="card_sel" class="form-control">
            <option value="0">All Users</option>
            <?php
            // Fetch users data from Firebase
            $usersUrl = "https://iot-biblio-3581c-default-rtdb.europe-west1.firebasedatabase.app/users.json";
            $jsonData = file_get_contents($usersUrl);
            $users = json_decode($jsonData, true);
            if ($users) {
                foreach ($users as $uid => $user) {
                    echo '<option value="' . htmlspecialchars($uid) . '">' . htmlspecialchars($user['name']) . '</option>';
                }
            } else {
                echo '<option value="0">No users found</option>';
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label for="log_date">Filter By Date:</label>
          <input type="date" name="log_date" id="log_date" class="form-control">
        </div>
        <div class="form-group">
          <label for="log_time">Filter By Time:</label>
          <input type="time" name="log_time" id="log_time" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="apply_filters" class="btn btn-primary">Apply Filters</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>