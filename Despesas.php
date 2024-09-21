<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despesas</title>
    <link rel="stylesheet" href="Assets/CSS/php.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nova+Round&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nova+Round&family=Oleo+Script:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>  
    <a class="seta" href="Home.html"><img src="Img/icon-SetaParaEsquerda.png" alt="SetaParaEsquerda"></a>
    <h2>Despesas</h2>
    <form action="" method="post">
        <div class="box">
            <div class="box2">
                <h3>Valor</h3>
                <label class="RS" for="Valor">R$</label>
                <input class="Valor" type="text" oninput="this.value = this.value.replace(',', '.');" name="Valor" placeholder="xxxx,xx" required>
            </div>
            <div class="select">
                <h3>Descrição da despesa</h3>
                <textarea class="text-despesas" name="descricao" placeholder="Digite suas despesas aqui..." required></textarea>
            </div>
        </div>
        <div class="submit">
            <button type="submit" name="Despesas" class="Enviar">Enviar</button>
        </div>
    </form>

    <?php
    include_once('banco.php');

    if (isset($_POST['Despesas'])) {
        $valor = $_POST['Valor'];
        $descricao = $_POST['descricao'];

        // Convertendo o valor para número
        $valor = str_replace(',', '.', $valor);
        $valor = floatval($valor);
        if ($valor == 0){
            echo "<div class='alert'> <img class='alert-img' src='Img/icon-erro.png' alt='icon-accept'> <h3 class='confirm'>Erro! Valor incorreto.</h3> </div>";
        }else{
            // Escapar as variáveis para prevenir injeção de SQL
        $descricao = mysqli_real_escape_string($conexao, $descricao);

        // Preparar a query com placeholders
        if (is_numeric($valor) && $valor <= 999999.99) {
            // Se estiver dentro do intervalo, continuar com a inserção
            $stmt = $conexao->prepare("INSERT INTO despesas (desp_valor, desp_descricao) VALUES (?, ?)");
            $stmt->bind_param("ds", $valor, $descricao);

            if ($stmt->execute()) {
                echo "<div class='alert'> <img class='alert-img' src='Img/icon-accept.png' alt='icon-accept'> <h3 class='confirm'>Despesa inserida com sucesso!</h3> </div>";
            } else {
                echo "<div class='alert'> <img class='alert-img' src='Img/icon-erro.png' alt='icon-accept'> <h3 class='confirm'>Erro: " . $stmt->error . "</h3> </div>";
            }

            $stmt->close();
        } else {
            echo "<div class='alert'> <img class='alert-img' src='Img/icon-erro.png' alt='icon-accept'> <h3 class='confirm'>Erro! Valor fora do intervalo permitido.</h3> </div>";
        }
        }
    }
    ?>

</body>

</html>