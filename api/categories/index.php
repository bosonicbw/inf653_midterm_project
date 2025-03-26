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

// Connect DB config & Category
include_once '../../config/Database.php';
include_once '../../models/Category.php';

// Instantiate DB & Category
$database = new Database();
$db = $database->connect();
$category = new Category($db);

// Switch/case for needed action
switch($method) {
    // GET case for read categories...
    case 'GET':
        if (isset($_GET['id'])) {
            // Get one category
            $found = $category->read_single($_GET['id']);
            if ($found) {
                echo json_encode(['id' => $category->id, 'category' => $category->category]);
            } else {
                echo json_encode(['message' => 'category_id Not Found']);
            }
        } else {
            // Get all categories
            $result = $category->read();
            $num = $result->rowCount();

            // Check count
            if ($num > 0) {
                $categories_arr = [];
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $categories_arr[] = ['id' => $id, 'category' => $category];
                }

                echo json_encode($categories_arr);
            } else {
                echo json_encode(['message' => 'No Categories Found']);
            }
        }
        break;

    // POST case for create categories...
    case 'POST':
        // Gather data var & if/else for checking required elements
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->category)) {
            $category->category = $data->category;
            if ($category->create()) {
                echo json_encode(['id' => $db->lastInsertId(), 'category' => $category->category]);
            } else {
                echo json_encode(['message' => 'Category Not Created']);
            }
        } else {
            echo json_encode(['message' => 'Missing Required Parameters']);
        }
        break;

    // PUT case for update categories...
    case 'PUT':
        // Gather data var & if/else for checking required elements
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->category)) {
            $category->id = $data->id;
            $category->category = $data->category;
            if ($category->update()) {
                echo json_encode(['id' => $category->id, 'category' => $category->category]);
            } else {
                echo json_encode(['message' => 'Category Not Updated']);
            }
        } else {
            echo json_encode(['message' => 'Missing Required Parameters']);
        }
        break;

    // DELETE case for deleting categories...
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) {
            $category->id = $data->id;
            if ($category->delete()) {
                echo json_encode(['id' => $category->id]);
            } else {
                echo json_encode(['message' => 'No Categories Found']);
            }
        } else {
            echo json_encode(['message' => 'Missing Required Parameters']);
        }
        break;

    // Otherwise not available...
    default:
        echo json_encode(['message' => 'Method not found or available, try again!']);
}
