<?php
session_start();
require_once '../Controllers/MessageController.php';
require_once '../Controllers/HostController.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../Common/login.php");
    exit;
}

$travelerId = $_SESSION['userID'];

// Create controllers
$messageController = new MessageController();
$hostController = new HostController();

// Get all conversations for this traveler
$conversations = $messageController->getRecentConversations($travelerId, 'traveler');

// Initialize variables for the active conversation
$activeHostId = null;
$activeHostData = null;
$activeMessages = [];

// Check if a specific host is selected
if (isset($_GET['host_id']) && is_numeric($_GET['host_id'])) {
    $activeHostId = (int)$_GET['host_id'];
    $activeHostData = $hostController->getHostById($activeHostId);
    
    // If host exists, get the conversation
    if ($activeHostData) {
        $activeMessages = $messageController->getConversation($travelerId, $activeHostId, 'traveler', 'host');
        
        // Mark messages as read
        $messageController->markMessagesAsRead($travelerId, $activeHostId, 'traveler', 'host');
    }
} 
// If no host is selected but there are conversations, select the first one
else if (!empty($conversations)) {
    $firstConversation = $conversations[0];
    $activeHostId = ($firstConversation['sender_id'] == $travelerId) ? 
                    $firstConversation['receiver_id'] : 
                    $firstConversation['sender_id'];
    
    $activeHostData = $hostController->getHostById($activeHostId);
    
    if ($activeHostData) {
        $activeMessages = $messageController->getConversation($travelerId, $activeHostId, 'traveler', 'host');
        
        // Mark messages as read
        $messageController->markMessagesAsRead($travelerId, $activeHostId, 'traveler', 'host');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $activeHostId) {
    $messageData = [
        'sender_id' => $travelerId,
        'receiver_id' => $activeHostId,
        'message' => $_POST['message'],
        'status' => 'delivered',
        'is_read' => 0,
        'sender_type' => 'traveler',
        'receiver_type' => 'host'
    ];
    
    $result = $messageController->sendMessage($messageData);
    
    if ($result) {
        // Redirect to prevent form resubmission
        header("Location: messages.php?host_id=" . $activeHostId);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HomeStays - Messages</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="homestays, cultural exchange, messaging, traveler communication" name="keywords">
    <meta content="Communicate with hosts and manage your cultural exchange messages" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">
    
    <style>
        .conversation-item {
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .conversation-item:hover {
            background-color: #f8f9fa;
        }
        .conversation-item.active {
            background-color: #e9ecef;
        }
        .message-container {
            height: 400px;
            overflow-y: auto;
        }
        .message {
            padding: 10px 15px;
            border-radius: 15px;
            margin-bottom: 10px;
            max-width: 75%;
            position: relative;
        }
        .message-sent {
            background-color: #dcf8c6;
            margin-left: auto;
            border-bottom-right-radius: 5px;
        }
        .message-received {
            background-color: #f1f0f0;
            margin-right: auto;
            border-bottom-left-radius: 5px;
        }
        .message-time {
            font-size: 0.7rem;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }
        .unread-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <?php include 'navTraveler.php'; ?>
    <!-- Navbar End -->

    <!-- Messages Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <!-- Page Header -->
            <div class="text-center mb-5">
                <h1 class="mb-3">Messages</h1>
                <p class="mb-0">Communicate with hosts and manage your cultural exchange conversations</p>
            </div>

            <div class="row">
                <!-- Conversations List -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchConversations" placeholder="Search conversations...">
                                <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($conversations)): ?>
                                <div class="p-4 text-center">
                                    <p class="text-muted mb-0">No conversations yet.</p>
                                    <p class="text-muted">Start by contacting a host!</p>
                                    <a href="exchange.php" class="btn btn-primary mt-2">Find Hosts</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($conversations as $index => $conversation): 
                                    // Determine the other user's ID
                                    $otherUserId = ($conversation['sender_id'] == $travelerId) ? 
                                                $conversation['receiver_id'] : 
                                                $conversation['sender_id'];
                                    
                                    // Get the other user's data
                                    $otherUserData = $hostController->getHostById($otherUserId);
                                    
                                    if (!$otherUserData) continue; // Skip if user data not found
                                    
                                    // Get unread message count
                                    $unreadCount = $messageController->getUnreadMessageCount($travelerId, 'traveler');
                                    
                                    // Get the last message
                                    $lastMessages = $messageController->getConversation($travelerId, $otherUserId, 'traveler', 'host');
                                    $lastMessage = end($lastMessages);
                                    
                                    // Calculate time ago
                                    $timeAgo = '';
                                    if ($lastMessage) {
                                        $sentTime = strtotime($lastMessage['timestamp']);
                                        $now = time();
                                        $diff = $now - $sentTime;
                                        
                                        if ($diff < 60) {
                                            $timeAgo = 'Just now';
                                        } elseif ($diff < 3600) {
                                            $timeAgo = floor($diff / 60) . 'm ago';
                                        } elseif ($diff < 86400) {
                                            $timeAgo = floor($diff / 3600) . 'h ago';
                                        } else {
                                            $timeAgo = floor($diff / 86400) . 'd ago';
                                        }
                                    }
                                    
                                    // Determine if this conversation is active
                                    $isActive = $otherUserId == $activeHostId;
                                ?>
                                    <div class="p-3 border-bottom conversation-item <?= $isActive ? 'active' : '' ?>" 
                                         onclick="window.location.href='messages.php?host_id=<?= $otherUserId ?>'">
                                        <div class="d-flex align-items-center">
                                            <img src="../Controllers/GetProfileImg.php?user_id=<?= $otherUserId ?>" 
                                                 class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="Host">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?= htmlspecialchars($otherUserData['first_name'] . ' ' . $otherUserData['last_name']) ?></h6>
                                                <p class="mb-0 text-muted small">
                                                    <?= $lastMessage ? (strlen($lastMessage['content']) > 30 ? 
                                                        htmlspecialchars(substr($lastMessage['content'], 0, 30)) . '...' : 
                                                        htmlspecialchars($lastMessage['content'])) : 'No messages yet' ?>
                                                </p>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted"><?= $timeAgo ?></small>
                                                <?php if ($unreadCount > 0): ?>
                                                    <span class="badge bg-primary rounded-pill ms-2"><?= $unreadCount ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="col-lg-8">
                    <?php if ($activeHostData): ?>
                        <div class="card border-0 shadow-sm">
                            <!-- Chat Header -->
                            <div class="card-header bg-white">
                                <div class="d-flex align-items-center">
                                    <img src="../Controllers/GetProfileImg.php?user_id=<?= $activeHostId ?>" 
                                         class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="Host">
                                    <div>
                                        <h5 class="mb-1"><?= htmlspecialchars($activeHostData['first_name'] . ' ' . $activeHostData['last_name']) ?></h5>
                                        <p class="mb-0 text-muted"><?= htmlspecialchars($activeHostData['location']) ?></p>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="view_host.php?id=<?= $activeHostId ?>" class="btn btn-sm btn-outline-primary">View Profile</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Chat Messages -->
                            <div class="card-body message-container">
                                <?php if (empty($activeMessages)): ?>
                                    <div class="text-center my-5">
                                        <p class="text-muted">No messages yet. Start the conversation!</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($activeMessages as $message): ?>
                                        <div class="d-flex mb-4 <?= $message['sender_id'] == $travelerId ? 'justify-content-end' : '' ?>">
                                            <?php if ($message['sender_id'] != $travelerId): ?>
                                                <img src="../Controllers/GetProfileImg.php?user_id=<?= $activeHostId ?>" 
                                                     class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;" alt="Host">
                                            <?php endif; ?>
                                            <div class="<?= $message['sender_id'] == $travelerId ? 'message message-sent' : 'message message-received' ?>">
                                                <?= nl2br(htmlspecialchars($message['content'])) ?>
                                                <div class="message-time">
                                                    <?= date('M d, Y g:i A', strtotime($message['timestamp'])) ?>
                                                </div>
                                            </div>
                                            <?php if ($message['sender_id'] == $travelerId): ?>
                                                <img src="../Controllers/GetProfileImg.php?user_id=<?= $travelerId ?>" 
                                                     class="rounded-circle ms-3" style="width: 40px; height: 40px; object-fit: cover;" alt="You">
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Message Input -->
                            <div class="card-footer bg-white">
                                <form action="messages.php?host_id=<?= $activeHostId ?>" method="post">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="message" placeholder="Type your message..." required>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-comments fa-4x text-muted mb-4"></i>
                                <h4>No Conversation Selected</h4>
                                <p class="text-muted">Select a conversation from the list or start a new one by contacting a host.</p>
                                <a href="exchange.php" class="btn btn-primary mt-3">Find Hosts</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Messages End -->

    <!-- Footer Start -->
    <?php include '../Common/footer.php'; ?>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    
    <script>
        // Auto-scroll to the bottom of the message container
        document.addEventListener('DOMContentLoaded', function() {
            const messageContainer = document.querySelector('.message-container');
            if (messageContainer) {
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
            
            // Search functionality
            const searchInput = document.getElementById('searchConversations');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const conversationItems = document.querySelectorAll('.conversation-item');
                    
                    conversationItems.forEach(item => {
                        const userName = item.querySelector('h6').textContent.toLowerCase();
                        const lastMessage = item.querySelector('p').textContent.toLowerCase();
                        
                        if (userName.includes(searchTerm) || lastMessage.includes(searchTerm)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</body>

