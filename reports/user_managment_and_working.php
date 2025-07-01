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
<!DOCTYPE html><html lang="en"><head>
  <meta charset="UTF-8"><title>User Management & Activity</title>
  <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/datatables/datatables.min.css" rel="stylesheet">
</head><body class="p-4">
  <div class="container">
    <h2>User Management & Activity</h2>
    <table id="userTable" class="table table-bordered">
      <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Stock Actions</th><th>Edit</th><th>Delete</th></tr></thead>
      <tbody>
        <?php while($u = mysqli_fetch_assoc($users)): ?>
        <tr>
          <td><?= htmlspecialchars($u['names']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['phone']) ?></td>
          <td><?= htmlspecialchars($u['role']) ?></td>
          <td><?= $actionCounts[$u['id']] ?? 0 ?></td>
          <td><a href="../edits/edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a></td>
          <td><a href="../edits/delete_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('Delete this user?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <script src="../vendor/jquery-3.2.1.min.js"></script>
  <script src="../vendor/datatables/datatables.min.js"></script>
  <script src="../vendor/datatables/dataTables.buttons.min.js"></script>
  <script src="../vendor/datatables/buttons.html5.min.js"></script>
  <script src="../vendor/datatables/buttons.print.min.js"></script>
  <script src="../vendor/jszip/jszip.min.js"></script>
  <script src="../vendor/pdfmake/pdfmake.min.js"></script>
  <script src="../vendor/pdfmake/vfs_fonts.js"></script>
  <script>
  $(function(){
    $('#userTable').DataTable({
      dom: 'Bfrtip',
      buttons: ['csvHtml5','excelHtml5','pdfHtml5','print'],
      pageLength: 25,
    });
  });
  </script>
</body></html>
