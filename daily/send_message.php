<?php
// session_start(); // Ensure session is started at the very top for message handling

include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");
include_once __DIR__. "/../includes/logger.php";
include_once __DIR__. "/../functions/message_functions.php"; // Include the new message functions file

log_user_action("Visited Send Message Page", "User navigated to the send message form");

// Check role
$allowed_roles = ['executive','daily','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}


$user_id = $_SESSION['user_id'];
$get_sender = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$sender_info = mysqli_fetch_array($get_sender);
$user_name = $sender_info['names'];

// Fetch all users for the recipient dropdown (excluding the sender themselves)
$all_users_query = mysqli_query($conn, "SELECT id, names, role FROM users WHERE id != $user_id ORDER BY names ASC");
$all_users = [];
if ($all_users_query) {
    while ($u = mysqli_fetch_assoc($all_users_query)) {
        $all_users[] = $u;
    }
} else {
    error_log("Error fetching users for recipient dropdown: " . mysqli_error($conn));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_id = $_POST['receiver_id'] ?? null;
    $subject = trim($_POST['subject'] ?? '');
    $message_content = trim($_POST['message_content'] ?? '');
    $parent_message_id = !empty($_POST['parent_message_id']) ? (int)$_POST['parent_message_id'] : null; // For replies

    // Basic validation
    if (empty($receiver_id) || empty($subject) || empty($message_content)) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'All fields (Recipient, Subject, Message) are required.'
        ];
    } else {
        // Use the reusable sendMessage function
        if (sendMessage($conn, $user_id, $receiver_id, $subject, $message_content, $parent_message_id)) {
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => 'Message sent successfully!'
            ];
        } else {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Failed to send message. Please try again.'
            ];
        }
    }
    // Redirect to prevent form re-submission on refresh
    header("Location: send_message.php");
    exit;
}

