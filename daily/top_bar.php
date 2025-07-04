
        <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 border rounded shadow-sm px-4">
            <a class="navbar-brand fw-bold text-primary" href="#"><?php if($_SESSION['role'] != 'daily'){echo "Message";} else{echo "Seller"; }?> Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if($_SESSION['role'] != "daily") {
                        
                        ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item"><a class="nav-link" href="send_message.php">Send Message</a></li>

                    <?php } else { ?>
                    <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="create_sales_order.php">Create Sale</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_orders.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="send_message.php">Send Message</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_profile.php">My Profile</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
                    <?php } ?>

                </ul>
                <span class="navbar-text">Hello, <strong><?= $user_name ?></strong></span>
            </div>
        </nav>