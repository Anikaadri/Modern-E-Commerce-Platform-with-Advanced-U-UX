<?php
/**
 * Review Controller
 */

require_once __DIR__ . '/../models/Review.php';

class ReviewController {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    /**
     * Get reviews by product ID
     */
    public function getReviewsByProductId($productId) {
        $query = "SELECT r.*, u.name as user_name 
                  FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.product_id = :product_id 
                  ORDER BY r.created_at DESC";
        
        $results = $this->db->query($query, [':product_id' => $productId]);
        
        $reviews = [];
        if ($results) {
            foreach ($results as $row) {
                $reviews[] = new Review($row);
            }
        }
        
        return $reviews;
    }

    /**
     * Add a new review
     */
    public function addReview($data) {
        $review = new Review($data);
        $errors = $review->validate();

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check if user already reviewed this product
        $checkQuery = "SELECT id FROM reviews WHERE product_id = :product_id AND user_id = :user_id";
        $existing = $this->db->query($checkQuery, [
            ':product_id' => $review->productId,
            ':user_id' => $review->userId
        ]);

        if ($existing) {
            return ['success' => false, 'errors' => ['You have already reviewed this product.']];
        }

        $query = "INSERT INTO reviews (product_id, user_id, rating, comment) 
                  VALUES (:product_id, :user_id, :rating, :comment)";
        
        $result = $this->db->execute($query, [
            ':product_id' => $review->productId,
            ':user_id' => $review->userId,
            ':rating' => $review->rating,
            ':comment' => $review->comment
        ]);

        if ($result) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => ['Failed to save review.']];
        }
    }

    /**
     * Get average rating for a product
     */
    public function getAverageRating($productId) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as count 
                  FROM reviews 
                  WHERE product_id = :product_id";
        
        $result = $this->db->query($query, [':product_id' => $productId]);
        
        if ($result && $result[0]['count'] > 0) {
            return [
                'rating' => round($result[0]['avg_rating'], 1),
                'count' => $result[0]['count']
            ];
        }
        
        return ['rating' => 0, 'count' => 0];
    }
}
