<?php
// Carrega a imagem base
$imagemBase = 'resposta.png';
$image = imagecreatefrompng($imagemBase);

// Dados recebidos por GET
$msgusuario   = $_GET['msg_usuario'] ?? '';
$msgcorrigida = $_GET['msg_corrigida'] ?? '';
$sugestao     = $_GET['sugestao'] ?? '';
$score        = $_GET['score'] ?? '';

// Parâmetros visuais
$tamanhoFonteTexto  = 44;
$tamanhoFonteTitulo = 64;
$xInicial           = 210;
$y                  = 300;
$lineHeightTexto    = $tamanhoFonteTexto + 16;
$lineHeightTitulo   = $tamanhoFonteTitulo + 22;
$maxWidth           = 1100;

// Cores
$corTexto   = imagecolorallocate($image, 101, 67, 33);
$corTitulo  = imagecolorallocate($image, 0, 0, 0);
$corErro    = imagecolorallocate($image, 200, 30, 30);

// Fonte
$fonte = __DIR__ . '/roboto.ttf';
if (!file_exists($fonte)) die("❌ Fonte não encontrada em: $fonte");

// --- Funções ---
function escreveTitulo($texto, &$y, $image, $fonte, $tamanho, $cor, $x) {
    imagettftext($image, $tamanho, 0, $x, $y, $cor, $fonte, $texto);
    $y += $tamanho + 20;
}

function escreveTextoFormatado($conteudo, $x, &$y, $image, $fonte, $tamanhoFonte, $corTexto, $corErro, $lineHeight, $maxWidth = 1000) {
    $palavras = explode(' ', $conteudo);
    $linha = '';

    foreach ($palavras as $palavra) {
        $teste = trim($linha . ' ' . $palavra);
        $bbox = imagettfbbox($tamanhoFonte, 0, $fonte, $teste);
        $largura = abs($bbox[2] - $bbox[0]);

        if ($largura > $maxWidth) {
            desenhaLinhaFormatada(trim($linha), $x, $y, $image, $fonte, $tamanhoFonte, $corTexto, $corErro);
            $y += $lineHeight;
            $linha = $palavra;
        } else {
            $linha .= ' ' . $palavra;
        }
    }

    if (!empty($linha)) {
        desenhaLinhaFormatada(trim($linha), $x, $y, $image, $fonte, $tamanhoFonte, $corTexto, $corErro);
        $y += $lineHeight;
    }
}

function desenhaLinhaFormatada($linha, $x, $y, $image, $fonte, $tamanho, $corTexto, $corErro) {
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

        $bbox = imagettfbbox($tamanho, 0, $fonte, $texto);
        $larguraTexto = abs($bbox[2] - $bbox[0]);

        if ($estilo === 'bold') {
            imagettftext($image, $tamanho, 0, $offsetX, $y, $corTexto, $fonte, $texto);
            imagettftext($image, $tamanho, 0, $offsetX + 1, $y, $corTexto, $fonte, $texto);
        } elseif ($estilo === 'error') {
            imagettftext($image, $tamanho, 0, $offsetX, $y, $corErro, $fonte, $texto);
            $linhaY = (int) ($y - ($tamanho * 0.35));
            imageline($image, $offsetX, $linhaY, $offsetX + $larguraTexto, $linhaY, $corErro);
        } else {
            imagettftext($image, $tamanho, 0, $offsetX, $y, $corTexto, $fonte, $texto);
        }

        $offsetX += $larguraTexto + 8;
    }
}

// --- CONTEÚDO DINÂMICO ---

// Bloco 1 - Pronúncia
escreveTitulo("Your message", $y, $image, $fonte, $tamanhoFonteTitulo, $corTitulo, $xInicial);
escreveTextoFormatado($msgusuario, $xInicial, $y, $image, $fonte, $tamanhoFonteTexto, $corTexto, $corErro, $lineHeightTexto, $maxWidth);

// Bloco 2 - Sugestão
escreveTitulo("Attention", $y, $image, $fonte, $tamanhoFonteTitulo, $corTitulo, $xInicial);
escreveTextoFormatado($msgcorrigida, $xInicial, $y, $image, $fonte, $tamanhoFonteTexto, $corTexto, $corErro, $lineHeightTexto, $maxWidth);

// Bloco 3 - Pontuação
escreveTitulo("Suggestion", $y, $image, $fonte, $tamanhoFonteTitulo, $corTitulo, $xInicial);
escreveTextoFormatado($msgcorrigida, $xInicial, $y, $image, $fonte, $tamanhoFonteTexto, $corTexto, $corErro, $lineHeightTexto, $maxWidth);

// --- Saída final ---
ob_start();
imagepng($image);
$imagemFinal = ob_get_clean();
imagedestroy($image);

header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="feedback_usuario.png"');
header('Content-Length: ' . strlen($imagemFinal));
echo $imagemFinal;
exit;

