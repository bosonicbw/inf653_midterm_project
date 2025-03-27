<?php
// Header supplied from prof - thanks!
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

// Connect DB config & Quote
include_once '../../config/Database.php';
include_once '../../models/Quote.php';

// Instantiate DB & Quote
$database = new Database();
$db = $database->connect();
$quote = new Quote($db);

// Switch/case for needed action
switch($method) {
    // GET case for read quotes...
    case 'GET':
        if (isset($_GET['id'])) {
            // For exact quote
            $found = $quote->read_single($_GET['id']);
            if ($found) {
                echo json_encode([
                    'id' => $quote->id,
                    'quote' => $quote->quote,
                    'author' => $quote->author,
                    'category' => $quote->category
                ]);
            } else {
                echo json_encode(['message' => 'No Quotes Found']);
            }

        } else if (isset($_GET['author_id']) && isset($_GET['category_id'])) {
            // For certain author & category
            $query = "
                SELECT q.id, q.quote, a.author, c.category
                FROM quotes q
                JOIN authors a ON q.author_id = a.id
                JOIN categories c ON q.category_id = c.id
                WHERE q.author_id = :author_id AND q.category_id = :category_id
            ";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':author_id', $_GET['author_id']);
            $stmt->bindParam(':category_id', $_GET['category_id']);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo $results ? json_encode($results) : json_encode(['message' => 'No Quotes Found']);

        } else if (isset($_GET['author_id'])) {
            // For certain author
            $query = "
                SELECT q.id, q.quote, a.author, c.category
                FROM quotes q
                JOIN authors a ON q.author_id = a.id
                JOIN categories c ON q.category_id = c.id
                WHERE q.author_id = :author_id
            ";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':author_id', $_GET['author_id']);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo $results ? json_encode($results) : json_encode(['message' => 'No Quotes Found']);

        } else if (isset($_GET['category_id'])) {
            // For certain category
            $query = "
                SELECT q.id, q.quote, a.author, c.category
                FROM quotes q
                JOIN authors a ON q.author_id = a.id
                JOIN categories c ON q.category_id = c.id
                WHERE q.category_id = :category_id
            ";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':category_id', $_GET['category_id']);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo $results ? json_encode($results) : json_encode(['message' => 'No Quotes Found']);

        } else {
            // Otherwise read all
            $result = $quote->read();
            $num = $result->rowCount();

            if ($num > 0) {
                $quotes_arr = [];

                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $quotes_arr[] = [
                        'id' => $row['id'],
                        'quote' => $row['quote'],
                        'author' => $row['author'],
                        'category' => $row['category']
                    ];
                }

                echo json_encode($quotes_arr);
            } else {
                echo json_encode(['message' => 'No Quotes Found']);
            }
        }
        break;

    // POST case for creating quotes...
    case 'POST':
        // Gather data var
        $data = json_decode(file_get_contents("php://input"));

        // If/else statements for checking required elements
        if (!empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {
            $quote->quote = $data->quote;
            $quote->author_id = $data->author_id;
            $quote->category_id = $data->category_id;

            // Needed Author check to comply with tester website critera
            $checkAuthor = $db->prepare("SELECT id FROM authors WHERE id = :id");
            $checkAuthor->bindParam(':id', $quote->author_id);
            $checkAuthor->execute();
            if ($checkAuthor->rowCount() === 0) {
                echo json_encode(['message' => 'author_id Not Found']);
                break;
            }

            // Needed Category check to comply with tester website criteria
            $checkCategory = $db->prepare("SELECT id FROM categories WHERE id = :id");
            $checkCategory->bindParam(':id', $quote->category_id);
            $checkCategory->execute();
            if ($checkCategory->rowCount() === 0) {
                echo json_encode(['message' => 'category_id Not Found']);
                break;
            }

            // And check required elements...
            if ($quote->create()) {
                echo json_encode([
                    'id' => $db->lastInsertId(),
                    'quote' => $quote->quote,
                    'author_id' => $quote->author_id,
                    'category_id' => $quote->category_id
                ]);
            } else {
                echo json_encode(['message' => 'No Quotes Found']);
            }
        } else {
            echo json_encode(['message' => 'No Quotes Found']);
        }
        break;

    // PUT case for updating quotes...
    case 'PUT':
        // Gather data var
        $data = json_decode(file_get_contents("php://input"));

        // If/else statements for checking required elements
        if (!empty($data->id) && !empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {
            $quote->id = $data->id;
            $quote->quote = $data->quote;
            $quote->author_id = $data->author_id;
            $quote->category_id = $data->category_id;

            // Needed Author check to comply with tester website critera
            $checkAuthor = $db->prepare("SELECT id FROM authors WHERE id = :id");
            $checkAuthor->bindParam(':id', $quote->author_id);
            $checkAuthor->execute();
            if ($checkAuthor->rowCount() === 0) {
                echo json_encode(['message' => 'author_id Not Found']);
                break;
            }

            // Needed Category check to comply with tester website criteria
            $checkCategory = $db->prepare("SELECT id FROM categories WHERE id = :id");
            $checkCategory->bindParam(':id', $quote->category_id);
            $checkCategory->execute();
            if ($checkCategory->rowCount() === 0) {
                echo json_encode(['message' => 'category_id Not Found']);
                break;
            }

            // And check required elements...
            if ($quote->update()) {
                echo json_encode([
                    'id' => $quote->id,
                    'quote' => $quote->quote,
                    'author_id' => $quote->author_id,
                    'category_id' => $quote->category_id
                ]);
            } else {
                echo json_encode(['message' => 'No Quotes Found']);
            }
        } else {
            echo json_encode(['message' => 'No Quotes Found']);
        }
        break;

    // DELETE case for deleting quotes...
    case 'DELETE':
        // Gather data var
        $data = json_decode(file_get_contents("php://input"));

        // If/else statements for checking required elements
        if (!empty($data->id)) {
            $quote->id = $data->id;
            if ($quote->delete()) {
                echo json_encode(['id' => $quote->id]);
            } else {
                echo json_encode(['message' => 'No Quotes Found']);
            }
        } else {
            echo json_encode(['message' => 'No Quotes Found']);
        }
        break;

    // Otherwise not available...
    default:
        echo json_encode(['message' => 'Method not found or available, try again!']);
}