<<<<<<< HEAD
<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/db.php';

$app = new \Slim\App(['settings' => $config]);

require __DIR__ . '/../app/dependencies.php';
require __DIR__ . '/../app/routes.php';

=======
<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/db.php';

$app = new \Slim\App(['settings' => $config]);

require __DIR__ . '/../app/dependencies.php';
require __DIR__ . '/../app/routes.php';

>>>>>>> ded2931a342082769828c793eaf6bfa71a718c85
$app->run();