<?php
include_once "../config/auth.php";
include_once "../config/config.php";

// Query directly from stock_logs without any JOINs
$result = mysqli_query($conn, "
  SELECT id, product_id, movement_type, quantity_change, current_stock_after, 
         reference_document_type, reference_document_id, reference_document_number, 
         notes, moved_by, movement_timestamp 
  FROM stock_movements
  ORDER BY movement_timestamp DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Stock Movement Summary</title>
  <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/datatables/datatables.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h2>Stock Movement Summary</h2>
    <table id="movementTable" class="table table-bordered table-striped">
      <thead class="thead-dark">
        <tr>
          <th>ID</th>
          <th>Product ID</th>
          <th>Type</th>
          <th>Quantity Change</th>
          <th>Stock After</th>
          <th>Reference Type</th>
          <th>Reference ID</th>
          <th>Document No.</th>
          <th>Notes</th>
          <th>Moved By</th>
          <th>Timestamp</th>
        </tr>
      </thead>
      <tbody>
        <?php while($r = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['product_id'] ?></td>
            <td><?= htmlspecialchars($r['movement_type']) ?></td>
            <td><?= $r['quantity_change'] ?></td>
            <td><?= $r['current_stock_after'] ?></td>
            <td><?= htmlspecialchars($r['reference_document_type']) ?></td>
            <td><?= $r['reference_document_id'] ?></td>
            <td><?= htmlspecialchars($r['reference_document_number']) ?></td>
            <td><?= htmlspecialchars($r['notes']) ?></td>
            <td><?php $user = mysqli_query($conn, "select names from users where id = {$r['moved_by']} "); $user = mysqli_fetch_assoc($user); echo $user['names']; ?></td>
            <td><?= $r['movement_timestamp'] ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- JS -->
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
      $('#movementTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        pageLength: 25
      });
    });
  </script>
</body>
</html>
