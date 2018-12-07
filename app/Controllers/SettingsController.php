<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;
use Aws\S3\S3Client::factory() as s3;
$bucket = getenv('S3_BUCKET')?: die('No "S3_BUCKET" config var in found in env!');

class SettingsController extends Controller {

    public function uploadAvatar($request, $response) {
       /*  if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['userfile']) && $_FILES['userfile']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            FIXME: add more validation, e.g. using ext/fileinfo
            try {
               FIXME: do not use 'name' for upload (that's the original filename from the user's computer)
                $upload = $s3->upload($bucket, $_FILES['userfile']['name'], fopen($_FILES['userfile']['tmp_name'], 'rb'), 'public-read');
        ?>
                <p>Upload <a href="<?=htmlspecialchars($upload->get('ObjectURL'))?>">successful</a> :)</p>
        <?php } catch(Exception $e) { ?>
                <p>Upload error :(</p>
        <?php } } */
        $files = $request->getUploadedFiles();
        $userId = $request->getParam('userId');
        $avatar = $files['file'];

        if (!file_exists("$userId/")) {
            mkdir("$userId/");
        }

        $fileSearch = "avatar";
        $files = glob("D:/Soft/xampp/htdocs/socialnetwork/server/public/$userId/*" . $fileSearch . "*");

        if(count($files) > 0) unlink($files[0]);
        
        if($avatar->getError() === UPLOAD_ERR_OK) {
            $name = explode(".", $avatar->getClientFilename());
            $name[0] = "avatar" . uniqid();
            $name = join(".", $name);
            $whitelist = array('127.0.0.1','::1');
            
            if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
                $avatar->moveTo("D:/Soft/xampp/htdocs/socialnetwork/server/public/$userId/$name");
            } else {
                $avatar->moveTo("D:/Soft/xampp/htdocs/socialnetwork/server/public/$userId/$name");
            }

            $photoURL="http://socialnetwork/$userId/$name";

            $sql = "UPDATE users SET 
            users.avatar = '$photoURL'
            WHERE users.id = $userId";
            
            $stmt = $this->db->prepare($sql);
            
            $avatar = $this->db->query($sql);
            
            return $response->withJson($photoURL);
        } 
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