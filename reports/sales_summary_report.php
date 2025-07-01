<?php
include_once "../config/auth.php";
include_once "../config/config.php";
$result = mysqli_query($conn, "
    SELECT DATE(created_at) AS date,
           COUNT(*) AS order_count,
           SUM(total_amount) AS total_revenue
    FROM sales_orders
    WHERE status = 'shipped'
    GROUP BY DATE(created_at)
    ORDER BY date DESC
");
?>
<!DOCTYPE html><html lang="en"><head>
  <meta charset="UTF-8"><title>Sales Summary</title>
  <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/datatables/datatables.min.css" rel="stylesheet">
</head><body class="p-4">
  <div class="container">
    <h2>Sales Summary Report</h2>
    <table id="salesTable" class="table table-bordered">
      <thead><tr><th>Date</th><th>Orders</th><th>Revenue</th></tr></thead>
      <tbody>
        <?php while($r = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $r['date'] ?></td>
            <td><?= $r['order_count'] ?></td>
            <td><?= number_format($r['total_revenue'],2) ?></td>
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
    $('#salesTable').DataTable({
      dom: 'Bfrtip',
      buttons: ['csvHtml5','excelHtml5','pdfHtml5','print'],
      pageLength: 25,
    });
  });
  </script>
</body></html>
