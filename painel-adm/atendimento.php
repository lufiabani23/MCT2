<?php
// Busca agendamentos e pacientes com agendamento para aquele dia
date_default_timezone_set('America/Sao_Paulo');
@session_start();

$datetimeToday = date('Y-m-d ');

$sqlBuscarAgendamentos = $conexao->prepare("SELECT * FROM agendar WHERE Psicologo = :psicologo_id AND Data_Inicio > :dataInicio AND Data_Inicio < :dataFim AND Realizado = 0");
$sqlBuscarAgendamentos->bindValue(':psicologo_id', $_SESSION['id_psicologo']);
$sqlBuscarAgendamentos->bindValue(':dataInicio', $datetimeToday . "00:00:00");
$sqlBuscarAgendamentos->bindValue(':dataFim', $datetimeToday . "23:59:59");
$sqlBuscarAgendamentos->execute();
$listaAgendamentos = $sqlBuscarAgendamentos->fetchAll();

$sqlBuscarAtendimentos = $conexao->prepare("SELECT * FROM atendimento WHERE Psicologo = :psicologo_id AND Data_Inicio > :dataInicio AND Data_Inicio < :dataFim");
$sqlBuscarAtendimentos->bindValue(':psicologo_id', $_SESSION['id_psicologo']);
$sqlBuscarAtendimentos->bindValue(':dataInicio', $datetimeToday . "00:00:00");
$sqlBuscarAtendimentos->bindValue(':dataFim', $datetimeToday . "23:59:59");
$sqlBuscarAtendimentos->execute();
$listaAtendimentos = $sqlBuscarAtendimentos->fetchAll();

$listaPacientesAtendimentos = array(); // Inicializa a variável $listaPacientes como um array vazio

foreach ($listaAtendimentos as $linha) {
    $idPaciente = $linha["Paciente"];
    $sqlBuscarPaciente = $conexao->prepare("SELECT * FROM paciente WHERE ID = :id_paciente");
    $sqlBuscarPaciente->bindValue(':id_paciente', $idPaciente);
    $sqlBuscarPaciente->execute();
    $paciente = $sqlBuscarPaciente->fetch();

    $listaPacientesAtendimentos[] = $paciente;
}


$listaPacientes = array(); // Inicializa a variável $listaPacientes como um array vazio

foreach ($listaAgendamentos as $linha) {
    $idPaciente = $linha["Paciente"];
    $sqlBuscarPaciente = $conexao->prepare("SELECT * FROM paciente WHERE ID = :id_paciente");
    $sqlBuscarPaciente->bindValue(':id_paciente', $idPaciente);
    $sqlBuscarPaciente->execute();
    $paciente = $sqlBuscarPaciente->fetch();

    $listaPacientes[] = $paciente;
}

