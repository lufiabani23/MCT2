<?php
// Busca agendamentos e pacientes com agendamento para aquele dia
date_default_timezone_set('America/Sao_Paulo');

$datetimeToday = date('Y-m-d ');

$sqlBuscarAgendamentos = $conexao->prepare("SELECT * FROM agendar WHERE Psicologo = :psicologo_id AND Data_Inicio > :dataInicio AND Data_Inicio < :dataFim");
$sqlBuscarAgendamentos->bindValue(':psicologo_id', $_SESSION['id_psicologo']);
$sqlBuscarAgendamentos->bindValue(':dataInicio', $datetimeToday . "00:00:00");
$sqlBuscarAgendamentos->bindValue(':dataFim', $datetimeToday . "23:59:59");
$sqlBuscarAgendamentos->execute();
$listaAgendamentos = $sqlBuscarAgendamentos->fetchAll();


$listaPacientes = array(); // Inicializa a variável $listaPacientes como um array vazio

foreach ($listaAgendamentos as $linha) {
    $idPaciente = $linha["Paciente"];
    $sqlBuscarPaciente = $conexao->prepare("SELECT * FROM paciente WHERE ID = :id_paciente");
    $sqlBuscarPaciente->bindValue(':id_paciente', $idPaciente);
    $sqlBuscarPaciente->execute();
    $paciente = $sqlBuscarPaciente->fetch();

    $listaPacientes[] = $paciente;
}

?>

<h1>Próximos atendimentos</h1>
<div class="row">
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











<!-- MODAL DE PACIENTE -->
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


                    $sqlBuscarAgendamento = $conexao->prepare("SELECT * FROM agendar WHERE (ID = $idAtendimento)");
                    $sqlBuscarAgendamento->execute();
                    $dadosAgendamento = $sqlBuscarAgendamento->fetch(); ?>


                    <div class="row">
                        <div class="col-2">
                            <img class="card-img-top foto-paciente " width="200em" src="<?php echo $dadosPaciente["Foto"]; ?>">
                        </div>
                        <div class="col-10">
                            <h4>Paciente: <?php echo $dadosPaciente["Nome"]; ?></h4>
                            <h5>Gênero: <?php echo $dadosPaciente["Genero"]; ?></h5>
                            <h5>Convênio: <?php echo $dadosConvenio["Nome"]; ?></h5>
                        </div>
                    </div>


                <?php } ?>
            </div>
            <div class="modal-footer">
                <a href="index.php?acao=atendimento" form="formModalPaciente" type="submit" class="btn btn-success" name="btnFinalizarAtendimento">Finalizar Atendimento</a>
            </div>
        </div>
    </div>
</div>

<?php
if (@($_GET['funcao']) == "atender") {
    echo '<script> $("#modalAtender").modal("show"); </script>';
}

?>