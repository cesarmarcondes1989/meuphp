<?php
// Caminho para a imagem base
$imagemBase = 'resposta.png';
$image = imagecreatefrompng($imagemBase);

// Inputs via GET
$msgusuario = $_GET['msg_usuario'] ?? '';
$msgcorrigida = $_GET['msg_corrigida'] ?? '';
$sugestao = $_GET['sugestao'] ?? '';
$score = $_GET['score'] ?? '';

$tamanhoFonte = isset($_GET['size']) ? intval($_GET['size']) : 50;
$x = isset($_GET['x']) ? intval($_GET['x']) : 210;
$y = isset($_GET['y']) ? intval($_GET['y']) : 700;
$y2 = isset($_GET['y2']) ? intval($_GET['y2']) : 1750;
$x3 = isset($_GET['x3']) ? intval($_GET['x3']) : 1170;
$y3 = isset($_GET['y3']) ? intval($_GET['y3']) : 515;
$y4 = isset($_GET['y4']) ? intval($_GET['y4']) : 515;

$lineHeight = $tamanhoFonte + 14; // altura entre linhas adaptativa

// Define cor e fonte
$corTexto = imagecolorallocate($image, 101, 67, 33);
$fonte = __DIR__ . '/ARIALN.TTF';
if (!file_exists($fonte)) die("❌ Fonte não encontrada em: $fonte");

// Função segura para escrever blocos de texto
function escreveBloco($titulo, $conteudo, &$x, &$y, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight) {
    $y = intval($y); // Garante que $y seja sempre inteiro
    if (!empty($titulo)) {
        imagettftext($image, $tamanhoFonte + 1, 0, intval($x), $y, $corTexto, $fonte, $titulo);
        $y += $lineHeight;
    }

    foreach (explode("\n", wordwrap($conteudo, 80, "\n")) as $linha) {
        imagettftext($image, $tamanhoFonte, 0, intval($x + 10), $y, $corTexto, $fonte, $linha);
        $y += $lineHeight;
    }
    $y += 10;
}

// Escrever blocos na imagem
escreveBloco("", $msgusuario, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight);
escreveBloco("", $msgcorrigida, $x, $y2, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight);
escreveBloco("", $score, $x3, $y3, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight);

// Mostrar imagem na tela
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
exit;
?>

