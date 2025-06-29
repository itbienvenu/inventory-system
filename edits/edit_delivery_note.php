<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') {
    die("Unauthorized access.");
}

if (!isset($_GET['dn']) || empty($_GET['dn'])) {
    die("Delivery Note number not provided for editing.");
}

$delivery_note_number = mysqli_real_escape_string($conn, $_GET['dn']);

// Fetch delivery note header details
$dn_query = mysqli_query($conn, "
    SELECT *
    FROM delivery_notes
    WHERE delivery_note_number = '$delivery_note_number'
");

if (!$dn_query) {
    die("Error fetching delivery note for editing: " . mysqli_error($conn));
}

$delivery_note_data = mysqli_fetch_assoc($dn_query);

if (!$delivery_note_data) {
    die("Delivery Note with number '$delivery_note_number' not found for editing.");
}

$delivery_note_id = $delivery_note_data['id'];

// Fetch delivery note items
$items_query = mysqli_query($conn, "
    SELECT dni.*, p.name AS product_name, p.sku AS product_sku
    FROM delivery_note_items dni
    LEFT JOIN products p ON dni.product_id = p.id
    WHERE dni.delivery_note_id = $delivery_note_id
");

if (!$items_query) {
    die("Error fetching delivery note items for editing: " . mysqli_error($conn));
}

$delivery_note_items = [];
while ($item = mysqli_fetch_assoc($items_query)) {
    $delivery_note_items[] = $item;
}

// Fetch all sales orders for the dropdown (optional linking)
$all_sales_orders_query = mysqli_query($conn, "SELECT id, order_number, company FROM sales_orders ORDER BY order_number DESC");
$all_sales_orders = [];
while ($so = mysqli_fetch_assoc($all_sales_orders_query)) {
    $all_sales_orders[] = $so;
}

// Fetch all products for the dropdown (for adding/editing items)
$all_products_edit_dn = mysqli_query($conn, "SELECT id, name, sku FROM products ORDER BY name ASC");
$all_products_edit_dn_array = [];
while ($p = mysqli_fetch_assoc($all_products_edit_dn)) {
    $all_products_edit_dn_array[] = $p;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Delivery Note #<?php echo htmlspecialchars($delivery_note_data['delivery_note_number']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
            margin-bottom: 30px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #ffc107; /* Warning color for edit */
            color: white;
            font-weight: bold;
            border-radius: 8px 8px 0 0 !important;
            padding: 15px;
        }
        .card-body {
            padding: 20px;
        }
        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        .product-row .form-control {
            border-radius: 5px;
        }
        .btn-danger, .btn-success {
            border-radius: 5px;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .rounded-pill.px-3 {
            border-radius: 50rem !important; /* Ensure consistent rounded pills */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Edit Delivery Note #<?php echo htmlspecialchars($delivery_note_data['delivery_note_number']); ?></h2>

        <form action="../functions/UpdateDeliveryNote.php" method="POST">
            <!-- Hidden fields to send delivery note ID and number for redirection -->
            <input type="hidden" name="delivery_note_id" value="<?php echo htmlspecialchars($delivery_note_data['id']); ?>">
            <input type="hidden" name="delivery_note_number" value="<?php echo htmlspecialchars($delivery_note_data['delivery_note_number']); ?>">

            <div class="row">
                <!-- Delivery Info Section -->
                <div class="col-lg-6">
                    <div class="card rounded-3 mb-4">
                        <div class="card-header"><strong>Delivery Details</strong></div>
                        <div class="card-body card-block">
                            <div class="form-group mb-3">
                                <label class="form-label">Customer Company Name</label>
                                <input type="text" name="customer_company" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($delivery_note_data['customer_company']); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Street Address</label>
                                <input type="text" name="customer_street" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($delivery_note_data['customer_street']); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="customer_city" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($delivery_note_data['customer_city']); ?>">
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col-6">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="customer_postal_code" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($delivery_note_data['customer_postal_code']); ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="customer_country" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($delivery_note_data['customer_country']); ?>">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Link to Sales Order (Optional)</label>
                                <select name="sales_order_id" class="form-control rounded-pill px-3">
                                    <option value="">-- Select Sales Order --</option>
                                    <?php foreach ($all_sales_orders as $so) : ?>
                                        <option value="<?php echo htmlspecialchars($so['id']); ?>"
                                            <?php echo ($so['id'] == $delivery_note_data['sales_order_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($so['order_number']); ?> (<?php echo htmlspecialchars($so['company']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Shipping Date</label>
                                <input type="date" name="shipping_date" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($delivery_note_data['shipping_date']); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Delivered By</label>
                                <input type="text" name="delivered_by" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($delivery_note_data['delivered_by']); ?>" placeholder="Courier Name or Driver Name">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Recipient's Name (if received)</label>
                                <input type="text" name="recipient_name" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($delivery_note_data['recipient_name']); ?>" placeholder="Name of person who received goods">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Received At</label>
                                <input type="datetime-local" name="received_at" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars(str_replace(' ', 'T', substr($delivery_note_data['received_at'], 0, 19))); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">General Notes</label>
                                <textarea name="notes" rows="3" class="form-control rounded-pill px-3" placeholder="Any special instructions or comments"><?php echo htmlspecialchars($delivery_note_data['notes']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Product Selection Section -->
                <div class="col-lg-6">
                    <div class="card rounded-3 mb-4">
                        <div class="card-header"><strong>Items to Ship</strong></div>
                        <div class="card-body card-block">
                            <div id="productRowsDN">
                                <?php if (empty($delivery_note_items)) : ?>
                                    <!-- Default empty row if no items -->
                                    <div class="form-row mb-2 product-row-dn d-flex align-items-center">
                                        <div class="col-5 pe-1">
                                            <select name="products[]" class="form-control rounded-pill px-3" required>
                                                <option value="">-- Select Product --</option>
                                                <?php foreach ($all_products_edit_dn_array as $p) : ?>
                                                    <option value="<?php echo htmlspecialchars($p['id']); ?>">
                                                        <?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['sku']); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-3 pe-1">
                                            <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" required min="1">
                                        </div>
                                        <div class="col-3 pe-1">
                                            <input type="text" name="item_notes[]" class="form-control rounded-pill px-3" placeholder="Item Notes (Optional)">
                                        </div>
                                        <div class="col-1 ps-0">
                                            <button type="button" class="btn btn-danger btn-sm removeRowDN rounded-pill">X</button>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <?php foreach ($delivery_note_items as $item) : ?>
                                        <div class="form-row mb-2 product-row-dn d-flex align-items-center">
                                            <div class="col-5 pe-1">
                                                <select name="products[]" class="form-control rounded-pill px-3" required>
                                                    <option value="">-- Select Product --</option>
                                                    <?php foreach ($all_products_edit_dn_array as $p) : ?>
                                                        <option value="<?php echo htmlspecialchars($p['id']); ?>"
                                                            <?php echo ($p['id'] == $item['product_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['sku']); ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-3 pe-1">
                                                <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" value="<?php echo htmlspecialchars($item['quantity']); ?>" required min="1">
                                            </div>
                                            <div class="col-3 pe-1">
                                                <input type="text" name="item_notes[]" class="form-control rounded-pill px-3" placeholder="Item Notes (Optional)" value="<?php echo htmlspecialchars($item['notes']); ?>">
                                            </div>
                                            <div class="col-1 ps-0">
                                                <button type="button" class="btn btn-danger btn-sm removeRowDN rounded-pill">X</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Add New Product Button -->
                            <div class="form-group mt-3">
                                <button type="button" class="btn btn-success btn-sm rounded-pill px-4" id="addProductDN">+ Add Item</button>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group text-right mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5">Save Changes</button>
                                <a href="view_delivery_note.php?dn=<?php echo htmlspecialchars($delivery_note_data['delivery_note_number']); ?>" class="btn btn-secondary rounded-pill px-4 ms-2">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            const allProductsEditDN = <?php echo json_encode($all_products_edit_dn_array); ?>;
            let productRowCountDN = $('#productRowsDN .product-row-dn').length; // Initialize with existing rows

            $('#addProductDN').on('click', function() {
                productRowCountDN++;
                let productOptions = '<option value="">-- Select Product --</option>';
                allProductsEditDN.forEach(function(p) {
                    productOptions += `<option value="${p.id}">${p.name} (${p.sku})</option>`;
                });

                const productRow = `
                    <div class="form-row mb-2 product-row-dn d-flex align-items-center">
                        <div class="col-5 pe-1">
                            <select name="products[]" class="form-control rounded-pill px-3" required>
                                ${productOptions}
                            </select>
                        </div>
                        <div class="col-3 pe-1">
                            <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" required min="1">
                        </div>
                        <div class="col-3 pe-1">
                            <input type="text" name="item_notes[]" class="form-control rounded-pill px-3" placeholder="Item Notes (Optional)">
                        </div>
                        <div class="col-1 ps-0">
                            <button type="button" class="btn btn-danger btn-sm removeRowDN rounded-pill">X</button>
                        </div>
                    </div>
                `;
                $('#productRowsDN').append(productRow);
            });

            // Event listener for removing product rows
            $(document).on('click', '.removeRowDN', function() {
                // Ensure at least one product row remains
                if ($('#productRowsDN .product-row-dn').length > 1) {
                    $(this).closest('.product-row-dn').remove();
                } else {
                    alert("A delivery note must have at least one item.");
                }
            });
        });
    </script>
</body>
</html>