if (isset($_POST['btnFinalizarAtendimento'])) {

    $motivo = $_POST['Motivo'];
    $valor = $_POST['Valor'];
    $formaPGTO = $_POST['FormaPGTO'];
    $registro = $_POST['Registro'];
    $OBS = $_POST['OBS'];
    $idPaciente = $_POST['idPaciente'];
    $idAgendamento = $_POST['idAgendamento'];
    $dataInicio = $_POST['DataInicio'];
    $dataFim = date('Y-m-d H:i:s');


    $sqlInserirAtendimento = $conexao->prepare("INSERT INTO atendimento 
    (Data_Inicio, Data_Fim, Valor, Motivo, Forma_Pgto, OBS, Registro, Psicologo, Paciente)
    VALUES (:dataInicio, :dataFim, :valor, :motivo, :formaPGTO, :OBS, :registro, :psicologo, :paciente)");

    $sqlInserirAtendimento->bindParam(':dataInicio', $dataInicio);
    $sqlInserirAtendimento->bindParam(':dataFim', $dataFim);
    $sqlInserirAtendimento->bindParam(':valor', $valor);
    $sqlInserirAtendimento->bindParam(':motivo', $motivo);
    $sqlInserirAtendimento->bindParam(':formaPGTO', $formaPGTO);
    $sqlInserirAtendimento->bindParam(':OBS', $OBS);
    $sqlInserirAtendimento->bindParam(':registro', $registro);
    $sqlInserirAtendimento->bindParam(':psicologo', $_SESSION['id_psicologo']);
    $sqlInserirAtendimento->bindParam(':paciente', $idPaciente);

    $sqlInserirAtendimento->execute();

    $sqlRealizarAgendamento = $conexao->prepare("UPDATE agendar SET Realizado = 1 WHERE ID = $idAgendamento");
    $sqlRealizarAgendamento->execute();

    echo "<script language='javascript'> window.location='index.php?acao=atendimento&alert=success'; </script>";
}


?>

<h1>Próximos atendimentos</h1>
<div class="row ml-2">
    <?php foreach ($listaAgendamentos as $indice => $linha) { ?>
        <div class="col-md-4 col-sm-12 mb-2">
            <div class="card card-atendimento">
                <a href="index.php?acao=atendimento&funcao=atender&idPaciente=<?php echo $listaPacientes[$indice]['ID']; ?>&idAtendimento=<?php echo $listaAgendamentos[$indice]["ID"]; ?>" class="card-atendimento">
                    <div class="card-body card-atendimento-body">
                        <div class="row">
                            <div class="col-5">
                                <?php if (isset($listaPacientes[$indice]["Foto"])) { ?>
                                    <img class="card-img-top foto-paciente" width="200em" src="<?php echo $listaPacientes[$indice]["Foto"]; ?>" alt="Imagem de capa do card">
                                <?php } ?>
                            </div>
                            <div class="col-7">
                                <h2 class="card-title"><?php echo $listaPacientes[$indice]["Nome"]; ?></h2>
                                <h5 class="card-text"><?php
                                                        $dataInicio = strtotime($linha["Data_Inicio"]);
                                                        $formatoData = date("d/m H:i", $dataInicio);
                                                        echo $formatoData; ?></h5>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    <?php } ?>
</div>

<h1>Atendimentos passados</h1>
<div class="row ml-2">
    <?php foreach ($listaAtendimentos as $indice => $linha) { ?>
        <div class="col-md-4 col-sm-12 mb-2">
            <div class="card card-atendimento">
                <a href="index.php?acao=atendimento&funcao=atender&idPaciente=<?php echo $listaPacientes[$indice]['ID']; ?>&idAtendimento=<?php echo $listaAgendamentos[$indice]["ID"]; ?>" class="card-atendimento">
                    <div class="card-body card-atendimento-body">
                        <div class="row">
                            <div class="col-5">
                                <?php if (isset($listaPacientesAtendimentos[$indice]["Foto"])) { ?>
                                    <img class="card-img-top foto-paciente" width="200em" src="<?php echo $listaPacientesAtendimentos[$indice]["Foto"]; ?>" alt="Imagem de capa do card">
                                <?php } ?>
                            </div>
                            <div class="col-7">
                                <h2 class="card-title"><?php echo $listaPacientesAtendimentos[$indice]["Nome"]; ?></h2>
                                <h5 class="card-text"><?php
                                                        $dataInicio = strtotime($linha["Data_Inicio"]);
                                                        $formatoData = date("d/m H:i", $dataInicio);
                                                        echo $formatoData; ?></h5>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    <?php } ?>
</div>
<div class="row mb-3">
    <div class="col text-right">
    <a href="index.php?acao=atendimentosPassados">Ver todos</a>
    </div>

</div>


<!-- MODAL DE ATENDIMENTO -->
<div class="modal fade modal-paciente novo-modal" data-backdrop="static" id="modalAtender" tabindex="-1" role="dialog" aria-labelledby="#modalAtender" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAtender">ATENDIMENTO</h5>
                <div class="col-lg-1 mr-lg-2 d-none d-lg-block"><img class="logo-lateral" src="../img/logosistemapsico.png"></div>
                </button>
            </div>
            <div class="modal-body">
                <?php
                if (isset($_GET['idPaciente']) and isset($_GET['idAtendimento'])) {
                    try {
                        $idPaciente = $_GET["idPaciente"];
                        $idAtendimento = $_GET["idAtendimento"];

                        $sqlBuscarPaciente = $conexao->prepare("SELECT * FROM paciente WHERE (ID = $idPaciente)");
                        $sqlBuscarPaciente->execute();
                        $dadosPaciente = $sqlBuscarPaciente->fetch();


                        $convenioPaciente = $dadosPaciente["Convenio"];
                        $sqlBuscarConvenio = $conexao->prepare("SELECT * FROM convenios WHERE (ID = :convenio_id)");
                        $sqlBuscarConvenio->bindValue(':convenio_id', $convenioPaciente);
                        $sqlBuscarConvenio->execute();
                        $dadosConvenio = $sqlBuscarConvenio->fetch();


                        $sqlBuscarPGTO = $conexao->prepare("SELECT * FROM forma_pgto WHERE (Psicologo = $_SESSION[id_psicologo])");
                        $sqlBuscarPGTO->execute();
                        $formasPGTO = $sqlBuscarPGTO->fetchAll();


                        $sqlBuscarAgendamento = $conexao->prepare("SELECT * FROM agendar WHERE (ID = $idAtendimento)");
                        $sqlBuscarAgendamento->execute();
                        $dadosAgendamento = $sqlBuscarAgendamento->fetch();
                    } catch (Exception $e) {
                        echo $e;
                    }
                ?>

                    <form id="formModalAtendimento" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?acao=<?php echo $item4; ?>">
                        <div class="row">
                            <div class="col-2">
                                <div class=" text-center mt-3">
                                    <img class="card-img-top foto-paciente " width="200em" src="<?php echo $dadosPaciente["Foto"]; ?>"> <br><br>
                                    <h5><?php $dataInicio = strtotime($dadosAgendamento["Data_Inicio"]);
                                        $formatoData = date("d/m - H:i", $dataInicio);
                                        echo $formatoData; ?></h5>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4">
                                <h4>Paciente: <?php echo $dadosPaciente["Nome"]; ?></h4>
                                <h5>Gênero: <?php echo $dadosPaciente["Genero"]; ?></h5>
                                <h5>Convênio: <?php echo $dadosConvenio["Nome"]; ?></h5>

                                <label for="Prontuario">Prontuário:</label><textarea readonly rows="4" id="Prontuario" class="form-control"><?php echo $dadosPaciente["Prontuario"]; ?></textarea>

                                <label for="Motivo">Motivo:</label>
                                <textarea class="form-control" id="Motivo" name="Motivo"><?php echo $dadosAgendamento["Motivo"]; ?></textarea>

                            </div>
                            <div class="col-5">
                                <label for="Registro">Registro:</label>
                                <textarea class="form-control" id="Registro" rows="13" name="Registro"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2"></div>
                            <div class="col-4">
                                <label for="Valor">Valor:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">R$</div>
                                        <input type="number" id="Valor" style="width: 19em;" name="Valor" class="form-control" placeholder="Valor do Atendimento" value="<?php echo $dadosAgendamento['Valor']; ?>">
                                    </div>
                                </div>
                                <label for="FormaPGTO">Forma de Pagamento:</label>
                                <select class="form-control" id="FormaPGTO" name="FormaPGTO">
                                    <option>Dinheiro</option>
                                    <option>PIX</option>
                                    <option>Boleto</option>
                                    <option>Transferência Bancária</option>
                                    <option>Cartão crédito/débito</option>
                                    <option>Outro</option>
                                </select>
                            </div>

                            <div class="col-5">
                                <label for="OBS">OBS.</label>
                                <textarea rows="4" class="form-control" id="OBS" name="OBS"><?php echo $dadosAgendamento["OBS."]; ?></textarea>
                            </div>
                        </div>

                        <input type="hidden" name="DataInicio" value="<?php echo date('Y-m-d H:i:s'); ?>">
                        <input type="hidden" name="idPaciente" value="<?php echo $dadosPaciente["ID"]; ?>">
                        <input type="hidden" name="idAgendamento" value="<?php echo $dadosAgendamento["ID"]; ?>">

                    </form>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <input href="index.php?acao=atendimento" form="formModalAtendimento" type="submit" class="btn btn-success" value="Finalizar Atendimento" name="btnFinalizarAtendimento">
                <button form="formModalPaciente" type="reset" data-dismiss="modal" class="btn btn-danger" name="<?php echo $item4 ?>">Cancelar</button>                    
            </div>
        </div>
    </div>
</div>

<?php
if (@($_GET['funcao']) == "atender") {
    echo '<script> $("#modalAtender").modal("show"); </script>';
}

?>