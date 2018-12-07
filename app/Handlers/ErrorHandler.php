<<<<<<< HEAD
<?php

function ErrorHandler( $response, $statusCode, $errorMsg) {
  $body = json_encode(["status" => "error", "message" => $errorMsg], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

  return $response
          ->withStatus($statusCode)
          ->withHeader("Content-type", "application/json")
          ->write($body);
}
=======
<?php

function ErrorHandler( $response, $statusCode, $errorMsg) {
  $body = json_encode(["status" => "error", "message" => $errorMsg], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

  return $response
          ->withStatus($statusCode)
          ->withHeader("Content-type", "application/json")
          ->write($body);
}
>>>>>>> ded2931a342082769828c793eaf6bfa71a718c85
