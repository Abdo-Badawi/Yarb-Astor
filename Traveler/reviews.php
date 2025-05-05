<?php
include_once '../Controllers/ReviewController.php';

$reviewController = new ReviewController();
$reviews = $reviewController->getReviewsByUser($_SESSION['userID']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Reviews</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4">User Reviews</h1>

    <?php if (isset($reviews['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($reviews['error']); ?></div>
    <?php elseif (empty($reviews)): ?>
        <div class="alert alert-info">No reviews found.</div>
    <?php else: ?>
        <div class="row gy-4">
            <?php foreach ($reviews as $review): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo htmlspecialchars($review['sender_picture']); ?>" alt="Sender Picture" class="rounded-circle" width="50" height="50">
                                    <span class="fw-bold text-capitalize"><?php echo htmlspecialchars($review['sender_name']); ?></span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo htmlspecialchars($review['receiver_picture']); ?>" alt="Receiver Picture" class="rounded-circle" width="50" height="50">
                                    <span class="fw-bold text-capitalize"><?php echo htmlspecialchars($review['receiver_name']); ?></span>
                                </div>
                            </div>

                            <h5 class="card-title mb-2"><?php echo htmlspecialchars($review['opportunity_title']); ?></h5>
                            <p class="mb-1 text-muted"><i class="bi bi-geo-alt"></i> Location: <?php echo htmlspecialchars($review['location']); ?></p>
                            <p class="mb-3 text-muted"><i class="bi bi-tag"></i> Category: <?php echo htmlspecialchars($review['category']); ?></p>

                            <p class="mb-2"><i class="bi bi-star text-warning"></i> <strong>Rating:</strong> <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                            <p class="mb-2"><i class="bi bi-chat-dots"></i> <?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>

                            <p class="mt-3 small <?php echo $review['is_reported'] ? 'text-danger' : 'text-success'; ?>">
                                <i class="bi <?php echo $review['is_reported'] ? 'bi-flag-fill' : 'bi-check-circle-fill'; ?>"></i>
                                <?php echo $review['is_reported'] ? 'This review is reported' : 'Published'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
