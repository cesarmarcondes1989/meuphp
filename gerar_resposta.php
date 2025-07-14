<?php
// Caminho para a imagem base
$imagemBase = 'resposta.png';
$image = imagecreatefrompng($imagemBase);

function limpar($texto) {
    return rtrim(stripslashes(trim($texto)), "\\/");
}

$msgusuario   = limpar($_GET['msg_usuario']   ?? '');
$msgcorrigida = limpar($_GET['msg_corrigida'] ?? '');
$sugestao     = limpar($_GET['sugestao']      ?? '');
$score        = limpar($_GET['score']         ?? '');


$tamanhoFonte = isset($_GET['size']) ? intval($_GET['size']) : 50;
$x            = isset($_GET['x']) ? intval($_GET['x']) : 210;
$y            = isset($_GET['y']) ? intval($_GET['y']) : 700;
$y2           = isset($_GET['y2']) ? intval($_GET['y2']) : 1750;
$x3           = isset($_GET['x3']) ? intval($_GET['x3']) : 1170;
$y3           = isset($_GET['y3']) ? intval($_GET['y3']) : 515;

$lineHeight   = $tamanhoFonte + 14;

// Define cores e fonte
$corTexto = imagecolorallocate($image, 101, 67, 33);        // marrom escuro
$corErro  = imagecolorallocate($image, 200, 30, 30);        // vermelho para erro
$fonte    = __DIR__ . '/roboto.ttf';
if (!file_exists($fonte)) die("❌ Fonte não encontrada em: $fonte");

// Função principal para escrever texto com estilo
function escreveTextoFormatadoComEstilo($conteudo, $x, &$y, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight, $maxWidth = 1200) {
    // Divide por padrões e espaços
    $parts = preg_split('/(\*[^*]+\*|~[^~]+~| )/', $conteudo, -1, PREG_SPLIT_DELIM_CAPTURE);
    $linha = '';
    $linhaMedida = 0;

    foreach ($parts as $parte) {
        $teste = $linha . $parte;
        $bbox = imagettfbbox($tamanhoFonte, 0, $fonte, $teste);
        $largura = abs($bbox[2] - $bbox[0]);

        if ($largura > $maxWidth && $linha !== '') {
            desenhaLinhaComEstilo($linha, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto);
            $y += $lineHeight;
            $linha = trim($parte);
        } else {
            $linha .= $parte;
        }
    }

    if (!empty($linha)) {
        desenhaLinhaComEstilo($linha, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto);
        $y += $lineHeight;
    }
}

// Aplica estilo individual em cada parte (negrito, risco)
function desenhaLinhaComEstilo($linha, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto) {
    $corErro = imagecolorallocate($image, 200, 30, 30); // vermelho
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

// Escreve os três blocos
escreveTextoFormatadoComEstilo($msgusuario,   $x,  $y,  $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight, 1200);
escreveTextoFormatadoComEstilo($msgcorrigida, $x,  $y2, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight, 1200);
escreveTextoFormatadoComEstilo($score,        $x3, $y3, $image, $fonte, $tamanhoFonte, $corTexto, $lineHeight, 1200);

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
