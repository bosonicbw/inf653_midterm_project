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

// Connect DB config & Author.php
include_once '../../config/Database.php';
include_once '../../models/Author.php';

// Instantiate DB & Author
$database = new Database();
$db = $database->connect();
$author = new Author($db);

// Switch/case for needed action
switch($method) {
    // GET case for read authors...
    case 'GET':
        if (isset($_GET['id'])) {
            // Get one author
            $found = $author->read_single($_GET['id']);
            if ($found) {
                echo json_encode(['id' => $author->id, 'author' => $author->author]);
            } else {
                echo json_encode(['message' => 'author_id Not Found']);
            }
        } else {
            // Get all authors
            $result = $author->read();
            $num = $result->rowCount();

            // Check count
            if ($num > 0) {
                $authors_arr = [];

                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $authors_arr[] = ['id' => $id, 'author' => $author];
                }

                echo json_encode($authors_arr);
            } else {
                echo json_encode(['message' => 'No Authors Found']);
            }
        }
        break;

    // POST case for create authors..
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->author)) {
            $author->author = $data->author;

            if ($author->create()) {
                echo json_encode(['id' => $db->lastInsertId(), 'author' => $author->author]);
            } else {
                echo json_encode(['message' => 'Author Not Created']);
            }
        } else {
            echo json_encode(['message' => 'Missing Required Parameters']);
        }
        break;

    // PUT case for update authors...
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->author)) {
            $author->id = $data->id;
            $author->author = $data->author;

            if ($author->update()) {
                echo json_encode(['id' => $author->id, 'author' => $author->author]);
            } else {
                echo json_encode(['message' => 'Author Not Updated']);
            }
        } else {
            echo json_encode(['message' => 'Missing Required Parameters']);
        }
        break;

    // DELETE case for deleting authors...
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) {
            $author->id = $data->id;

            if ($author->delete()) {
                echo json_encode(['id' => $author->id]);
            } else {
                echo json_encode(['message' => 'No Authors Found']);
            }
        } else {
            echo json_encode(['message' => 'Missing Required Parameters']);
        }
        break;

    // Otherwise not available...
    default:
        echo json_encode(['message' => 'Method not found or available, try again!']);
}