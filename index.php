<?php
// O array de palavras possíveis[cite: 1]
$palavras = ["php", "html", "css", "mysql", "java", "python", "linux"];

// Recupera o estado do jogo via POST
$palavraSorteada = $_POST['palavra_sorteada'] ?? '';
$letrasTentadas = $_POST['letras_tentadas'] ?? '';
$erros = (int)($_POST['erros'] ?? 0);
$mensagem = "";

// Se não tem palavra sorteada, é uma nova partida
if ($palavraSorteada === '') {
    $indice = array_rand($palavras); // Sorteia a palavra[cite: 1]
    $palavraSorteada = $palavras[$indice];
}

// Processa a nova letra digitada
$letra = $_POST['letra'] ?? '';
$letra = strtolower(trim(substr($letra, 0, 1))); // Garante que é apenas 1 letra e minúscula[cite: 1]

$fimDeJogo = false;

// Lógica de acertos e erros
if ($letra !== "" && $erros < 6) {
    if (strpos($letrasTentadas, $letra) === false) {
        $letrasTentadas .= $letra; // Adiciona ao histórico de tentativas
        
        if (strpos($palavraSorteada, $letra) === false) { // Verifica se a letra existe na palavra[cite: 1]
            $erros++;
            $mensagem = "Errou! A letra não existe na palavra.";[cite: 1]
        } else {
            $mensagem = "Acertou! A letra existe na palavra.";[cite: 1]
        }
    } else {
        $mensagem = "Atenção: Você já tentou a letra '" . strtoupper($letra) . "'.";
    }
}

// Monta a palavra para exibição
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

// Prepara o histórico de letras para exibição visual
$historicoVisual = '';
if ($letrasTentadas !== '') {
    // Transforma a string "abc" em um array ['A', 'B', 'C'] e une com " - "
    $arrayLetras = str_split(strtoupper($letrasTentadas));
    $historicoVisual = implode(' - ', $arrayLetras);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">[cite: 1]
    <title>Forca PHP v1.2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">[cite: 1]
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="h3 mb-3">Jogo da Forca v1.2.0</h1>
                        
                        <!-- Lógica das Imagens Dinâmicas -->
                        <div class="mb-4">
                            <img src="img/forca_<?= $erros ?>.png" alt="Forca com <?= $erros ?> erros" class="img-fluid" style="height: 250px; object-fit: contain;">
                        </div>
                        
                        <h3 class="text-danger mb-3">Erros: <?= $erros ?> / 6</h3>
                        
                        <p class="fs-1 fw-bold font-monospace tracking-widest mb-3">
                            <?= htmlspecialchars($palavraExibicao) ?>
                        </p>

                        <!-- NOVO: Exibição do Histórico de Letras -->
                        <?php if ($historicoVisual !== ''): ?>
                            <div class="mb-4 p-2 bg-white border rounded">
                                <span class="text-muted small text-uppercase d-block mb-1">Letras já tentadas:</span>
                                <strong class="fs-5 tracking-wide"><?= htmlspecialchars($historicoVisual) ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if ($mensagem !== ""): 
                            $corAlerta = 'alert-info';
                            if (strpos($mensagem, 'Acertou') !== false || strpos($mensagem, 'Parabéns') !== false) {
                                $corAlerta = 'alert-success';
                            } elseif (strpos($mensagem, 'Errou') !== false || strpos($mensagem, 'Fim de jogo') !== false) {
                                $corAlerta = 'alert-danger';
                            } elseif (strpos($mensagem, 'Atenção') !== false) {
                                $corAlerta = 'alert-warning';
                            }
                        ?>
                            <div class="alert <?= $corAlerta ?>">
                                <?= htmlspecialchars($mensagem) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!$fimDeJogo): ?>
                            <!-- O formulário envia a letra digitada para a própria página[cite: 1] -->
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