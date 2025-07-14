<?php
// Caminho para a imagem base
$imagemBase = 'resposta.png';
$image = imagecreatefrompng($imagemBase);
$msgusuario = $_GET['msg_usuario'];
$msgcorrigida = $_GET['msg_corrigida'];
$score = $_GET['score'];

// Define cor e fonte
$corTexto = imagecolorallocate($image, 101, 67, 33);
$fonte = __DIR__ . '/fonte_usar.ttf'; // Certifique-se que arial.ttf está no mesmo diretório
if (!file_exists($fonte)) die("❌ Fonte não encontrada em: $fonte");


// Texto personalizado
$texto = "$msgusuario";

// Posição inicial
$x = 250;
$y = 550;
$lineHeight = 30;

// Escreve o texto linha por linha
foreach (explode("\n", $texto) as $linha) {
    imagettftext($image, 12, 0, $x, $y, $corTexto, $fonte, $linha);
    $y += $lineHeight;
}

// Salva imagem temporária em memória
ob_start();
imagepng($image);
$imagemFinal = ob_get_clean();
imagedestroy($image);

// Retorna para download
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="resposta_usuario.png"');
header('Content-Length: ' . strlen($imagemFinal));
echo $imagemFinal;
exit;
?>
