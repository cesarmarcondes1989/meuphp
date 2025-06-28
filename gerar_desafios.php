<?php
// Sorteia um número aleatório entre 1 e 20
$numeroSorteado = rand(1, 22);

// Formata o número para dois dígitos (ex: 1 vira 01)
$numeroFormatado = str_pad($numeroSorteado, 2, "0", STR_PAD_LEFT);

// Monta o nome da imagem
$nomeImagem = $numeroFormatado . ".png";
$info = getimagesize($nomeImagem);
header("Content-Type: " . $info['mime']);

// Exibe a imagem



// Retorna para download
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="imagem.png"');
header('Content-Length: ' . strlen(readfile($nomeImagem)));
echo readfile($nomeImagem);
exit;
?>
