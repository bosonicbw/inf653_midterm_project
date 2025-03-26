<?php
class Author {
    // DB connection and table vars
    private $conn; // !!! NOTE- Should this be private?
    public $id;
    public $author;

    // DB connection constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all authors...
    public function read() {
        // SQL query of all & run it
        $query = 'SELECT * FROM authors ORDER BY id';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Read a single author...
    public function read_single($id) {
        // SQL query of single author & run it
        $query = 'SELECT * FROM authors WHERE id = :id LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If/else check for setting result
        if ($row) {
            $this->id = $row['id'];
            $this->author = $row['author'];
            return true;
        }
        return false;
    }

    // Create new author...
    public function create() {
        // SQL query & run it
        $query = 'INSERT INTO authors (author) VALUES (:author)';
        $stmt = $this->conn->prepare($query);
        $this->author = htmlspecialchars(strip_tags($this->author));
        $stmt->bindParam(':author', $this->author);

        return $stmt->execute();
    }

    // Update author...
    public function update() {
        // SQL query and run it
        $query = 'UPDATE authors SET author = :author WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $this->author = htmlspecialchars(strip_tags($this->author));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Set values
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Delete author...
    public function delete() {
        // SQL query and run it...
        $query = 'DELETE FROM authors WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}