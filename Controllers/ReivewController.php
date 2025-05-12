<?php
include_once '../Models/Review.php';
class ReivewController {
    public function getReviews($userId){
        $review = new Reivew();
        $reviews = $review->getReviewsByUser($userId);
        return $reviews;
    }
}
?>
