<?php
include_once "../config/auth.php";
include_once "../config/config.php";
$result = mysqli_query($conn, "
    SELECT name, sku, quantity, low_stock, supplier
    FROM products
    WHERE quantity <= low_stock
    ORDER BY quantity ASC
");
?>
<!DOCTYPE html><html lang="en"><head>
  <meta charset="UTF-8"><title>Low Stock Products</title>
  <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/datatables/datatables.min.css" rel="stylesheet">
</head><body class="p-4">
  <div class="container">
    <h2>Low Stock Products</h2>
    <table id="lowStockTable" class="table table-bordered">
      <thead><tr><th>Product</th><th>SKU</th><th>Qty</th><th>Threshold</th><th>Supplier</th></tr></thead>
      <tbody>
        <?php while($r = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><?= htmlspecialchars($r['sku']) ?></td>
          <td><?= $r['quantity'] ?></td>
          <td><?= $r['low_stock'] ?></td>
          <td><?= htmlspecialchars($r['supplier']) ?></td>
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
    $('#lowStockTable').DataTable({
      dom: 'Bfrtip',
      buttons: ['csvHtml5','excelHtml5','pdfHtml5','print'],
      pageLength: 25,
    });
  });
  </script>
</body></html>
