<?php
date_default_timezone_set("America/Sao_Paulo");
$hora = date("H:i:s");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Teste PHP</title>
</head>
<body style="font-family: Arial; text-align: center; padding: 50px;">
  <h1>✅ Olá, PHP está funcionando!</h1>
  <p>Agora são <strong><?php echo $hora; ?></strong></p>
  <p>Essa página foi gerada em tempo real usando PHP 🎉</p>
</body>
</html>
