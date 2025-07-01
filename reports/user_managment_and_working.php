<?php
include_once "../config/auth.php";
include_once "../config/config.php";
$users = mysqli_query($conn, "SELECT id, names, email, phone, role FROM users ORDER BY names");
$actions = mysqli_query($conn, "
    SELECT sl.user_id, u.names, COUNT(*) AS action_count
    FROM stock_logs sl
    JOIN users u ON sl.user_id = u.id
    GROUP BY sl.user_id
");
$actionCounts = [];
while($row = mysqli_fetch_assoc($actions)) {
    $actionCounts[$row['user_id']] = $row['action_count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management & Activity</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
  
  <!-- DataTables Core CSS from CDN -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  
  <!-- DataTables Buttons CSS from CDN -->
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

  <style>
    /* Basic styling for better readability and spacing */
    body {
      font-family: 'Inter', sans-serif; /* Using Inter font for modern look */
      background-color: #f8f9fa;
    }
    .container {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin-top: 20px;
    }
    h2 {
      color: #343a40;
      margin-bottom: 20px;
      text-align: center;
    }
    .table thead th {
      background-color: #343a40;
      color: #ffffff;
      border-color: #454d55;
    }
    .table tbody tr:nth-child(even) {
      background-color: #f2f2f2;
    }
    .table tbody tr:hover {
      background-color: #e9ecef;
    }
    /* Style for DataTables buttons container */
    .dt-buttons {
      margin-bottom: 15px;
    }
    .dt-buttons .dt-button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 8px 15px;
      margin-right: 5px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .dt-buttons .dt-button:hover {
      background-color: #0056b3;
    }

    /* Custom Modal for Delete Confirmation */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .modal-overlay.show {
      opacity: 1;
      visibility: visible;
    }
    .modal-content {
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      text-align: center;
      max-width: 400px;
      width: 90%;
      transform: translateY(-20px);
      transition: transform 0.3s ease;
    }
    .modal-overlay.show .modal-content {
      transform: translateY(0);
    }
    .modal-content h3 {
      margin-top: 0;
      color: #333;
    }
    .modal-content p {
      margin-bottom: 20px;
      color: #666;
    }
    .modal-buttons {
      display: flex;
      justify-content: center;
      gap: 10px;
    }
    .modal-buttons button {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    .modal-buttons .btn-confirm {
      background-color: #dc3545; /* Red for danger */
      color: white;
    }
    .modal-buttons .btn-confirm:hover {
      background-color: #c82333;
    }
    .modal-buttons .btn-cancel {
      background-color: #6c757d; /* Grey for cancel */
      color: white;
    }
    .modal-buttons .btn-cancel:hover {
      background-color: #5a6268;
    }
  </style>
</head>
<body class="p-4">
  <div class="container">
    <h2>User Management & Activity</h2>
    <table id="userTable" class="table table-bordered table-striped">
      <thead>
        <tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Stock Actions</th><th>Edit</th><th>Delete</th></tr>
      </thead>
      <tbody>
        <?php while($u = mysqli_fetch_assoc($users)): ?>
        <tr>
          <td><?= htmlspecialchars($u['names']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['phone']) ?></td>
          <td><?= htmlspecialchars($u['role']) ?></td>
          <td><?= $actionCounts[$u['id']] ?? 0 ?></td>
          <td><a href="../edits/edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning rounded">Edit</a></td>
          <td>
            <button type="button" class="btn btn-sm btn-danger rounded delete-user-btn" data-user-id="<?= $u['id'] ?>">Delete</button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Custom Delete Confirmation Modal -->
  <div id="deleteConfirmationModal" class="modal-overlay">
    <div class="modal-content">
      <h3>Confirm Deletion</h3>
      <p>Are you sure you want to delete this user? This action cannot be undone.</p>
      <div class="modal-buttons">
        <button class="btn-cancel" id="cancelDelete">Cancel</button>
        <button class="btn-confirm" id="confirmDelete">Delete</button>
      </div>
    </div>
  </div>

  <!-- JavaScript Libraries (jQuery first, then DataTables and its extensions) -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

  <!-- DataTables Core JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- DataTables Buttons Extension JS -->
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  
  <!-- File export dependencies for Buttons -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> <!-- For Excel and CSV -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> <!-- For PDF -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> <!-- For PDF fonts -->

  <!-- Specific Button types -->
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script> <!-- HTML5 export buttons (CSV, Excel, PDF) -->
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script> <!-- Print button -->

  <!-- Initialize DataTable and Handle Custom Delete Confirmation -->
  <script>
    $(function(){
      console.log("jQuery document ready for User Management. Attempting to initialize DataTable...");

      // Initialize DataTable with Buttons
      $('#userTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
          'copy',
          'csv',
          'excel',
          'pdf',
          'print'
        ],
        pageLength: 25,
      });
      console.log("DataTable initialization attempt complete for User Management.");

      // Custom Delete Confirmation Logic
      const deleteModal = $('#deleteConfirmationModal');
      const confirmDeleteBtn = $('#confirmDelete');
      const cancelDeleteBtn = $('#cancelDelete');
      let userIdToDelete = null;

      // Show modal when delete button is clicked
      $('#userTable').on('click', '.delete-user-btn', function() {
        userIdToDelete = $(this).data('user-id');
        deleteModal.addClass('show');
      });

      // Handle confirm delete
      confirmDeleteBtn.on('click', function() {
        if (userIdToDelete) {
          // Redirect to the delete script with the user ID
          window.location.href = `../edits/delete_user.php?id=${userIdToDelete}`;
        }
        deleteModal.removeClass('show');
      });

      // Handle cancel delete
      cancelDeleteBtn.on('click', function() {
        userIdToDelete = null; // Reset user ID
        deleteModal.removeClass('show');
      });

      // Close modal if clicking outside (optional)
      deleteModal.on('click', function(event) {
        if ($(event.target).is(deleteModal)) {
          userIdToDelete = null;
          deleteModal.removeClass('show');
        }
      });
    });
  </script>
</body>
</html>
