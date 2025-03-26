<?php
class Category {
    // DB connection and table vars
    private $conn;
    public $id;
    public $category;

    // DB connection constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all categories...
    public function read() {
        // SQL query of all & run it
        $query = 'SELECT * FROM categories ORDER BY id';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Read a single category...
    public function read_single($id) {
        // SQL query of single category & run it
        $query = 'SELECT * FROM categories WHERE id = :id LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If/else check for setting result
        if ($row) {
            $this->id = $row['id'];
            $this->category = $row['category'];
            return true;
        }
        return false;
    }

    // Create new category...
    public function create() {
        // SQL query & run it
        $query = 'INSERT INTO categories (category) VALUES (:category)';
        $stmt = $this->conn->prepare($query);
        $this->category = htmlspecialchars(strip_tags($this->category));
        $stmt->bindParam(':category', $this->category);

        return $stmt->execute();
    }

    // Update category...
    public function update() {
        // SQL query and run it
        $query = 'UPDATE categories SET category = :category WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Set values
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Delete category...
    public function delete() {
        // SQL query and run it...
        $query = 'DELETE FROM categories WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
}
