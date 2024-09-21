<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas</title>
    <link rel="stylesheet" href="Assets/CSS/php.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nova+Round&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nova+Round&family=Oleo+Script:wght@400;700&display=swap" rel="stylesheet">
</head>
<script>
    function setDefaultMonth() {
        var today = new Date();
        var year = today.getFullYear();
        var month = ('0' + (today.getMonth() + 1)).slice(-2);
        document.getElementById('month-input').value = year + '-' + month;
    }
    document.addEventListener('DOMContentLoaded', setDefaultMonth);
</script>

<body>
    <a class="seta" href="Home.html"><img src="Img/icon-SetaParaEsquerda.png" alt="SetaParaEsquerda"></a>
    <h2>Consultar</h2>
    <form action="" method="post">
        <div class="box">
            <div class="box2">
                <h3>O que deseja consultar?</h3>
                <div class="radio-box" id="radio-box-consulta">
                    <div class="radio-column" id="radio-column-consulta">
                        <label><input class="dark" type="radio" name="consulta" value="1" id="radio1" required>Lucro</label>
                        <label><input class="dark" type="radio" name="consulta" value="2" id="radio2">Pagamentos</label>
                        <label><input class="dark" type="radio" name="consulta" value="3" id="radio3">Despesas</label>
                    </div>
                </div>
            </div>
            <div class="select" id="select-consulta">
                <h3>Mês</h3>
                <input class="Valor" type="month" id="month-input" name="txt-date1" required>
            </div>
        </div>
        <div class="submit">
            <button type="submit" name="Consultas" class="Enviar">Consultar</button>
        </div>
    </form>
    <?php
    include_once('banco.php');
    if (isset($_POST['Consultas'])) {
        $consulta = $_POST['consulta'];
        $dataDe = $_POST['txt-date1'];

        // Escapar as variáveis para prevenir injeção de SQL
        $consulta = mysqli_real_escape_string($conexao, $consulta);
        $dataDe = mysqli_real_escape_string($conexao, $dataDe) . "-01";

        if ($consulta == 1) {
            // Consulta simplificada para calcular débitos, créditos, dinheiro, pix, boleto e despesas em uma única query
            $result = mysqli_query($conexao, "
                SELECT 
                    CONCAT('Pagamentos: ',
                        'Débito: ', IFNULL(FORMAT(SUM(paga_valor_debito), 2, 'de_DE'), 'ND'),
                        ' - Crédito: ', IFNULL(FORMAT(SUM(paga_valor_credito), 2, 'de_DE'), 'ND'),
                        ' - Dinheiro: ', IFNULL(FORMAT(SUM(paga_valor_dinheiro), 2, 'de_DE'), 'ND'),
                        ' - Pix: ', IFNULL(FORMAT(SUM(paga_valor_pix), 2, 'de_DE'), 'ND'),
                        ' - Boleto: ', IFNULL(FORMAT(SUM(paga_valor_boleto), 2, 'de_DE'), 'ND')
                    ) AS pagamentos,
                    CONCAT('Total de Pagamentos: R$: ', FORMAT(SUM(total_pagamentos), 2, 'de_DE')) AS total_pagamentos,
                    CONCAT('Despesas: R$: ', FORMAT(IFNULL(SUM(d.despesas_total), 0), 2, 'de_DE')) AS despesas,
                    CONCAT('Lucro: R$: ', FORMAT(SUM(total_pagamentos) - IFNULL(SUM(d.despesas_total), 0), 2, 'de_DE')) AS lucro
                FROM (
                    SELECT
                        SUM(CASE WHEN t.tipo_nome = 'Débito' THEN p.paga_valor ELSE 0 END) AS paga_valor_debito,
                        SUM(CASE WHEN t.tipo_nome = 'Crédito' THEN p.paga_valor ELSE 0 END) AS paga_valor_credito,
                        SUM(CASE WHEN t.tipo_nome = 'Dinheiro' THEN p.paga_valor ELSE 0 END) AS paga_valor_dinheiro,
                        SUM(CASE WHEN t.tipo_nome = 'Pix' THEN p.paga_valor ELSE 0 END) AS paga_valor_pix,
                        SUM(CASE WHEN t.tipo_nome = 'Boleto' THEN p.paga_valor ELSE 0 END) AS paga_valor_boleto,
                        SUM(p.paga_valor) AS total_pagamentos
                    FROM pagamentos p
                    INNER JOIN tipo t ON p.tipo_id = t.tipo_id
                    WHERE DATE_FORMAT(p.paga_dateTime, '%Y-%m') = DATE_FORMAT('$dataDe', '%Y-%m')
                ) AS aggregated
                LEFT JOIN (
                    SELECT
                        DATE_FORMAT(d.desp_dateTime, '%Y-%m') AS desp_date,
                        SUM(d.desp_valor) AS despesas_total
                    FROM despesas d
                    WHERE DATE_FORMAT(d.desp_dateTime, '%Y-%m') = DATE_FORMAT('$dataDe', '%Y-%m')
                    GROUP BY DATE_FORMAT(d.desp_dateTime, '%Y-%m')
                ) d ON DATE_FORMAT('$dataDe', '%Y-%m') = d.desp_date
            ");





        // Exibindo os resultados
        if ($fetch = mysqli_fetch_row($result)) {
            echo "<div class='consulta-result-box'>";
                echo "<div class='consulta-result'>";
                    echo "<h3>" . $fetch[0] . "</h3>"; // Pagamentos detalhados por tipo (Débito, Crédito, etc.)
                    echo "<h3>" . $fetch[1] . "</h3>"; // Total de pagamentos
                    echo "<h3>" . $fetch[2] . "</h3>"; // Despesas totais
                    echo "<h3>" . $fetch[3] . "</h3>"; // Lucro
                echo "</div>";
            echo "</div>";
            
        }
        } elseif ($consulta == 2) {
            // Consulta para Pagamentos
            $query = "
                SELECT 
                CONCAT(paga_nome,' - R$: ', FORMAT(p.paga_valor, 2, 'de_DE'),' - ',t.tipo_nome,' - ', DATE_FORMAT(p.paga_dateTime, '%d/%m/%Y - %H:%i:%s')) AS valor_data_formatado,
                p.paga_id
                FROM pagamentos p
                INNER JOIN tipo t on p.tipo_id = t.tipo_id
                WHERE DATE_FORMAT(paga_dateTime, '%Y-%m') = DATE_FORMAT('$dataDe', '%Y-%m');
                ";
        } elseif ($consulta == 3) {
            // Consulta para Despesas
            $query = "
                SELECT 
                CONCAT('R$: ', FORMAT(desp_valor, 2, 'de_DE'), ' - ', DATE_FORMAT(desp_dateTime, '%d/%m/%Y - %H:%i:%s'),' - ', desp_descricao) AS valor_data_formatado,
                desp_id
                FROM despesas
                WHERE DATE_FORMAT(desp_dateTime, '%Y-%m') = DATE_FORMAT('$dataDe', '%Y-%m');
                ";
        }

        // Preparar a query, caso não seja consulta de Lucro
        if ($consulta != 1 && isset($query)) {
            $stmt = mysqli_prepare($conexao, $query);

            // Executar a query
            if ($stmt) {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='consulta' value='$consulta'>";
                echo "<div class='consulta-result-box'>";
                echo "<div class='consulta-result'>";
                while ($fetch = mysqli_fetch_row($result)) {
                    echo "<label><input type='checkbox' name='resultado[]' value='" . $fetch[1] . "' id='checkbox'>" . $fetch[0] . "</label><br>";
                }
                echo "</div>";
                echo "</div>";
                echo "<div class='submit'>
                    <button type='submit' name='Excluir' class='Excluir'>Excluir</button>
                </div>";
                echo "</form>";

                // Fechar a declaração
                mysqli_stmt_close($stmt);
            } else {
                echo "<div class='alert'> <img class='alert-img'src='Img/icon-erro.png' alt='icon-accept'> <h3 class='confirm'> Erro!" . mysqli_error($conexao) . "</h3> </div>";
            }
        }
    }

    if (isset($_POST['Excluir'])) {
        $consulta = $_POST['consulta'];

        // Verifica se há valores selecionados
        if (isset($_POST['resultado']) && !empty($_POST['resultado'])) {
            $excluir = $_POST['resultado'];

            foreach ($excluir as $id) {
                $id = mysqli_real_escape_string($conexao, $id);
                if ($consulta == 2) {
                    $deleteQuery = "DELETE FROM pagamentos WHERE paga_id = '$id'";
                } elseif ($consulta == 3) {
                    $deleteQuery = "DELETE FROM despesas WHERE desp_id = '$id'";
                }
                mysqli_query($conexao, $deleteQuery);
            }
            echo "<div class='alert'> <img class='alert-img'src='Img/icon-accept.png' alt='icon-accept'> <h3 class='confirm'> Registros excluídos com sucesso!</h3> </div>";
        } else {
            echo "<div class='alert'> <img class='alert-img'src='Img/icon-erro.png' alt='icon-erro'> <h3 class='confirm'> Nenhum item foi selecionado para exclusão!</h3> </div>";
        }
    }
    ?>

</body>

</html>