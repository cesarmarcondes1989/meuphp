<?php
// Caminho para a imagem base
$imagemBase = 'resposta.png';
$image = imagecreatefrompng($imagemBase);

$msgusuario = $_GET['msg_usuario'] ?? '';
$msgcorrigida = $_GET['msg_corrigida'] ?? '';
$sugestao = $_GET['sugestao'] ?? '';
$score = $_GET['score'] ?? '';

// Define cor e fonte
$corTexto = imagecolorallocate($image, 101, 67, 33);
$fonte = __DIR__ . '/fonte_usar.ttf';
if (!file_exists($fonte)) die("❌ Fonte não encontrada em: $fonte");

// Tamanho e espaçamento
$tamanhoFonte = 12;
$lineHeight = 28;
$x = 60;
$y = 500;

// Função auxiliar para escrever bloco com título + conteúdo
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

// Salvar imagem
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

