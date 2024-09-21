<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamentos</title>
    <link rel="stylesheet" href="Assets/CSS/php.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nova+Round&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nova+Round&family=Oleo+Script:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <a class="seta" href="Home.html"><img src="Img/icon-SetaParaEsquerda.png" alt="SetaParaEsquerda"></a>
    <h2 class="">Pagamentos</h2>
    <form action="" method="post">
        <div class="box">
            <div class="box2">
                <h3>Valor</h3>
                <label class="RS" for="Valor">R$</label>
                <input class="paga-box" type="text" oninput="this.value = this.value.replace(',', '.');" name="valor" placeholder="xxxx,xx" required>
                <h3>Nome</h3>
                <input class="paga-text" type="text" name="Nome" placeholder="Nome" required>
            </div>
            <div class="select">
                <h3>Método de pagamento</h3>
                <div class="radio-box" id="radio-box-pagamentos">
                    <div class="radio-column" id="radio-column-pagamentos">
                        <label><input type="radio" name="metodoPagamento" value="1" id="radio1" required>Débito</label>
                        <label><input type="radio" name="metodoPagamento" value="2" id="radio2">Crédito</label>
                        <label><input type="radio" name="metodoPagamento" value="3" id="radio3">Dinheiro</label>
                    </div>
                    <div class="radio-column" id="radio-column-pagamentos">
                        <label><input type="radio" name="metodoPagamento" value="4" id="radio4">Pix</label>
                        <label><input type="radio" name="metodoPagamento" value="5" id="radio5">Boleto</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="submit">
            <button type="submit" name="Pagamentos" class="Enviar">Enviar</button>
        </div>
    </form>
    <?php
    include_once('banco.php');
    if (isset($_POST['Pagamentos'])) {
        $valor = $_POST['valor'];
        $metodoPagamento = $_POST['metodoPagamento'];
        $Nome = $_POST['Nome'];

        // Convertendo o valor para número
        $valor = str_replace(',', '.', $valor);
        $valor = floatval($valor);
        if ($valor == 0) {
            echo "<div class='alert'> <img class='alert-img' src='Img/icon-erro.png' alt='icon-erro'> <h3 class='confirm'>Erro! Valor Incorreto.</h3> </div>";
        } else {
            // Escapar as variáveis para prevenir injeção de SQL
            $metodoPagamento = mysqli_real_escape_string($conexao, $metodoPagamento);
            $Nome = mysqli_real_escape_string($conexao, $Nome);

            // Preparar a query com placeholders
            $stmt = $conexao->prepare("INSERT INTO pagamentos (paga_valor, paga_nome, tipo_id) VALUES (?, ?, ?)");
            $stmt->bind_param("dsi", $valor, $Nome, $metodoPagamento); // Corrigido: "dsi" para double, string e integer

            // Executar a query
            if ((is_numeric($valor) && $valor <= 999999.99) && (is_string($Nome) && strlen($Nome) < 200)) {
                if ($stmt->execute()) {
                    echo "<div class='alert'> <img class='alert-img' src='Img/icon-accept.png' alt='icon-accept'> <h3 class='confirm'>Pagamento inserido com sucesso!</h3> </div>";
                } else {
                    echo "<div class='alert'> <img class='alert-img' src='Img/icon-erro.png' alt='icon-erro'> <h3 class='confirm'>Erro: " . $stmt->error . "</h3> </div>";
                }

                $stmt->close();
            } else {
                echo "<div class='alert'> <img class='alert-img' src='Img/icon-erro.png' alt='icon-erro'> <h3 class='confirm'>Erro! Valor fora do intervalo permitido.</h3> </div>";
            }
        }
    }
    ?>

</body>

</html>