<?php

    session_start();

    require '../Controllers/Classes/Database.php';
    require '../Controllers/Classes/Auth.php';
    require '../Controllers/Classes/User.php';
    require '../Controllers/Classes/Post.php';
    require '../Controllers/Classes/Comment.php';
    require '../Controllers/Classes/Helpers.php';

    header('Content-Type: application/json');

    $db = new Database();
    $user = new User($db);
    $helpers = new Helpers();
    $post = new Post($db);
    $comment = new Comment($db);

    // Check if access token does not exist

    if (!$user -> isAccessTokenExists()) {
        echo json_encode([
            'result' => false,
            'error' => 'You are not authorized to perform this action'
        ]);
        exit;
    }

    $get_payload_date = file_get_contents('php://input');
    $data = json_decode($get_payload_date);

    switch ($_SERVER["REQUEST_METHOD"]) {

        // Create Comment

        case 'POST':
            
            $post_id = isset($data -> postId) ? $data -> postId : '';
            $post_content = isset($data -> commentContent) ? $data -> commentContent : '';

            $post -> setId($post_id);

            if (!$post -> createComment($post_content, $user -> getId())) {
                echo json_encode([
                    'result' => false,
                    'error' => 'Could not process your request'
                ]);
            } else {
                echo json_encode([
                    'result' => true,
                    'error' => '',
                    'details' => [
                        'firstName' => $user -> getFirstName(),
                        'lastName' => $user -> getLastName(),
                        'content' => $helpers -> format_text($post_content)
                    ]
                ]);
            }

            break;
        
        // Update comment

        case 'PUT':

            $comment_id = isset($data -> commentId) ? $data -> commentId : '';
            $comment_content = isset($data -> commentContent) ? $data -> commentContent : '';
    
            $comment -> setId($comment_id);
            $comment -> setContent($comment_content);
    
            if (!$comment -> updateComment()) {
                echo json_encode([
                    'result' => false,
                    'error' => $comment -> getErrors()[0]
                ]);
            } else {
                echo json_encode([
                    'result' => true,
                    'error' => '',
                    'content' => $helpers -> format_text($comment_content)
                ]);
            }

            break;

        // Delete comment

        case 'DELETE':

            $comment_id = isset($data -> commentId) ? $data -> commentId : '';

            $comment -> setId($comment_id);

            if (!$comment -> deleteComment()) {
                echo json_encode([
                    'result' => false,
                    'error' => $comment -> getErrors()[0]
                ]);
            } else {
                echo json_encode([
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
    