<?php
// Caminho para a imagem base
$imagemBase = 'ranking.png';
$image = imagecreatefrompng($imagemBase);
$posicao = round($_GET['posicao']);
$pontuacao = $_GET['pontuacao'];


// Define cor e fonte
$corTexto = imagecolorallocate($image, 101, 67, 33);
$fonte = __DIR__ . '/fonte_usar.ttf'; // Certifique-se que arial.ttf está no mesmo diretório
if (!file_exists($fonte)) die("❌ Fonte não encontrada em: $fonte");


// Texto personalizado
$texto = "Posição: $posicao\n\n\n\nPontuação: $pontuacao";

// Posição inicial
$x = 250;
$y = 550;
$lineHeight = 30;

// Escreve o texto linha por linha
foreach (explode("\n", $texto) as $linha) {
    imagettftext($image, 50, 0, $x, $y, $corTexto, $fonte, $linha);
    //imagestring($image, 5, $x, $y, "Posição: ", $corTexto);
    //imagestring($image, 5, $x, $y + 20, "Pontuação: ", $corTexto);
    $y += $lineHeight;
}

// Salva imagem temporária em memória
ob_start();
imagepng($image);
$imagemFinal = ob_get_clean();
imagedestroy($image);

// Retorna para download
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="ranking.png"');
header('Content-Length: ' . strlen($imagemFinal));
echo $imagemFinal;
exit;
?>
