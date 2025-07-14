<?php
// Caminho para a imagem base
$imagemBase = 'resposta.png';
$image = imagecreatefrompng($imagemBase);

// Inputs via GET
$msgusuario   = $_GET['msg_usuario'] ?? '';
$msgcorrigida = $_GET['msg_corrigida'] ?? '';
$sugestao     = $_GET['sugestao'] ?? '';
$score        = $_GET['score'] ?? '';

$tamanhoFonte = isset($_GET['size']) ? intval($_GET['size']) : 50;
$x            = isset($_GET['x']) ? intval($_GET['x']) : 210;
$y            = isset($_GET['y']) ? intval($_GET['y']) : 700;
$y2           = isset($_GET['y2']) ? intval($_GET['y2']) : 1750;
$x3           = isset($_GET['x3']) ? intval($_GET['x3']) : 1170;
$y3           = isset($_GET['y3']) ? intval($_GET['y3']) : 515;

$lineHeight   = $tamanhoFonte + 14;

// Define cores e fonte
$corTexto = imagecolorallocate($image, 101, 67, 33);         // marrom escuro
$corErro  = imagecolorallocate($image, 200, 30, 30);         // vermelho
$fonte    = __DIR__ . '/roboto.ttf';                     // Fonte recomendada
if (!file_exists($fonte)) die("❌ Fonte não encontrada em: $fonte");

// Função para quebra de texto e renderização com estilos
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

// Função para aplicar estilos na linha: *negrito* e ~riscado~
function desenhaLinhaComEstilo($linha, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto) {
    $corErro = imagecolorallocate($image, 200, 30, 30);
    $parts = preg_split('/(\*[^*]+\*|~[^~]+~)/', $linha, -1, PREG_SPLIT_DELIM_CAPTURE);
    $offsetX = $x;

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

        $bbox = imagettfbbox($tamanhoFonte, 0, $fonte, $texto);
        $larguraTexto = abs($bbox[2] - $bbox[0]);

        if ($estilo === 'bold') {
            imagettftext($image, $tamanhoFonte, 0, $offsetX, $y, $corTexto, $fonte, $texto);
            imagettftext($image, $tamanhoFonte, 0, $offsetX + 1, $y, $corTexto, $fonte, $texto);
        } elseif ($estilo === 'error') {
            imagettftext($image, $tamanhoFonte, 0, $offsetX, $y, $corErro, $fonte, $texto);
            $linhaY = (int) ($y - ($tamanhoFonte * 0.35));
            imageline($image, $offsetX, $linhaY, $offsetX + $larguraTexto, $linhaY, $corErro);
        } else {
            imagettftext($image, $tamanhoFonte, 0, $offsetX, $y, $corTexto, $fonte, $texto);
        }

        $offsetX += $larguraTexto + 8;
    }
}

// Escrever os textos
escreveTextoFormatadoComEstilo($msgusuario,   $x,  $y,  $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight, 1100);
escreveTextoFormatadoComEstilo($msgcorrigida, $x,  $y2, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight, 1100);
escreveTextoFormatadoComEstilo($score,        $x3, $y3, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight, 1100);

// Gerar imagem
ob_start();
imagepng($image);
$imagemFinal = ob_get_clean();
imagedestroy($image);

// Enviar imagem para download
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="resposta_usuario.png"');
header('Content-Length: ' . strlen($imagemFinal));
echo $imagemFinal;
exit;
?>
