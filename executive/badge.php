<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags-->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="au theme template">
  <meta name="author" content="Hau Nguyen">
  <meta name="keywords" content="au theme template">

  <!-- Title Page-->
  <title>Badge</title>

  <!-- Fontfaces CSS-->
  <link href="css/font-face.css" rel="stylesheet" media="all">
  <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
  <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
  <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

  <!-- Bootstrap CSS-->
  <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

  <!-- Vendor CSS-->
  <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
  <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
  <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
  <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
  <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
  <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
  <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

  <!-- Main CSS-->
  <link href="css/theme.css" rel="stylesheet" media="all">

</head>

<body class="animsition">
  <div class="page-wrapper">
    <!-- HEADER MOBILE-->
    <header class="header-mobile d-block d-lg-none">
      <div class="header-mobile__bar">
        <div class="container-fluid">
          <div class="header-mobile-inner">
            <a class="logo" href="index.php">
              <img src="images/icon/logo.png" alt="CoolAdmin" />
            </a>
            <button class="hamburger hamburger--slider" type="button">
              <span class="hamburger-box">
                <span class="hamburger-inner"></span>
              </span>
            </button>
          </div>
        </div>
      </div>
      <nav class="navbar-mobile">
        <div class="container-fluid">
          <ul class="navbar-mobile__list list-unstyled">
            <li class="has-sub">
              <a class="js-arrow" href="#">
                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
            </li>
            <li>
              <a href="chart.php">
                <i class="fas fa-chart-bar"></i>Charts</a>
            </li>
            <li>
              <a href="table.php">
                <i class="fas fa-table"></i>Tables</a>
            </li>
            <li>
              <a href="form.php">
                <i class="far fa-check-square"></i>Forms</a>
            </li>
            <li>
              <a href="calendar.php">
                <i class="fas fa-calendar-alt"></i>Calendar</a>
            </li>
            <li>
              <a href="map.php">
                <i class="fas fa-map-marker-alt"></i>Maps</a>
            </li>
            <li class="has-sub">
              <a class="js-arrow" href="#">
                <i class="fas fa-copy"></i>Pages</a>
              <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
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
                <i class="fas fa-desktop"></i>UI Elements</a>
              <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                <li>
                  <a href="button.php">Button</a>
                </li>
                <li>
                  <a href="badge.php">Badges</a>
                </li>
                <li>
                  <a href="tab.php">Tabs</a>
                </li>
                <li>
                  <a href="card.php">Cards</a>
                </li>
                <li>
                  <a href="alert.php">Alerts</a>
                </li>
                <li>
                  <a href="progress-bar.php">Progress Bars</a>
                </li>
                <li>
                  <a href="modal.php">Modals</a>
                </li>
                <li>
                  <a href="switch.php">Switchs</a>
                </li>
                <li>
                  <a href="grid.php">Grids</a>
                </li>
                <li>
                  <a href="fontawesome.php">Fontawesome Icon</a>
                </li>
                <li>
                  <a href="typo.php">Typography</a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    <!-- END HEADER MOBILE-->

    <!-- MENU SIDEBAR-->
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
              <a class="js-arrow" href="#">
                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
            </li>
            <li>
              <a href="chart.php">
                <i class="fas fa-chart-bar"></i>Charts</a>
            </li>
            <li>
              <a href="table.php">
                <i class="fas fa-table"></i>Tables</a>
            </li>
            <li>
              <a href="form.php">
                <i class="far fa-check-square"></i>Forms</a>
            </li>
            <li>
              <a href="calendar.php">
                <i class="fas fa-calendar-alt"></i>Calendar</a>
            </li>
            <li>
              <a href="map.php">
                <i class="fas fa-map-marker-alt"></i>Maps</a>
            </li>
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
            <li class="active has-sub">
              <a class="js-arrow" href="#">
                <i class="fas fa-desktop"></i>UI Elements</a>
              <ul class="list-unstyled navbar__sub-list js-sub-list">
                <li>
                  <a href="button.php">Button</a>
                </li>
                <li>
                  <a href="badge.php">Badges</a>
                </li>
                <li>
                  <a href="tab.php">Tabs</a>
                </li>
                <li>
                  <a href="card.php">Cards</a>
                </li>
                <li>
                  <a href="alert.php">Alerts</a>
                </li>
                <li>
                  <a href="progress-bar.php">Progress Bars</a>
                </li>
                <li>
                  <a href="modal.php">Modals</a>
                </li>
                <li>
                  <a href="switch.php">Switchs</a>
                </li>
                <li>
                  <a href="grid.php">Grids</a>
                </li>
                <li>
                  <a href="fontawesome.php">Fontawesome Icon</a>
                </li>
                <li>
                  <a href="typo.php">Typography</a>
                </li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </aside>
    <!-- END MENU SIDEBAR-->

    <!-- PAGE CONTAINER-->
    <div class="page-container">
      <!-- HEADER DESKTOP-->
      <header class="header-desktop">
        <div class="section__content section__content--p30">
          <div class="container-fluid">
            <div class="header-wrap">
              <form class="form-header" action="" method="POST">
                <input class="au-input au-input--xl" type="text" name="search" placeholder="Search for datas &amp; reports..." />
                <button class="au-btn--submit" type="submit">
                  <i class="zmdi zmdi-search"></i>
                </button>
              </form>
              <div class="header-button">
                <div class="noti-wrap">
                  <div class="noti__item js-item-menu">
                    <i class="zmdi zmdi-comment-more"></i>
                    <span class="quantity">1</span>
                    <div class="mess-dropdown js-dropdown">
                      <div class="mess__title">
                        <p>You have 2 news message</p>
                      </div>
                      <div class="mess__item">
                        <div class="image img-cir img-40">
                          <img src="images/icon/avatar-06.jpg" alt="Michelle Moreno" />
                        </div>
                        <div class="content">
                          <h6>Michelle Moreno</h6>
                          <p>Have sent a photo</p>
                          <span class="time">3 min ago</span>
                        </div>
                      </div>
                      <div class="mess__item">
                        <div class="image img-cir img-40">
                          <img src="images/icon/avatar-04.jpg" alt="Diane Myers" />
                        </div>
                        <div class="content">
                          <h6>Diane Myers</h6>
                          <p>You are now connected on message</p>
                          <span class="time">Yesterday</span>
                        </div>
                      </div>
                      <div class="mess__footer">
                        <a href="#">View all messages</a>
                      </div>
                    </div>
                  </div>
                  <div class="noti__item js-item-menu">
                    <i class="zmdi zmdi-email"></i>
                    <span class="quantity">1</span>
                    <div class="email-dropdown js-dropdown">
                      <div class="email__title">
                        <p>You have 3 New Emails</p>
                      </div>
                      <div class="email__item">
                        <div class="image img-cir img-40">
                          <img src="images/icon/avatar-06.jpg" alt="Cynthia Harvey" />
                        </div>
                        <div class="content">
                          <p>Meeting about new dashboard...</p>
                          <span>Cynthia Harvey, 3 min ago</span>
                        </div>
                      </div>
                      <div class="email__item">
                        <div class="image img-cir img-40">
                          <img src="images/icon/avatar-05.jpg" alt="Cynthia Harvey" />
                        </div>
                        <div class="content">
                          <p>Meeting about new dashboard...</p>
                          <span>Cynthia Harvey, Yesterday</span>
                        </div>
                      </div>
                      <div class="email__item">
                        <div class="image img-cir img-40">
                          <img src="images/icon/avatar-04.jpg" alt="Cynthia Harvey" />
                        </div>
                        <div class="content">
                          <p>Meeting about new dashboard...</p>
                          <span>Cynthia Harvey, April 12,,2018</span>
                        </div>
                      </div>
                      <div class="email__footer">
                        <a href="#">See all emails</a>
                      </div>
                    </div>
                  </div>
                  <div class="noti__item js-item-menu">
                    <i class="zmdi zmdi-notifications"></i>
                    <span class="quantity">3</span>
                    <div class="notifi-dropdown js-dropdown">
                      <div class="notifi__title">
                        <p>You have 3 Notifications</p>
                      </div>
                      <div class="notifi__item">
                        <div class="bg-c1 img-cir img-40">
                          <i class="zmdi zmdi-email-open"></i>
                        </div>
                        <div class="content">
                          <p>You got a email notification</p>
                          <span class="date">April 12, 2018 06:50</span>
                        </div>
                      </div>
                      <div class="notifi__item">
                        <div class="bg-c2 img-cir img-40">
                          <i class="zmdi zmdi-account-box"></i>
                        </div>
                        <div class="content">
                          <p>Your account has been blocked</p>
                          <span class="date">April 12, 2018 06:50</span>
                        </div>
                      </div>
                      <div class="notifi__item">
                        <div class="bg-c3 img-cir img-40">
                          <i class="zmdi zmdi-file-text"></i>
                        </div>
                        <div class="content">
                          <p>You got a new file</p>
                          <span class="date">April 12, 2018 06:50</span>
                        </div>
                      </div>
                      <div class="notifi__footer">
                        <a href="#">All notifications</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="account-wrap">
                  <div class="account-item clearfix js-item-menu">
                    <div class="image">
                      <img src="images/icon/avatar-01.jpg" alt="John Doe" />
                    </div>
                    <div class="content">
                      <a class="js-acc-btn" href="#">john doe</a>
                    </div>
                    <div class="account-dropdown js-dropdown">
                      <div class="info clearfix">
                        <div class="image">
                          <a href="#">
                            <img src="images/icon/avatar-01.jpg" alt="John Doe" />
                          </a>
                        </div>
                        <div class="content">
                          <h5 class="name">
                            <a href="#">john doe</a>
                          </h5>
                          <span class="email">johndoe@example.com</span>
                        </div>
                      </div>
                      <div class="account-dropdown__body">
                        <div class="account-dropdown__item">
                          <a href="#">
                            <i class="zmdi zmdi-account"></i>Account</a>
                        </div>
                        <div class="account-dropdown__item">
                          <a href="#">
                            <i class="zmdi zmdi-settings"></i>Setting</a>
                        </div>
                        <div class="account-dropdown__item">
                          <a href="#">
                            <i class="zmdi zmdi-money-box"></i>Billing</a>
                        </div>
                      </div>
                      <div class="account-dropdown__footer">
                        <a href="#">
                          <i class="zmdi zmdi-power"></i>Logout</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>
      <!-- HEADER DESKTOP-->

      <!-- MAIN CONTENT-->
      <div class="main-content">
        <div class="section__content section__content--p30">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-6">

                <div class="card">
                  <div class="card-header">
                    <strong>Badges</strong>
                    <small>Use
                      <code>.badge</code> class within
                      <code>&lt;span&gt;</code> elements to create badges:
                    </small>
                  </div>
                  <div class="card-body">

                    <a href="#">News
                      <span class="badge badge-primary">5</span>
                    </a>
                    <br>
                    <a href="#">Comments
                      <span class="badge badge-warning">10</span>
                    </a>
                    <br>
                    <a href="#">Updates
                      <span class="badge badge-success">2</span>
                    </a>
                  </div>
                </div>
                <!-- /# card -->


                <div class="card">
                  <div class="card-header">
                    <strong>Labels</strong>
                  </div>
                  <div class="card-body">
                    <p class="text-muted m-b-15">Use the
                      <code>.label</code> class,&nbsp; followed by one of the six contextual classes
                      <code>.label-default</code>,
                      <code>.label-primary</code>,
                      <code>.label-success</code>,
                      <code>.label-info</code>,
                      <code>.label-warning</code> or
                      <code>.label-danger</code>, within a
                      <code>&lt;span&gt;</code> element to create a label:</p>

                    <h1>Example heading
                      <span class="badge badge-secondary">New</span>
                    </h1>
                    <h2>Example heading
                      <span class="badge badge-secondary">New</span>
                    </h2>
                    <h3>Example heading
                      <span class="badge badge-secondary">New</span>
                    </h3>
                    <h4>Example heading
                      <span class="badge badge-secondary">New</span>
                    </h4>
                    <h5>Example heading
                      <span class="badge badge-secondary">New</span>
                    </h5>
                    <h6>Example heading
                      <span class="badge badge-secondary">New</span>
                    </h6>
                  </div>
                </div>

              </div>
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-header">
                    <strong>Badges in Buttons</strong>
                  </div>
                  <div class="card-body">
                    <p class="text-muted m-b-15">Use the
                      <code>.badge</code> class within
                      <code>&lt;span&gt;</code> elements to create badges:</p>

                    <button type="button" class="btn btn-primary m-l-10 m-b-10">Primary
                      <span class="badge badge-light">7</span>
                    </button>
                    <button type="button" class="btn btn-success m-l-10 m-b-10">Success
                      <span class="badge badge-light">7</span>
                    </button>
                    <button type="button" class="btn btn-info m-l-10 m-b-10">Info
                      <span class="badge badge-light">7</span>
                    </button>
                    <button type="button" class="btn btn-warning m-l-10 m-b-10">Warning
                      <span class="badge badge-light">7</span>
                    </button>
                    <button type="button" class="btn btn-danger m-l-10 m-b-10">Danger
                      <span class="badge badge-light">7</span>
                    </button>
                  </div>
                </div>

                <div class="card">
                  <div class="card-header">
                    <strong>Labels</strong>
                  </div>
                  <div class="card-body">
                    <p class="text-muted m-b-15">Use the
                      <code>.label</code> class,&nbsp; followed by one of the six contextual classes
                      <code>.label-default</code>,
                      <code>.label-primary</code>,
                      <code>.label-success</code>,
                      <code>.label-info</code>,
                      <code>.label-warning</code> or
                      <code>.label-danger</code>, within a
                      <code>&lt;span&gt;</code> element to create a label:</p>

                    <span class="badge badge-primary">Primary</span>
                    <span class="badge badge-secondary">Secondary</span>
                    <span class="badge badge-success">Success</span>
                    <span class="badge badge-danger">Danger</span>
                    <span class="badge badge-warning">Warning</span>
                    <span class="badge badge-info">Info</span>
                    <span class="badge badge-light">Light</span>
                    <span class="badge badge-dark">Dark</span>


                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END PAGE CONTAINER-->

  </div>

  <!-- Jquery JS-->
  <script src="vendor/jquery-3.2.1.min.js"></script>
  <!-- Bootstrap JS-->
  <script src="vendor/bootstrap-4.1/popper.min.js"></script>
  <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
  <!-- Vendor JS       -->
  <script src="vendor/slick/slick.min.js">
  </script>
  <script src="vendor/wow/wow.min.js"></script>
  <script src="vendor/animsition/animsition.min.js"></script>
  <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
  </script>
  <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
  <script src="vendor/counter-up/jquery.counterup.min.js">
  </script>
  <script src="vendor/circle-progress/circle-progress.min.js"></script>
  <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="vendor/chartjs/Chart.bundle.min.js"></script>
  <script src="vendor/select2/select2.min.js">
  </script>

  <!-- Main JS-->
  <script src="js/main.js"></script>

</body>

</html>
<!-- end document-->
