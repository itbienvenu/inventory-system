<?php
include_once "../config/auth.php";
include_once "../config/config.php";
$result = mysqli_query($conn, "
    SELECT po.*, u.names AS created_by_name
    FROM purchase_orders po
    JOIN users u ON po.created_by = u.id
    ORDER BY po.created_at DESC
");
?>
<!DOCTYPE html><html lang="en"><head>
  <meta charset="UTF-8"><title>Purchase Orders Report</title>
  <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/datatables/datatables.min.css" rel="stylesheet">
</head><body class="p-4">
  <div class="container">
    <h2>Purchase Order Report</h2>
    <table id="poTable" class="table table-bordered">
      <thead><tr><th>PO #</th><th>Date</th><th>Created By</th><th>Status</th><th>Total Cost</th></tr></thead>
      <tbody>
        <?php while($r = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= htmlspecialchars($r['po_number']) ?></td>
          <td><?= $r['created_at'] ?></td>
          <td><?= htmlspecialchars($r['created_by_name']) ?></td>
          <td><?= htmlspecialchars(ucfirst($r['status'])) ?></td>
          <td><?= number_format($r['total_amount'],2) ?></td>
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
    $('#poTable').DataTable({
      dom: 'Bfrtip',
      buttons: ['csvHtml5','excelHtml5','pdfHtml5','print'],
      pageLength: 25,
    });
  });
  </script>
</body></html>
