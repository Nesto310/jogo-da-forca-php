<?php
// O array de palavras possíveis
$palavras = ["php", "html", "css", "mysql", "java", "python", "linux"];

// Recupera o estado do jogo via POST
$palavraSorteada = $_POST['palavra_sorteada'] ?? '';
$letrasTentadas = $_POST['letras_tentadas'] ?? '';
$erros = (int)($_POST['erros'] ?? 0);
$mensagem = "";

// Se não tem palavra sorteada, é uma nova partida
if ($palavraSorteada === '') {
    $indice = array_rand($palavras);
    $palavraSorteada = $palavras[$indice];
}

// Processa a nova letra digitada
$letra = $_POST['letra'] ?? '';
$letra = strtolower(trim(substr($letra, 0, 1)));

$fimDeJogo = false;

// Lógica de acertos e erros
if ($letra !== "" && $erros < 6) {
    if (strpos($letrasTentadas, $letra) === false) {
        $letrasTentadas .= $letra; // Adiciona ao histórico de tentativas
        
        if (strpos($palavraSorteada, $letra) === false) {
            $erros++;
            $mensagem = "Errou! A letra não existe na palavra.";
        } else {
            $mensagem = "Acertou! A letra existe na palavra.";
        }
    } else {
        $mensagem = "Você já tentou essa letra.";
    }
}

// Monta a palavra para exibição (escondendo as letras não tentadas)
$palavraExibicao = '';
$acertos = 0;
for ($i = 0; $i < strlen($palavraSorteada); $i++) {
    $letraAtual = substr($palavraSorteada, $i, 1);
    
    if (strpos($letrasTentadas, $letraAtual) !== false) {
        $palavraExibicao .= $letraAtual . " ";
        $acertos++;
    } else {
        $palavraExibicao .= "_ ";
    }
}

// Verifica condição de vitória ou derrota
if ($erros >= 6) {
    $mensagem = "Fim de jogo! Você foi enforcado. A palavra era: " . strtoupper($palavraSorteada);
    $fimDeJogo = true;
} elseif ($acertos === strlen($palavraSorteada) && $palavraSorteada !== '') {
    $mensagem = "Parabéns! Você sobreviveu!";
    $fimDeJogo = true;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Forca PHP v1.1.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="h3 mb-3">Jogo da Forca v1.1.0</h1>
                        
                        <div class="mb-4">
                            <img src="img/forca_<?= $erros ?>.png" alt="Forca com <?= $erros ?> erros" class="img-fluid" style="height: 250px; object-fit: contain;">
                        </div>
                        
                        <h3 class="text-danger mb-3">Erros: <?= $erros ?> / 6</h3>
                        
                        <p class="fs-1 fw-bold font-monospace tracking-widest mb-4">
                            <?= htmlspecialchars($palavraExibicao) ?>
                        </p>

                        <?php if ($mensagem !== ""): 
                            // Define a cor do alerta baseado no conteúdo da mensagem
                            $corAlerta = 'alert-info';
                            if (strpos($mensagem, 'Acertou') !== false || strpos($mensagem, 'Parabéns') !== false) {
                                $corAlerta = 'alert-success';
                            } elseif (strpos($mensagem, 'Errou') !== false || strpos($mensagem, 'Fim de jogo') !== false) {
                                $corAlerta = 'alert-danger';
                            } elseif (strpos($mensagem, 'já tentou') !== false) {
                                $corAlerta = 'alert-warning';
                            }
                        ?>
                            <div class="alert <?= $corAlerta ?>">
                                <?= htmlspecialchars($mensagem) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!$fimDeJogo): ?>
                            <form method="post" class="d-flex justify-content-center gap-2">
                                <input type="hidden" name="palavra_sorteada" value="<?= htmlspecialchars($palavraSorteada) ?>">
                                <input type="hidden" name="letras_tentadas" value="<?= htmlspecialchars($letrasTentadas) ?>">
                                <input type="hidden" name="erros" value="<?= $erros ?>">
                                
                                <input type="text" name="letra" maxlength="1" class="form-control w-25 text-center fw-bold" required autofocus placeholder="Letra">
                                <button type="submit" class="btn btn-primary">Verificar</button>
                            </form>
                        <?php else: ?>
                            <a href="index.php" class="btn btn-success">Jogar Novamente</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>