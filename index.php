<?php
include 'Instagram.php';

$access_token = 'IGQVJVVTBHMU5aYlBIREFraDRIMTYzTmp0YU9sV2JnQXpKTzZAUTXRBak9TRWVPRGhWclNJMzZAPQnJqYmlOUlQwQ1JBRkV0ejYzVEUtVHAxWEZAvQUxpYWFnUFo2NmNINGN0c1lKQWhyakQ4TmdyYTh1SgZDZD';
$Instagram = new Instagram($access_token);

//get user
$user = $Instagram->getUserData();

//get posts
$posts = $Instagram->getUserMedia();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Instagram feed</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            background-color: #fafafa;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }

        .container {
            margin: 50px auto;
            max-width: 1000px;
        }

        .posts_container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .post {
            width: 32%;
            margin-bottom: 2%;
        }

        img {
            width: 100%;
            height: auto;
        }

        source {
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        echo "<h1>@" . $user['username'] . "</h1>";
        ?>
        <div class="posts_container">
            <?php

            //first posts
            foreach ($posts['data'] as $post) {
                switch ($post['media_type']) {
                    case 'CAROUSEL_ALBUM':
                        $album_images = $Instagram->getUserMediaChildren($post['id']);

                        foreach ($album_images['data'] as $album_image) {
                            if ($album_image['media_type'] == 'VIDEO') {
                                echo '<video class="post" controls>';
                                echo '<source src="' . $album_image['media_url'] . '" type="video/mp4">';
                                echo '</video>';
                            } else {
                                echo '<div class="post">';
                                echo "<img src=" . $album_image['media_url'] . ">";
                                echo '</div>';
                            }
                        }
                        break;
                    case 'VIDEO':
                        echo '<video class="post" controls>';
                        echo '<source src="' . $post['media_url'] . '" type="video/mp4">';
                        echo '</video>';
                        break;
                    default:
                        echo '<div class="post">';
                        echo "<img src=" . $post['media_url'] . ">";
                        echo '</div>';
                }
            }

            ?>
        </div>
    </div>
</body>

</html>