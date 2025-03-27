<?php
class Quote {
    // DB conn & table vars
    private $conn;
    public $id;
    public $quote;
    public $author_id;
    public $category_id;
    public $author;
    public $category;

    // DB conn constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all quotes...
    public function read() {
        // SQL query
        $query = '
            SELECT q.id, q.quote, a.author, c.category
            FROM quotes q
            JOIN authors a ON q.author_id = a.id
            JOIN categories c ON q.category_id = c.id
            ORDER BY q.id
        ';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        // Return found data/value
        return $stmt;
    }

    // Read one quote...
    public function read_single($id) {
        // SQL query
        $query = '
            SELECT q.id, q.quote, q.author_id, q.category_id, a.author, c.category
            FROM quotes q
            JOIN authors a ON q.author_id = a.id
            JOIN categories c ON q.category_id = c.id
            WHERE q.id = :id
            LIMIT 1
        ';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set values
        if ($row) {
            $this->id = $row['id'];
            $this->quote = $row['quote'];
            $this->author_id = $row['author_id'];
            $this->category_id = $row['category_id'];
            $this->author = $row['author'];
            $this->category = $row['category'];
            return true;
        }

        // Otherwise none present
        return false;
    }

    // Create new quote...
    public function create() {
        // Run SQL query
        $query = 'INSERT INTO quotes (quote, author_id, category_id) VALUES (:quote, :author_id, :category_id)';
        $stmt = $this->conn->prepare($query);
        $this->quote = htmlspecialchars(strip_tags($this->quote));

        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);

        return $stmt->execute();
    }

    // Update quote...
    public function update() {
        // Run SQL query
        $query = 'UPDATE quotes SET quote = :quote, author_id = :author_id, category_id = :category_id WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $this->quote = htmlspecialchars(strip_tags($this->quote));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Delete quote...
    public function delete() {
        // Run SQL query
        $query = 'DELETE FROM quotes WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
