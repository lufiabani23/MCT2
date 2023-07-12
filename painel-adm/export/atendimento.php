<?php
@session_start();
$idAtendimento = $_GET['idAtendimento'];
include_once("../../conexao.php");

$sqlBuscarAtendimento = $conexao -> prepare("SELECT * from atendimento where ID = $idAtendimento");
$sqlBuscarAtendimento -> execute ();
$dadosAtendimento = $sqlBuscarAtendimento -> fetch();


$sqlBuscarPaciente = $conexao -> prepare("SELECT * from paciente where id = $dadosAtendimento[Paciente]");
$sqlBuscarPaciente -> execute();
$dadosPaciente = $sqlBuscarPaciente -> fetch();

$convenioPaciente = $dadosPaciente["Convenio"];
                        $sqlBuscarConvenio = $conexao->prepare("SELECT * FROM convenios WHERE (ID = :convenio_id)");
                        $sqlBuscarConvenio->bindValue(':convenio_id', $convenioPaciente);
                        $sqlBuscarConvenio->execute();
                        $dadosConvenio = $sqlBuscarConvenio->fetch();

$visualizar = true;

?>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">

    <link rel="stylesheet" type="text/css" href="../../css/painel.css">

    <link rel="shortcut icon" href="../img/favicon/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../img/favicon/favicon.ico" type="image/x-icon">

    <title>Relatório de Atendimento <?php $dataInicio = strtotime($dadosAtendimento["Data_Inicio"]);
                                        $formatoData = date("d/m", $dataInicio);
                                        echo $formatoData; ?> - <?php echo $dadosPaciente["Nome"]; ?></title>
</head>

<body>

<div class="relatorio d-none d-print-block">
    <div class="row">
        <div class="col-4">
        <div class="col-sm-12 col-lg-6"><img class="relatorio" src="../../img/logosistemapsico.png"></div>
        </div>
        <div class="col-8">
        <h1 class="mt-5">RELATÓRIO DE ATENDIMENTO</h1>
        </div>
    </div>
</div>

<h2 class="d-print-none">Feche esta aba para voltar ao sistema!</h2>

<div class="container">
<form id="formModalAtendimento" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?acao=<?php echo $item4; ?>">
                        <div class="row">
                            <div class="col-md-2 col-sm-12">
                                <div class=" text-center mt-3">
                                    <?php if (isset($dadosPaciente["Foto"])) { ?>
                                        <img class="card-img-top foto-paciente " width="200em" src="../<?php echo $dadosPaciente["Foto"]; ?>"> <br><br>
                                    <?php } ?>
                                    <?php if ($visualizar == 0) { ?>
                                        <h5><?php $dataInicio = strtotime($dadosAgendamento["Data_Inicio"]);
                                            $formatoData = date("d/m - H:i", $dataInicio);
                                            echo $formatoData; ?></h5>
                                        </h5>
                                    <?php } else { ?>
                                        <h5><?php $dataInicio = strtotime($dadosAtendimento["Data_Inicio"]);
                                            $formatoData = date("d/m", $dataInicio);
                                            echo $formatoData; ?> <br>
                                            Início: <?php $dataInicio = strtotime($dadosAtendimento["Data_Inicio"]);
                                                    $formatoData = date("H:i", $dataInicio);
                                                    echo $formatoData; ?> <br>
                                            Termínio: <?php $dataFim = strtotime($dadosAtendimento["Data_Fim"]);
                                                        $formatoData = date("H:i", $dataFim);
                                                        echo $formatoData; ?>
                                        </h5>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <h4>Paciente: <?php echo $dadosPaciente["Nome"]; ?></h4>
                                <h5>Gênero: <?php echo $dadosPaciente["Genero"]; ?></h5>
                                <h5>Convênio: <?php echo $dadosConvenio["Nome"]; ?></h5>

                                <label for="Prontuario">Prontuário:</label><textarea readonly rows="4" id="Prontuario" class="form-control auto-resize"><?php echo $dadosPaciente["Prontuario"]; ?></textarea>

                                <label for="Motivo">Motivo:</label>
                                <textarea class="form-control auto-resize" id="Motivo" <?php if ($visualizar == 1) {
                                                                                echo "readonly";
                                                                            } ?> name="Motivo"><?php if ($visualizar == 1) {
                                                                                                                                                    echo $dadosAtendimento["Motivo"];
                                                                                                                                                } else {
                                                                                                                                                    echo $dadosAgendamento["Motivo"];
                                                                                                                                                } ?></textarea>

                            </div>
                            <div class="col-md-5 col-sm-12">
                                <label for="Registro">Registro:</label>
                                <textarea class="form-control auto-resize" id="Registro" <?php echo $visualizar == 1 ? "readonly" : "" ?> rows="13" name="Registro"><?php echo $visualizar == 1 ? $dadosAtendimento["Registro"] : "" ?></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-12 col-5">
                                        <label for="Valor">Valor:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">R$</div>
                                            </div>
                                            <input type="number" id="Valor" name="Valor" class="form-control" <?php echo $visualizar == 1 ? "readonly" : "" ?> placeholder="Valor do Atendimento" value="<?php echo $visualizar == 1 ? $dadosAtendimento["Valor"] : $dadosAgendamento['Valor']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-7">
                                        <label for="FormaPGTO">Forma de Pagamento:</label>
                                        <select class="form-control" id="FormaPGTO" <?php echo $visualizar == 1 ? "readonly" : "" ?> name="FormaPGTO">
                                            <?php if ($visualizar == 0) { ?>
                                                <option>Dinheiro</option>
                                                <option>PIX</option>
                                                <option>Boleto</option>
                                                <option>Transferência Bancária</option>
                                                <option>Cartão crédito/débito</option>
                                                <option>Outro</option>
                                            <?php } else { ?>
                                                <option><?php echo $dadosAtendimento["Forma_Pgto"]; ?></option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5 col-sm-12">
                                <label for="OBS">OBS.</label>
                                <textarea rows="4" class="form-control auto-resize" <?php echo $visualizar == 1 ? "readonly" : "" ?> id="OBS" name="OBS"><?php echo $visualizar == 1 ? $dadosAtendimento["OBS"] : $dadosAgendamento['OBS.']; ?></textarea>
                            </div>
                        </div>


                        <input type="hidden" name="DataInicio" value="<?php echo date('Y-m-d H:i:s'); ?>">
                        <input type="hidden" name="idPaciente" value="<?php echo $dadosPaciente["ID"]; ?>">
                        <input type="hidden" name="idAgendamento" value="<?php echo $dadosAgendamento["ID"]; ?>">

                    </form>
</div>

</div>

<script>
function tabClose() {
  var tab = window.open("","_self");
  tab.close();
}
</script>


<footer class="relatorio d-none d-print-block">
<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('d/m/Y' . " - " . 'H:i'); echo $hoje ?>
  <hr>
            <h2>SystemPsi</h2>
            <h6><?php echo $_SESSION['nome_psicologo']; ?> - CRP: <?php echo $_SESSION['CRP_psicologo']; ?></h6>
</footer>


<script>
    window.print();

    // Função para redimensionar a altura do textarea
    function resizeTextarea() {
        const textareas = document.querySelectorAll('.auto-resize');
        textareas.forEach((textarea) => {
            textarea.style.height = 'auto'; // Redefine a altura para recalcular corretamente
            textarea.style.height = textarea.scrollHeight + 'px'; // Define a altura com base no conteúdo
        });
    }

    // Chama a função inicialmente para redimensionar os textareas
    resizeTextarea();
</script>



</body>