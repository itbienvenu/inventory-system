<!-- inventory_valuation_report.php -->
<?php
include_once "../config/auth.php";
include_once "../config/config.php";
$result = mysqli_query($conn, "SELECT name, sku, quantity, cost_price, price FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory Valuation</title>
  <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/datatables/datatables.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h2 class="mb-4">Inventory Valuation Report</h2>
    <table id="valuationTable" class="table table-bordered table-striped">
      <thead>
        <tr><th>Product</th><th>SKU</th><th>Qty</th><th>Cost Price</th><th>Total Cost</th><th>Selling Price</th><th>Total Sell Value</th></tr>
      </thead>
      <tbody>
        <?php
        $totalCost = $totalSell = 0;
        while ($r = mysqli_fetch_assoc($result)) {
          $cost = $r['quantity'] * $r['cost_price'];
          $sell = $r['quantity'] * $r['price'];
          $totalCost += $cost;
          $totalSell += $sell;
          echo "<tr>
            <td>{$r['name']}</td><td>{$r['sku']}</td><td>{$r['quantity']}</td>
            <td>".number_format($r['cost_price'],2)."</td><td>".number_format($cost,2)."</td>
            <td>".number_format($r['price'],2)."</td><td>".number_format($sell,2)."</td>
          </tr>";
        }
        ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="4">TOTAL</th>
          <th><?=number_format($totalCost,2)?></th>
          <th></th>
          <th><?=number_format($totalSell,2)?></th>
        </tr>
      </tfoot>
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
      $('#valuationTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['csvHtml5','excelHtml5','pdfHtml5','print'],
        pageLength: 25
      });
    });
  </script>
</body>
</html>
