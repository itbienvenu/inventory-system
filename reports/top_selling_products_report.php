<!-- top_selling_products_report.php -->
<?php
include_once "../config/auth.php";
include_once "../config/config.php";
$sql = "
  SELECT p.name, SUM(soi.quantity) AS sold_qty, SUM(soi.quantity * soi.unit_price) AS revenue
  FROM sales_order_items soi
  JOIN products p ON soi.product_id = p.id
  GROUP BY soi.product_id
  ORDER BY sold_qty DESC
  LIMIT 10";
$res = mysqli_query($conn, $sql);
$labels=[]; $qtys=[];
?>
<!DOCTYPE html><html lang="en">
<head><meta charset="UTF-8"><title>Top Selling Products</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
<link href="../vendor/datatables/datatables.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h2>Top Selling Products</h2>
  <canvas id="topChart" height="100"></canvas>
  <table id="topTable" class="table table-bordered mt-3">
    <thead><tr><th>Product</th><th>Qty Sold</th><th>Revenue</th></tr></thead><tbody>
    <?php while($r=mysqli_fetch_assoc($res)):
      $labels[] = $r['name'];
      $qtys[] = $r['sold_qty'];
    ?>
      <tr>
        <td><?=$r['name']?></td>
        <td><?=$r['sold_qty']?></td>
        <td><?=number_format($r['revenue'],2)?></td>
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
  $('#topChart').Chart = new Chart($('#topChart'), {
    type: 'bar',
    data: {
      labels: <?=json_encode($labels)?>,
      datasets: [{ label: 'Quantity Sold', backgroundColor: '#007bff', data: <?=json_encode($qtys)?> }]
    },
    options: { responsive: true, scales: { x: { beginAtZero: true } } }
  });
  $('#topTable').DataTable({
    dom: 'Bfrtip',
    buttons: ['csvHtml5','excelHtml5','pdfHtml5','print'],
    pageLength: 10
  });
});
</script>
</body></html>
