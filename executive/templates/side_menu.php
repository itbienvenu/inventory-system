<aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="#">
                    <img src="images/icon/logo.png" alt="Cool Admin" />
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li class="has-sub">
                            <a class="js-arrow" href="<?php if($_SESSION['role'] != 'executive'){echo '../'.$_SESSION['role'].'/index.php';}else{echo 'index.php';}?>">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                            
                        </li>
                        <li>
                            <a href="chart.php">
                                <i class="fas fa-chart-bar"></i>Charts</a>
                        </li>
                        <li class="active">
                            <a href="table.php">
                                <i class="fas fa-table"></i>Tables</a>
                        </li>
                        <li>
                            <a href="<?php if($_SESSION['role'] == 'executive') {echo "form.php";} else {echo '../'.$_SESSION['role'].'/form.php';}?>">
                                <i class="far fa-check-square"></i>Forms</a>
                        </li>
                        <li>
                            <a href="<?php if($_SESSION['role'] == 'admin') {
                                echo "../edits/manage_products.php";
                                } else {
                                    echo "products.php"; }?>">
                                <i class="fas fa-calendar-alt"></i>Products</a>
                        </li>
                        <?php if($_SESSION["role"] != "executive") {
                        }else {?>
                        <li>
                            <a href="inventory_managment.php">
                                <i class="zmdi zmdi-home"></i>Inventory Managment</a>
                        </li>
                        <?php }?>
                        <li>
                            <a href="<?php if($_SESSION['role'] == 'admin') {
                                echo "../admin/repo.php";
                                } else {
                                    echo "repo.php"; }?>">
                                <i class="fas fa-calendar-alt"></i>Reports</a>
                        </li>
                        <li>
                            <a href="documents.php">
                                <i class="fas fa-calendar-alt"></i>Documents</a>
                        </li>
                                    
                        <li>
                            <a href="<?php if($_SESSION['role'] == 'executive') {echo "typo.php";} else if ($_SESSION['role'] == 'admin') {echo "../executive/typo.php";}?>">
                                <i class="fas fa-calendar-alt"></i>Documentation</a>
                        </li>
                        
                        <?php if($_SESSION["role"] != "executive") {
                        } else {?>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-copy"></i>Pages</a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="login.php">Login</a>
                                </li>
                                <li>
                                    <a href="register.php">Register</a>
                                </li>
                                <li>
                                    <a href="forget-pass.php">Forget Password</a>
                                </li>
                            </ul>
                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-desktop"></i>Others Links</a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="database_tables.php">Database Tables</a>
                                </li>
                                
        
                            </ul>
                        </li>
                        <?php } ?>
                        
                    </ul>
                </nav>
            </div>
        </aside>