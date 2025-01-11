<?php

    session_start();

    require '../Controllers/Classes/Database.php';
    require '../Controllers/Classes/Auth.php';
    require '../Controllers/Classes/User.php';
    require '../Controllers/Classes/Post.php';
    require '../Controllers/Classes/Comment.php';
    require '../Controllers/Classes/Reaction.php';
    require '../Controllers/Classes/Helpers.php';

    header('Content-Type: application/json');

    $db = new Database();
    $user = new User($db);
    $helpers = new Helpers();
    $post = new Post($db);
    $reaction = new Reaction($db);

    // Check if access token does not exist

    if (!$user -> isAccessTokenExists()) {
        echo json_encode([
            'result' => false,
            'error' => 'You are not authorized to perform this action'
        ]);
        exit;
    }

    $get_payload_date = file_get_contents('php://input');
    $data = json_decode($get_payload_date, true);

    switch ($_SERVER["REQUEST_METHOD"]) {

        // Add Reaction

        case 'POST':
            
            $post_id = isset($data['postId']) ? $data['postId'] : '';
            $react_type = isset($data['type']) ? $data['type'] : '';

            $post -> setId($post_id);

            if (!$post -> addReaction($react_type, $user -> getId())) {
                echo json_encode([
                    'result' => false,
                    'error' => $post -> getErrors()[0]
                ]);
            } else {
                echo json_encode([
                    'status' => 'Added',
                    'result' => true,
                    'error' => ''
                ]);
            }

            break;
        
        // Update comment

        case 'PUT':

            $post_id = isset($data['postId']) ? $data['postId'] : '';
            $react_type = isset($data['type']) ? $data['type'] : '';

            if (!$reaction -> updateReaction($react_type, $user -> getId(), $post_id)) {
                echo json_encode([
                    'result' => false,
                    'error' => $post -> getErrors()[0]
                ]);
            } else {
                echo json_encode([
                    'status' => 'Updated',
                    'result' => true,
                    'error' => ''
                ]);
            }

            break;

        // Delete Reaction

        case 'DELETE':

            $post_id = isset($data['postId']) ? $data['postId'] : '';

            if (!$reaction -> removeReaction($user -> getId(), $post_id)) {
                echo json_encode([
                    'result' => false,
                    'error' => $post -> getErrors()[0]
                ]);
            } else {
                echo json_encode([
                    'status' => 'Deleted',
                    'result' => true,
                    'error' => ''
                ]);
            }

            break;
        
        default:
            echo json_encode([
                'result' => false,
                'error' => 'Invalid Request!'
            ]);
    }
    