<?php

    session_start();

    require '../Controllers/Classes/Database.php';
    require '../Controllers/Classes/Auth.php';
    require '../Controllers/Classes/User.php';
    require '../Controllers/Classes/Post.php';
    require '../Controllers/Classes/Comment.php';
    require '../Controllers/Classes/Tag.php';
    require '../Controllers/Classes/Helpers.php';

    header('Content-Type: application/json');

    $db = new Database();
    $user = new User($db);
    $helpers = new Helpers();
    $post = new Post($db);

    // Check if access token does not exist

    switch ($_SERVER["REQUEST_METHOD"]) {

        // Display Posts

        case 'GET':
            
            // $id 

            $ids = "";
            $search = "";

            if (isset($_SERVER["QUERY_STRING"])) {
                try {
                    parse_str($_SERVER["QUERY_STRING"], $result);

                    $search = str_replace('search=', '', $result["search"]);
                    $ids = rtrim(str_replace('tags=', '', $result["tags"]), ',');
                } catch (Exception) {
                    echo json_encode([
                        'result' => false,
                        'error' => 'Invalid Request'
                    ]);
                    exit;
                }
            }

            if ($ids == "") {
                $id_list = [];
            } else {
                $id_list = explode(',', $ids);
            }


            $filtered_posts = $post -> filterPost(trim($search), $id_list);

            echo json_encode([
                'result' => true,
                'error' => "",
                'response' => $filtered_posts,
                'keyword' => $search,
                'errorrr' => $db -> getError()
            ]);

            break;

        // Update Post

        case 'PUT':

            if (!$user -> isAccessTokenExists()) {
                echo json_encode([
                    'result' => false,
                    'error' => 'You are not authorized to perform this action'
                ]);
                exit;
            }
        
            $get_payload_date = file_get_contents('php://input');
            $data = json_decode($get_payload_date, true);

            $post_id = isset($data['postId']) ? $data['postId'] : '';
            $post_title = isset($data['postTitle']) ? $data['postTitle'] : '';
            $post_content = isset($data['postContent']) ? $data['postContent'] : '';
            $tags_string = isset($data['tags']) ? $data['tags'] : '';

            if (trim($tags_string) == "") {
                echo json_encode([
                    'result' => false,
                    'error' => "Please select at least one tag"
                ]);
                exit;
            }
    
            $post -> setId($post_id);
            $post -> setTitle($post_title);
            $post -> setContent($post_content);

            $tags_list = explode(',', rtrim($tags_string, ','));
    
            if (!$post -> updatePost()) {
                echo json_encode([
                    'result' => false,
                    'error' => $post -> getErrors()[0]
                ]);
            } else {

                $returned_tags_list = [];
                foreach($tags_list as $tag_item) {
                    $tag = new Tag($db, $tag_item);
                    $post -> addTag($tag);
                    $returned_tags_list[] = [$tag -> getName(), $tag -> getId()];
                }

                echo json_encode([
                    'result' => true,
                    'error' => '',
                    'tags' => json_encode($returned_tags_list)
                    // 'content' => $helpers -> format_text($comment_content)
                ]);
            }

            break;
        
        default:
            echo json_encode([
                'result' => false,
                'error' => 'Invalid Request!'
            ]);
    }
    