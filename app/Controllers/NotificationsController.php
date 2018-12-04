<?php

namespace App\Controllers;

class NotificationsController extends Controller {

    public function getNotifications($request, $response, $args) {
        $id = $args['id'];

        $sql  = "SELECT * FROM relationship 
        WHERE NOT action_user_id = $id
        AND status = 0 AND (user_one_id = $id OR user_two_id = $id)";

        try {
            $stmt = $this->db->prepare($sql);

            $notifications = $this->db->query($sql)->fetchAll(\PDO::FETCH_OBJ);

            $stmt->execute();
        } catch(PDOException $e) {
            echo '{"error": {"text": '.$e->getMessage().'}}';
        }
    
        $res = json_encode($notifications);
        return $res;
    }

    public function sendFriendship($request, $response, $args) {
        $sender_id              = $request->getParam('id');
        $receiver_id            = $request->getParam('receiver_id');
        $action_user_avatar     = $request->getParam('avatar');
        $text                   = $request->getParam('text');

        $user_one_id = $sender_id > $receiver_id ? $receiver_id : $sender_id;
        $user_two_id = $receiver_id < $sender_id ? $sender_id : $receiver_id;
        
        $sql = "INSERT INTO 
            relationship (user_one_id,user_two_id,status,action_user_id,text,action_user_avatar) 
            VALUES 
            (:user_one_id,:user_two_id,0,:sender_id,:text,:action_user_avatar)";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':user_one_id', $user_one_id);
            $stmt->bindParam(':user_two_id', $user_two_id);
            $stmt->bindParam(':sender_id', $sender_id);
            $stmt->bindParam(':text', $text);
            $stmt->bindParam(':action_user_avatar', $action_user_avatar);

            $stmt->execute();
        } catch(PDOException $e) {
            echo '{"error": {"text": '.$e->getMessage().'}}';
        }
    }
    
    public function acceptFriendship($request, $response, $args) {
        $sender_id              = $request->getParam('sender_id');
        $receiver_id            = $request->getParam('receiver_id');

        $sql = "UPDATE relationship
        SET status = 1
        WHERE action_user_id = $sender_id 
        AND user_one_id = $receiver_id OR user_two_id = $receiver_id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } catch(PDOException $e) {
            echo '{"error": {"text": '.$e->getMessage().'}}';
        }

        return json_encode($sender_id);
    }

    public function rejectFriendship($request, $response, $args) {
        $sender_id              = $request->getParam('sender_id');
        $receiver_id            = $request->getParam('receiver_id');

        $sql = "DELETE FROM relationship
            WHERE action_user_id = $sender_id 
            AND user_one_id = $receiver_id OR user_two_id = $receiver_id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } catch(PDOException $e) {
            echo '{"error": {"text": '.$e->getMessage().'}}';
        }

        return json_encode($sender_id);
    }
}