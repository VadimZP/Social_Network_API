<?php

namespace App\Controllers;

class PostsController extends Controller {

    public function getAllPosts($request, $response, $args) {
        $author = $args['author'];

        $sql  = "SELECT * FROM posts WHERE author = $author";
        
        try {
            $stmt = $this->db->prepare($sql);

            $posts = $this->db->query($sql)->fetchAll(\PDO::FETCH_OBJ);

            $stmt->execute();
        } catch(PDOException $e) {
            echo '{"error": {"text": '.$e->getMessage().'}}';
        }
    
        $res = json_encode($posts);
        return $res;
    }

    public function getPost($request, $response, $args) {
        $post_id = $args['post_id'];

        $sql  = "SELECT * FROM posts WHERE author = $id and id = $post_id";
        
        try {
            $stmt = $this->db->prepare($sql);

            $post = $this->db->query($sql)->fetchAll(\PDO::FETCH_OBJ);

            $stmt->execute();
        } catch(PDOException $e) {
            echo '{"error": {"text": '.$e->getMessage().'}}';
        }
    
        $res = json_encode($post);
        return $res;
    }

    public function sendPost($request, $response, $args) {
        $text         = $request->getParam('text');
        $author       = $request->getParam('author');

        $sql = "INSERT INTO 
        posts (text,author,date) 
        VALUES 
        (:text,:author,CURRENT_TIMESTAMP)";
        
        try {
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':text', $text);
            $stmt->bindParam(':author', $author);

            $stmt->execute();
        } catch(PDOException $e) {
            echo '{"error": {"text": '.$e->getMessage().'}}';
        }
        
        $post = array(
            'text' => $text,
            'author' => $author,
        );

        $res = json_encode($post);
        return $res;
    }

    public function deletePost($request, $response, $args) {
        $post_id = $args['post_id'];

        $sql = "DELETE FROM posts
        WHERE post_id = $post_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } catch(PDOException $e) {
            echo '{"error": {"text": '.$e->getMessage().'}}';
        }
        
        return json_encode($post_id);
    }

    public function editPost($request, $response, $args) {
        $post_id       = $request->getParam('post_id');
        $text         = $request->getParam('text');

        $sql = "UPDATE posts SET 
        users.text = '$text',
        WHERE posts.id = $post_id";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':text', $text);
            $stmt->bindParam(':id', $post_id);

            $stmt->execute();
        } catch(PDOException $e) {
            echo '{"error": {"text": '.$e->getMessage().'}}';
        }

        $sql  = "SELECT * FROM posts WHERE author = $id and id = $post_id";

        $stmt = $this->db->prepare($sql);

        $updatedPost = $this->db->query($sql)->fetchAll(\PDO::FETCH_OBJ);

        $res = json_encode($updatedPost);
        return $res;
    }
}