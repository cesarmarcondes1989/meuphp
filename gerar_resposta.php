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

$lineHeight = $tamanhoFonte + 14; // altura entre linhas adaptativa

// Define cor e fonte
$corTexto = imagecolorallocate($image, 101, 67, 33);
$fonte = __DIR__ . '/ARIALN.TTF';
if (!file_exists($fonte)) die("❌ Fonte não encontrada em: $fonte");

// Função para quebrar texto em múltiplas linhas com base em largura máxima e aplicar estilos
function escreveTextoFormatadoComEstilo($conteudo, $x, &$y, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight, $maxWidth = 1000) {
    $palavras = explode(' ', $conteudo);
    $linha = '';

    foreach ($palavras as $palavra) {
        $teste = trim($linha . ' ' . $palavra);
        $bbox = imagettfbbox($tamanhoFonte, 0, $fonte, $teste);
        $largura = abs($bbox[2] - $bbox[0]);

        if ($largura > $maxWidth) {
            desenhaLinhaComEstilo(trim($linha), $x, $y, $image, $fonte, $tamanhoFonte, $corTexto);
            $y += $lineHeight;
            $linha = $palavra;
        } else {
            $linha .= ' ' . $palavra;
        }
    }

    if (!empty($linha)) {
        desenhaLinhaComEstilo(trim($linha), $x, $y, $image, $fonte, $tamanhoFonte, $corTexto);
        $y += $lineHeight;
    }
}

// Função para desenhar uma linha com estilos: *negrito* e ~riscado~
function desenhaLinhaComEstilo($linha, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto) {
    $parts = preg_split('/(\*[^*]+\*|~[^~]+~)/', $linha, -1, PREG_SPLIT_DELIM_CAPTURE);
    $offsetX = $x;

    // Define cores adicionais
    $corErro = imagecolorallocate($image, 200, 30, 30); // vermelho para erros
    $corNegrito = $corTexto; // usa a mesma cor pro bold

    foreach ($parts as $parte) {
        $estilo = 'normal';

        if (preg_match('/^\*(.*?)\*$/', $parte, $m)) {
            $texto = $m[1];
            $estilo = 'bold';
        } elseif (preg_match('/^~(.*?)~$/', $parte, $m)) {
            $texto = $m[1];
            $estilo = 'error';
        } else {
            $texto = $parte;
        }

        // Medir tamanho da palavra
        $bbox = imagettfbbox($tamanhoFonte, 0, $fonte, $texto);
        $larguraTexto = abs($bbox[2] - $bbox[0]);

        // Desenhar com estilo
        if ($estilo === 'bold') {
            imagettftext($image, $tamanhoFonte, 0, $offsetX, $y, $corNegrito, $fonte, $texto);
            imagettftext($image, $tamanhoFonte, 0, $offsetX + 1, $y, $corNegrito, $fonte, $texto); // falso negrito
        } elseif ($estilo === 'error') {
            imagettftext($image, $tamanhoFonte, 0, $offsetX, $y, $corErro, $fonte, $texto); // palavra errada em vermelho
        } else {
            imagettftext($image, $tamanhoFonte, 0, $offsetX, $y, $corTexto, $fonte, $texto);
        }

        $offsetX += $larguraTexto + 8;
    }
}


// Escrever os três blocos de conteúdo
escreveTextoFormatadoComEstilo($msgusuario, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight, 1100);
escreveTextoFormatadoComEstilo($msgcorrigida, $x, $y2, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight, 1100);
escreveTextoFormatadoComEstilo($score, $x3, $y3, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight, 1100);

// Gerar imagem final em memória
ob_start();
imagepng($image);
$imagemFinal = ob_get_clean();
imagedestroy($image);

// Mostrar imagem na tela
// Mostrar imagem na tela
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="resposta_usuario.png"');
header('Content-Length: ' . strlen($imagemFinal));
echo $imagemFinal;
exit;
?>

