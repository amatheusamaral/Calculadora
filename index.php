<?php
$num1 = '';
$num2 = '';
$operacao = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $num1 = isset($_POST['num1']) ? $_POST['num1'] : '';
    $num2 = isset($_POST['num2']) ? $_POST['num2'] : '';
    $operacao = isset($_POST['operacao']) ? $_POST['operacao'] : '';

    if (isset($_POST["calcular"])) {

        if (!empty($num1) && !empty($num2) && !empty($operacao)) {
            $resultado = calcular($num1, $num2, $operacao);
            $historicoItem = "$num1 $operacao $num2 = $resultado";
            salvarHistorico($historicoItem);
        } else {
            echo "<span style='color: white;'>Por favor, preencha todos os campos.</span>";
        }
    } elseif (isset($_POST["memoria"])) {
        $memoria = [
            "num1" => $num1,
            "num2" => $num2,
            "operacao" => $operacao
        ];
        $_SESSION['memoria'] = $memoria;
        echo "<span style='color: white;'>Valores salvos na memória.</span>";
    } elseif (isset($_POST["pegar_valores"])) {
        if (isset($_SESSION['memoria'])) {
            $memoria = $_SESSION['memoria'];
            $num1 = $memoria['num1'];
            $num2 = $memoria['num2'];
            $operacao = $memoria['operacao'];
        }
    } elseif (isset($_POST["limpar_historico"])) {
        file_put_contents("historico.txt", "");
        echo "<span style='color: white;'>Histórico apagado com sucesso.</span>";
    }
}

function calcular($num1, $num2, $operacao) {
    switch ($operacao) {
        case '+':
            return $num1 + $num2;
        case '-':
            return $num1 - $num2;
        case '*':
            return $num1 * $num2;
        case '/':
            if ($num2 != 0) {
                return $num1 / $num2;
            } else {
                return "Erro: divisão por zero";
            }
        case 'fatorial':
            return fatorial($num1);
        case 'potencia':
            return pow($num1, $num2);
        default:
            return "Operação inválida";
    }
}

function fatorial($num) {
    if ($num == 0) {
        return 1;
    } else {
        return $num * fatorial($num - 1);
    }
}

function salvarHistorico($historicoItem) {
    $historicoFile = fopen("historico.txt", "a");

    fwrite($historicoFile, $historicoItem . PHP_EOL);
    fclose($historicoFile);
}

function exibirHistorico() {
    if (file_exists("historico.txt")) {
        $historicoFile = fopen("historico.txt", "r");
        while (!feof($historicoFile)) {
            $linha = fgets($historicoFile);
            echo "<li>$linha</li>";
        }
        fclose($historicoFile);
    } else {
        echo "<li>Histórico vazio</li>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #1b212c;
        }

        .calculator {
            width: 60%;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .calculator h1 {
            text-align: center;
        }

        .calculator input[type="text"],
        .calculator select {
            width: calc(30% - 10px); 
            padding: 10px;
            margin-bottom: 10px;
            font-size: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .calculator button {
            width: calc(30% - 10px); 
            height: 50px;
            font-size: 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .calculator button[name="calcular"] {
            background-color: #22543a;
            color: white;
        }

        .calculator button[name="pegar_valores"] {
            background-color: #D3D3D3;
            color: black;
        }

        .calculator button[name="memoria"] {
            background-color: #35A6FF;
            color: white ;
        }

        .calculator button[name="limpar_historico"] {
            background-color: #35A6FF;
            color: white;
        }

        .calculator #historico {
            margin-top: 20px;
            font-size: 16px;
        }

        .calculator #historico h2 {
            margin-bottom: 10px;
        }

        .calculator #lista-historico {
            list-style-type: none;
            padding: 0;
        }

        .calculator #lista-historico li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="calculator">
        <h1>Calculadora PHP</h1>
        <form method="post">
            <input type="text" name="num1" id="num1" placeholder="Número 1" value="<?php echo $num1; ?>">
            <select name="operacao" id="operacao">
                <option value="+" <?php if ($operacao == '+') echo 'selected'; ?>>+</option>
                <option value="-" <?php if ($operacao == '-') echo 'selected'; ?>>-</option>
                <option value="*" <?php if ($operacao == '*') echo 'selected'; ?>>*</option>
                <option value="/" <?php if ($operacao == '/') echo 'selected'; ?>>/</option>
                <option value="fatorial" <?php if ($operacao == 'fatorial') echo 'selected'; ?>>n!</option>
                <option value="potencia" <?php if ($operacao == 'potencia') echo 'selected'; ?>>x^y</option>
            </select>
            <input type="text" name="num2" id="num2" placeholder="Número 2" value="<?php echo $num2; ?>">
            <button type="submit" name="calcular" class="btn-calcular">Calcular</button>
            <input type="text" value="<?php 
                if (isset($_POST['num1'], $_POST['num2'], $_POST['operacao'])) {
                    $num1 = $_POST['num1'];
                    $num2 = $_POST['num2'];
                    $operacao = $_POST['operacao'];
                    $resultado = calcular($num1, $num2, $operacao);
                    echo "$num1 $operacao $num2 = $resultado";
                }
            ?>" readonly>
        </form>
        <form method="post">
            <button type="submit" name="memoria" class="btn-memoria">M</button>
            <button type="submit" name="pegar_valores" class="btn-pegar-valores">Recuperar Valores</button>
            <button type="submit" name="limpar_historico" class="btn-limpar-historico">Apagar Histórico</button>
        </form>
        <div id="historico">
            <h2>Histórico</h2>
            <ul id="lista-historico">
                <?php exibirHistorico(); ?>
            </ul>
        </div>
    </div>
</body>
</html>