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
                                <span class="theme-switch" onclick="toggleTheme()" title="Switch Theme">
    🌓
</span>

                                <!-- Message Notification Dropdown -->
<div class="noti__item js-item-menu">
    <i class="zmdi zmdi-comment-more"></i>
    <span class="quantity"><?= $new_messages_count ?></span> <!-- Dynamic unread count -->
    <div class="mess-dropdown js-dropdown">
        <div class="mess__title">
            <p>You have <?= $new_messages_count ?> new messages</p> <!-- Dynamic unread count -->
        </div>
        <div class="mess__list js-scrollbar"> <!-- Changed to js-scrollbar for better scrolling if many messages -->
            <?php if (!empty($recent_executive_messages)): ?>
                <?php foreach ($recent_executive_messages as $message): ?>
                    <div class="mess__item <?= $message['is_read'] == 0 ? 'unread' : '' ?>"
                         data-message-id="<?= htmlspecialchars($message['id']) ?>"
                         data-subject="<?= htmlspecialchars($message['subject']) ?>"
                         data-content="<?= htmlspecialchars($message['message_content']) ?>"
                         data-timestamp="<?= htmlspecialchars($message['timestamp']) ?>"
                         data-sender-name="<?= htmlspecialchars($message['sender_name']) ?>"
                         data-sender-role="<?= htmlspecialchars($message['sender_role']) ?>">
                        <div class="image img-cir img-40">
                            <!-- Using a generic placeholder image. Replace with actual user avatars if available -->
                            <img src="https://placehold.co/40x40/cccccc/333333?text=User" alt="<?= htmlspecialchars($message['sender_name']) ?>" />
                        </div>
                        <div class="content">
                            <h6><?= htmlspecialchars($message['sender_name']) ?> (<?= htmlspecialchars(ucfirst($message['sender_role'])) ?>)</h6>
                            <p><?= htmlspecialchars(substr($message['subject'], 0, 40)) ?><?= (strlen($message['subject']) > 40 ? '...' : '') ?></p>
                            <span class="time"><?= formatMessageTime($message['timestamp']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center p-3 text-muted">No new messages.</div>
            <?php endif; ?>
        </div>
        <div class="mess__footer">
            <a href="seller/send_message.php?tab=inbox">View all messages</a> <!-- Link to full message center -->
        </div>
    </div>
</div>
<!-- End Message Notification Dropdown -->

<!-- The other noti__item blocks (email, notifications) remain as they were,
     unless you want to dynamically update them as well. -->
<!-- <div class="noti__item js-item-menu">
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
</div> -->
<!-- <div class="noti__item js-item-menu">
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
</div> -->


                                <div class="account-wrap">
                                    <div class="account-item clearfix js-item-menu">
                                        <div class="image">
                                            <img src="images/icon/avatar-01.jpg" />
                                        </div>
                                        <div class="content">
                                            <a class="js-acc-btn" href="#">
                                                <?php 
                                                $get_name = mysqli_query($conn,  "SELECT * FROM users where id=$id");
                                                while($row = mysqli_fetch_assoc($get_name))
                                                {
                                                    echo $row['names'];
                                                
                                                ?>

                                            </a>
                                        </div>
                                        <div class="account-dropdown js-dropdown">
                                            <div class="info clearfix">
                                                <div class="image">
                                                    <a href="#">
                                                        <img src="images/icon/avatar-01.jpg" alt="<?php echo $row['names']; ?>" />
                                                    </a>
                                                </div>
                                                <div class="content">
                                                    <h5 class="name">
                                                        <a href="#">

                                                        </a>
                                                    </h5>
                                                    <span class="email">
                                                        <?php echo $row['email']; ?>
                                                    </span>
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
                                                <a href="../config/logout.php">
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
<?php } ?>                             