// Initialize message variables for display
$message_type = '';
$message_text = '';
if (!isset($_SESSION['message'])) {
    $message_type = $_SESSION['message']['type'];
    $message_text = $_SESSION['message']['text'];
    unset($_SESSION['message']); // Clear message after displaying
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Message</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .message-row.unread {
            font-weight: bold;
            background-color: #f8f9fa; /* Light grey background for unread */
        }
        .message-row:hover {
            cursor: pointer;
            background-color: #e9ecef; /* Lighter grey on hover */
        }
        .message-detail-modal .modal-body {
            white-space: pre-wrap; /* Preserve whitespace and line breaks */
        }
        /* Ensure buttons in table cells don't cause overflow on small screens */
        .table-responsive .btn-sm {
            padding: .25rem .5rem;
            font-size: .75rem;
        }
    </style>
</head>
<body class="bg-light p-4">
    <div class="container-fluid"> <!-- Use container-fluid for full width -->

        <!-- Navbar (consistent with dashboard) -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 border rounded shadow-sm px-4">
            <a class="navbar-brand fw-bold text-primary" href="#">Seller Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="create_sales_order.php">Create Sale</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_orders.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_profile.php">My Profile</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="send_message.php">Send Message</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
                </ul>
                <span class="navbar-text">Hello, <strong><?= $user_name ?></strong></span>
            </div>
        </nav>

        <h2 class="mb-4">Messaging Center</h2>

        <!-- System Message / Notification Area -->
        <div id="systemMessageContainer" class="mb-4">
            <?php if (!empty($message_text)): ?>
                <div class="alert alert-<?= htmlspecialchars($message_type) ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message_text) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <!-- Left Column: Send Message Form -->
            <div class="col-lg-6 mb-4">
                <div class="card" id="sendMessageFormCard"> <!-- Added ID for scrolling -->
                    <div class="card-header bg-primary text-white">
                        <strong>Compose Message</strong>
                    </div>
                    <div class="card-body card-block">
                        <form action="send_message.php" method="post" class="form-horizontal">
                            <input type="hidden" name="parent_message_id" id="parent_message_id" value="">
                            <div class="row form-group mb-3">
                                <div class="col col-md-3">
                                    <label for="receiver_id" class="form-control-label">Recipient</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <select name="receiver_id" id="receiver_id" class="form-control" required>
                                        <option value="">-- Select Recipient --</option>
                                        <?php foreach ($all_users as $user): ?>
                                            <option value="<?= htmlspecialchars($user['id']) ?>">
                                                <?= htmlspecialchars($user['names']) ?> (<?= htmlspecialchars(ucfirst($user['role'])) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col col-md-3">
                                    <label for="subject" class="form-control-label">Subject</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" id="subject" name="subject"
                                        placeholder="Enter message subject" class="form-control" required>
                                </div>
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col col-md-3">
                                    <label for="message_content" class="form-control-label">Message</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <textarea name="message_content" id="message_content" rows="9"
                                        placeholder="Type your message here..." class="form-control" required></textarea>
                                </div>
                            </div>

                            <div class="card-footer text-end mt-4">
                                <button type="submit" class="btn btn-primary btn-sm me-2">
                                    <i class="fa fa-paper-plane"></i> Send Message
                                </button>
                                <button type="reset" class="btn btn-danger btn-sm" id="resetFormBtn">
                                    <i class="fa fa-ban"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Manage Messages -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <strong>Manage Messages</strong>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3" id="messageTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="inbox-tab" data-bs-toggle="tab" data-bs-target="#inbox" type="button" role="tab" aria-controls="inbox" aria-selected="true">Inbox</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button" role="tab" aria-controls="sent" aria-selected="false">Sent</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="messageTabsContent">
                            <div class="tab-pane fade show active" id="inbox" role="tabpanel" aria-labelledby="inbox-tab">
                                <p class="text-center text-muted" id="inboxLoading">Loading inbox messages...</p>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Sender</th>
                                                <th>Subject</th>
                                                <th>Received</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="inboxMessagesTableBody">
                                            <!-- Messages will be loaded here via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-center text-muted" id="noInboxMessages" style="display:none;">No messages in your inbox.</p>
                            </div>
                            <div class="tab-pane fade" id="sent" role="tabpanel" aria-labelledby="sent-tab">
                                <p class="text-center text-muted" id="sentLoading">Loading sent messages...</p>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Recipient</th>
                                                <th>Subject</th>
                                                <th>Sent</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sentMessagesTableBody">
                                            <!-- Messages will be loaded here via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-center text-muted" id="noSentMessages" style="display:none;">No sent messages.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Message Detail Modal -->
    <div class="modal fade" id="messageDetailModal" tabindex="-1" aria-labelledby="messageDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="messageDetailModalLabel">Message Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>From/To:</strong> <span id="modalSenderReceiver"></span></p>
                    <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
                    <p><strong>Date:</strong> <span id="modalDate"></span></p>
                    <hr>
                    <p id="modalContent"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info" id="replyMessageBtn" style="display:none;">Reply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Optional: Auto-dismiss alerts after a few seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000); // Close after 5 seconds

            // Function to fetch and display messages
            function fetchMessages(type) {
                const loadingElement = $(`#${type}Loading`);
                const tableBody = $(`#${type}MessagesTableBody`);
                const noMessagesElement = $(`#no${type.charAt(0).toUpperCase() + type.slice(1)}Messages`);

                loadingElement.show();
                tableBody.empty(); // Clear previous messages
                noMessagesElement.hide();

                $.ajax({
                    url: 'fetch_messages_ajax.php', // Path to your AJAX endpoint
                    type: 'GET',
                    data: { type: type },
                    dataType: 'json',
                    success: function(response) {
                        loadingElement.hide();
                        if (response.status === 'success' && response.messages.length > 0) {
                            response.messages.forEach(function(message) {
                                let statusText = '';
                                let rowClass = '';
                                let senderReceiverName = '';

                                if (type === 'inbox') {
                                    statusText = message.is_read == 1 ? 'Read' : 'Unread';
                                    rowClass = message.is_read == 1 ? '' : 'unread';
                                    senderReceiverName = message.sender_name + ' (' + message.sender_role.charAt(0).toUpperCase() + message.sender_role.slice(1) + ')';
                                } else { // sent messages
                                    statusText = message.reply_count > 0 ? 'Replied' : 'No Reply';
                                    senderReceiverName = message.receiver_name + ' (' + message.receiver_role.charAt(0).toUpperCase() + message.receiver_role.slice(1) + ')';
                                }

                                const row = `
                                    <tr class="message-row ${rowClass}" data-message='${JSON.stringify(message)}' data-message-type="${type}">
                                        <td>${senderReceiverName}</td>
                                        <td>${message.subject}</td>
                                        <td>${message.timestamp}</td>
                                        <td>${statusText}</td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm viewMessageBtn" data-bs-toggle="modal" data-bs-target="#messageDetailModal">View</button>
                                        </td>
                                    </tr>
                                `;
                                tableBody.append(row);
                            });
                        } else {
                            noMessagesElement.show();
                        }
                    },
                    error: function(xhr, status, error) {
                        loadingElement.hide();
                        noMessagesElement.text('Error loading messages. Please try again.').show();
                        console.error("AJAX Error: ", status, error, xhr.responseText);
                    }
                });
            }

            // Load inbox messages on page load
            fetchMessages('inbox');

            // Handle tab clicks
            $('#inbox-tab').on('click', function() {
                fetchMessages('inbox');
            });
            $('#sent-tab').on('click', function() {
                fetchMessages('sent');
            });

            // Handle "View" button click to show message details in modal
            $(document).on('click', '.viewMessageBtn', function() {
                const messageData = $(this).closest('.message-row').data('message');
                const messageType = $(this).closest('.message-row').data('message-type');

                $('#modalSenderReceiver').text(messageType === 'inbox' ? `${messageData.sender_name} (${messageData.sender_role.charAt(0).toUpperCase() + messageData.sender_role.slice(1)})` : `${messageData.receiver_name} (${messageData.receiver_role.charAt(0).toUpperCase() + messageData.receiver_role.slice(1)})`);
                $('#modalSubject').text(messageData.subject);
                $('#modalDate').text(messageData.timestamp);
                $('#modalContent').text(messageData.message_content);

                // Show Reply button only for inbox messages
                if (messageType === 'inbox') {
                    $('#replyMessageBtn').show().data('message-id', messageData.id).data('sender-id', messageData.sender_id).data('subject', messageData.subject);
                    // Mark message as read if it's an inbox message and unread
                    if (messageData.is_read == 0) {
                        // AJAX call to mark as read
                        $.ajax({
                            url: 'mark_message_read_ajax.php', // You'll need to create this file
                            type: 'POST',
                            data: { message_id: messageData.id },
                            success: function(response) {
                                if (response.status === 'success') {
                                    // Update the row visually
                                    $(`.message-row[data-message*='"id":${messageData.id}']`).removeClass('unread').find('td:nth-child(4)').text('Read');
                                } else {
                                    console.error("Failed to mark message as read:", response.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("AJAX Error marking message as read: ", status, error);
                            }
                        });
                    }
                } else {
                    $('#replyMessageBtn').hide();
                }
            });

            // Handle Reply button click in modal
            $('#replyMessageBtn').on('click', function() {
                const originalMessageId = $(this).data('message-id');
                const originalSenderId = $(this).data('sender-id');
                const originalSubject = $(this).data('subject');

                // Close the modal
                const messageDetailModal = bootstrap.Modal.getInstance(document.getElementById('messageDetailModal'));
                messageDetailModal.hide();

                // Populate the send message form for reply
                $('#receiver_id').val(originalSenderId);
                $('#subject').val(`Re: ${originalSubject}`);
                $('#parent_message_id').val(originalMessageId);
                $('#message_content').focus(); // Focus on message content area

                // Scroll to the top of the form
                $('html, body').animate({
                    scrollTop: $("#sendMessageFormCard").offset().top
                }, 500);
            });

            // Reset form button handler
            $('#resetFormBtn').on('click', function() {
                $('#parent_message_id').val(''); // Clear parent message ID on reset
            });
        });
    </script>
</body>
</html>
