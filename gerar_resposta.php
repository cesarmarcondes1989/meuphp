<?php
$imagemBase = 'resposta_nova.png';
$image = imagecreatefrompng($imagemBase);

// Inputs
$blocos = [
    ['titulo' => '', 'texto' => $_GET['msg_apoio'] ?? ''],
    ['titulo' => 'Your Message', 'texto' => $_GET['msg_usuario'] ?? ''],
    ['titulo' => 'Recommendation',  'texto' => $_GET['msg_corrigida'] ?? ''],
    ['titulo' => 'Good Practices',     'texto' => $_GET['msg_sugestao'] ?? ''],
    ['titulo' => '', 'texto' => $_GET['msg_final'] ?? '']
];
$score = $_GET['score'] ?? '';
$tamanhoFonte = $_GET['size'] ?? 50;
$tamanhoFonte_s = $_GET['size_s'] ?? 110;
$x_s = $_GET['x_s'] ?? 980;
$y_s = $_GET['y_s'] ?? 343;
$x = $_GET['x'] ?? 180;
$y = $_GET['y'] ?? 620;
$lineHeight = $tamanhoFonte + 14;

$fonte = __DIR__ . '/roboto.ttf';
if (!file_exists($fonte)) die("❌ Fonte não encontrada!");

$corTexto = imagecolorallocate($image, 101, 67, 33); // marrom
$corScore = imagecolorallocate($image, 112, 48, 160); // marrom
$corErro  = imagecolorallocate($image, 200, 30, 30); // vermelho

function escreveTextoFormatado($titulo, $texto, &$x, &$y, $image, $fonte, $tamanhoFonte, $corTexto, $corErro, $lineHeight, $maxWidth = 1100) {
    // Título
    imagettftext($image, $tamanhoFonte + 10, 0, $x, $y, $corTexto, $fonte, $titulo);
    $y += $lineHeight + 12;

    // Limpar barra final
    $texto = preg_replace('/\\\\$/', '', $texto);
    $texto = preg_replace('/\\\\\./', '.', $texto);

    $palavras = explode(' ', $texto);
    $linha = '';

    foreach ($palavras as $palavra) {
        $teste = trim($linha . ' ' . $palavra);
        $bbox = imagettfbbox($tamanhoFonte, 0, $fonte, $teste);
        $largura = abs($bbox[2] - $bbox[0]);

        if ($largura > $maxWidth) {
            desenhaLinhaEstilo($linha, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto, $corErro);
            $y += $lineHeight;
            $linha = $palavra;
        } else {
            $linha .= ' ' . $palavra;
        }
    }
    if (!empty($linha)) {
        desenhaLinhaEstilo($linha, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto, $corErro);
        $y += $lineHeight;
    }

    $y += 75; // espaço entre blocos
}

function desenhaLinhaEstilo($linha, $x, $y, $image, $fonte, $tamanhoFonte, $corTexto, $corErro) {
    $parts = preg_split('/(\*[^*]+\*|~[^~]+~)/', $linha, -1, PREG_SPLIT_DELIM_CAPTURE);
    $offsetX = $x;

    foreach ($parts as $parte) {
        $estilo = 'normal';
        $corAtual = $corTexto;

        if (preg_match('/^\*(.*?)\*$/', $parte, $m)) {
            $texto = $m[1];
            $estilo = 'bold';
        } elseif (preg_match('/^~(.*?)~$/', $parte, $m)) {
            $texto = $m[1];
            $estilo = 'strike';
            $corAtual = $corErro;
        } else {
            $texto = $parte;
        }

        $bbox = imagettfbbox($tamanhoFonte, 0, $fonte, $texto);
        $larguraTexto = abs($bbox[2] - $bbox[0]);

        // Estilos
        if ($estilo === 'bold') {
            imagettftext($image, $tamanhoFonte, 0, $offsetX, $y, $corAtual, $fonte, $texto);
            imagettftext($image, $tamanhoFonte, 0, $offsetX + 1, $y, $corAtual, $fonte, $texto);
        } elseif ($estilo === 'strike') {
            imagettftext($image, $tamanhoFonte, 0, $offsetX, $y, $corAtual, $fonte, $texto);
            $linhaY = (int) ($y - ($tamanhoFonte * 0.35));
            imageline($image, $offsetX, $linhaY, $offsetX + $larguraTexto, $linhaY, $corErro);
        } else {
            imagettftext($image, $tamanhoFonte, 0, $offsetX, $y, $corAtual, $fonte, $texto);
        }

        $offsetX += $larguraTexto + 8;
    }
}

// Escrever blocos
foreach ($blocos as $bloco) {
    escreveTextoFormatado($bloco['titulo'], $bloco['texto'], $x, $y, $image, $fonte, $tamanhoFonte, $corTexto, $corErro, $lineHeight);
}
escreveTextoFormatado('', $score, $x_s, $y_s, $image, $fonte, $tamanhoFonte_s, $corScore, $corErro, $lineHeight);

// Saída
ob_start();
imagepng($image);
$imagemFinal = ob_get_clean();
imagedestroy($image);

header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="resposta_usuario.png"');
header('Content-Length: ' . strlen($imagemFinal));
echo $imagemFinal;
exit;
?>
