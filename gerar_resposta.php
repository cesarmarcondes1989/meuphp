<?php
// Caminho para a imagem base
$imagemBase = 'resposta.png';
$image = imagecreatefrompng($imagemBase);

// Inputs via GET
$msgusuario = $_GET['msg_usuario'] ?? '';
$msgcorrigida = $_GET['msg_corrigida'] ?? '';
$sugestao = $_GET['sugestao'] ?? '';
$score = $_GET['score'] ?? '';

$tamanhoFonte = isset($_GET['size']) ? intval($_GET['size']) : 12;
$x = isset($_GET['x']) ? intval($_GET['x']) : 110;
$y = isset($_GET['y']) ? intval($_GET['y']) : 500;

$lineHeight = $tamanhoFonte + 14; // altura entre linhas adaptativa

// Define cor e fonte
$corTexto = imagecolorallocate($image, 101, 67, 33);
$fonte = __DIR__ . '/ARIALN.TTF';
if (!file_exists($fonte)) die("❌ Fonte não encontrada em: $fonte");

// Função auxiliar para escrever texto com quebra de linha automática
function escreveBloco($titulo, $conteudo, &$x, &$y, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight) {
    imagettftext($image, $tamanhoFonte + 1, 0, $x, $y, $corTexto, $fonte, $titulo);
    $y += $lineHeight;
    foreach (explode("\n", wordwrap($conteudo, 80, "\n")) as $linha) {
        imagettftext($image, $tamanhoFonte, 0, $x + 10, $y, $corTexto, $fonte, $linha);
        $y += $lineHeight;
    }
    $y += 10;
}

// Escrever blocos na imagem
escreveBloco("🗣 Mensagem do Usuário:", $msgusuario, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight);
escreveBloco("✅ Correção:", $msgcorrigida, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight);
escreveBloco("💡 Sugestão:", $sugestao, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight);

// Mostrar imagem na tela
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
exit;
?>

