<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;
use Google\Cloud\Storage\StorageClient;

class SettingsController extends Controller {

    public function uploadAvatar($request, $response) {
        $projectId = 'social-network-224817';

        $storage = new StorageClient([
            'projectId' => $projectId
        ]);
        $userId = $request->getParam('userId');

        $bucketName = 'files-of-' . $userId;
        $bucket = $storage->createBucket($bucketName);

         $files = $request->getUploadedFiles();
         $avatar = $files['file'];
         return json_encode($_FILES);
 
         $file = fopen($avatar['tmp_name'], 'r');
         $bucket = $storage->bucket($bucketName);
         $object = $bucket->upload($file, [
             'name' => `avatar-$userId`
         ]);
    }

    public function changeData($request, $response) {
        $error = array();

        $id                   = $request->getParam('id');
        $validateEmail        = v::notEmpty()->email()->noWhitespace()->validate($request->getParam('email'));
        $email                = $validateEmail ? $request->getParam('email') : $error[] = 'email';
        $validateName         = v::notEmpty()->alnum()->length(2, null)->noWhitespace()->validate($request->getParam('name'));
        $name                 = $validateName ? $request->getParam('name') : $error[] = 'name';
        $validateSurname      = v::notEmpty()->noWhitespace()->length(2, null)->validate($request->getParam('surname'));
        $surname              = $validateSurname ? $request->getParam('surname') : $error[] = 'surname';
        $gender               = $request->getParam('gender');
        $country              = $request->getParam('country');
        $birth                = "{$request->getParam('birth')['day']} {$request->getParam('birth')['month']} {$request->getParam('birth')['year']}";
        
        if(!empty($error)) {
            return $this->errorHandler($response, 400, 'Next fields are in incorrect form: '. implode(", ", $error).'');
        }

        if (!empty($request->getParam('password'))) {
            $validatePass = v::alnum()->length(6, null)->noWhitespace()->validate($request->getParam('password'));
            $password     = $validatePass ? password_hash($request->getParam('password'), PASSWORD_DEFAULT) : $error[] = 'password';
            
            $sql = "UPDATE users SET 
            users.email = '$email',
            users.password = '$password',
            users.name = '$name',
            users.surname = '$surname',
            users.gender = '$gender',
            users.birth = '$birth',
            users.country = '$country'
            WHERE users.id = $id";

            try {
                $stmt = $this->db->prepare($sql);

                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':surname', $surname);
                $stmt->bindParam(':gender', $gender);
                $stmt->bindParam(':birth', $birth);
                $stmt->bindParam(':country', $country);

                $stmt->execute();
            } catch(PDOException $e) {
                echo '{"error": {"text": '.$e->getMessage().'}}';
            }
        } elseif(empty($request->getParam('password'))) {
            $sql = "UPDATE users SET 
            users.email = '$email',
            users.name = '$name',
            users.surname = '$surname',
            users.gender = '$gender',
            users.birth = '$birth',
            users.country = '$country'
            WHERE users.id = $id";

            try {
                $stmt = $this->db->prepare($sql);

                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':surname', $surname);
                $stmt->bindParam(':gender', $gender);
                $stmt->bindParam(':birth', $birth);
                $stmt->bindParam(':country', $country);

                $stmt->execute();
            } catch(PDOException $e) {
                echo '{"error": {"text": '.$e->getMessage().'}}';
            }
        }

        $sql = "SELECT * FROM users WHERE id = $id";

        $stmt = $this->db->prepare($sql);

        $updatedUser = $this->db->query($sql)->fetchAll(\PDO::FETCH_OBJ);

        $res = json_encode($updatedUser);
        return $res;
    }
}
