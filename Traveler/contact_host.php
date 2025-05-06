<?php
session_start();
require_once '../Controllers/HostController.php';
require_once '../Controllers/MessageController.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../Common/login.php");
    exit;
}

// Check if host ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: exchange.php");
    exit;
}

$hostId = (int)$_GET['id'];
$travelerId = $_SESSION['userID'];

// Create controllers
$hostController = new HostController();
$messageController = new MessageController();

// Get host details
$hostData = $hostController->getHostById($hostId);

// Check if host exists
if (!$hostData) {
    header("Location: exchange.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageData = [
        'sender_id' => $travelerId,
        'receiver_id' => $hostId,
        'message' => $_POST['message'],
        'sent_date' => date('Y-m-d H:i:s'),
        'is_read' => 0,
        'sender_type' => 'traveler',
        'receiver_type' => 'host'
    ];
    
    $result = $messageController->sendMessage($messageData);
    
    if ($result) {
        $_SESSION['success_message'] = "Your message has been sent successfully!";
        // Redirect to prevent form resubmission
        header("Location: contact_host.php?id=" . $hostId);
        exit;
    } else {
        $_SESSION['error_message'] = "Failed to send your message. Please try again.";
    }
}

// Get conversation history
$messages = $messageController->getConversation($travelerId, $hostId, 'traveler', 'host');

// Mark messages as read
$messageController->markMessagesAsRead($travelerId, $hostId, 'traveler', 'host');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HomeStays - Contact Host</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="homestays, cultural exchange, local experience, authentic travel" name="keywords">
    <meta content="Contact your host for this cultural exchange" name="description">

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
        .message-container {
            height: 400px;
            overflow-y: auto;
        }
        
        .message {
            border-radius: 15px;
            padding: 10px 15px;
            margin-bottom: 10px;
            max-width: 75%;
        }
        
        .message-sent {
            background-color: #dcf8c6;
            margin-left: auto;
        }
        
        .message-received {
            background-color: #f1f0f0;
        }
        
        .message-time {
            font-size: 0.7rem;
            color: #777;
            text-align: right;
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
 
    <!-- Contact Host Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                <h5 class="section-title px-3">Contact Host</h5>
                <h1 class="mb-0">Message <?= htmlspecialchars($hostData['first_name'] . ' ' . $hostData['last_name']) ?></h1>
            </div>
            
            <!-- Alert Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <img src="../Controllers/GetProfileImg.php?user_id=<?= $hostData['host_id'] ?>" 
                                     class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;" alt="Host">
                                <div>
                                    <h5 class="mb-1"><?= htmlspecialchars($hostData['first_name'] . ' ' . $hostData['last_name']) ?></h5>
                                    <p class="mb-0 text-muted"><?= htmlspecialchars($hostData['location']) ?></p>
                                </div>
                            </div>
                            
                            <!-- Message History -->
                            <div class="message-container mb-4 p-3 border rounded">
                                <?php if (empty($messages)): ?>
                                    <p class="text-center text-muted">No messages yet. Start the conversation!</p>
                                <?php else: ?>
                                    <?php foreach ($messages as $msg): ?>
                                        <div class="message <?= $msg['sender_id'] == $travelerId ? 'message-sent' : 'message-received' ?>">
                                            <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                            <div class="message-time">
                                                <?= date('M d, Y g:i A', strtotime($msg['sent_date'])) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Message Form -->
                            <form action="" method="post">
                                <div class="mb-3">
                                    <label for="message" class="form-label">Your Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="exchange.php" class="btn btn-outline-secondary">Back to Opportunities</a>
                                    <button type="submit" class="btn btn-primary">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact Host End -->

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
            messageContainer.scrollTop = messageContainer.scrollHeight;
        });
    </script>
</body>
</html>
