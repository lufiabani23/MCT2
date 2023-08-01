<?php
// Busca agendamentos e pacientes com agendamento para aquele dia
date_default_timezone_set('America/Sao_Paulo');
@session_start();

$datetimeToday = date('Y-m-d ');

//BUSCAR AGENDAMENTOS PARA HOJE
$sqlBuscarAgendamentos = $conexao->prepare("SELECT * FROM agendar WHERE Psicologo = :psicologo_id AND Data_Inicio > :dataInicio AND Data_Inicio < :dataFim AND Realizado = 0");
$sqlBuscarAgendamentos->bindValue(':psicologo_id', $_SESSION['id_psicologo']);
$sqlBuscarAgendamentos->bindValue(':dataInicio', $datetimeToday . "00:00:00");
$sqlBuscarAgendamentos->bindValue(':dataFim', $datetimeToday . "23:59:59");
$sqlBuscarAgendamentos->execute();
$listaAgendamentos = $sqlBuscarAgendamentos->fetchAll();

// BUSCAR ATENDIMENTOS PASSADOS
$sqlBuscarAtendimentos = $conexao->prepare("SELECT * FROM atendimento WHERE Psicologo = :psicologo_id");
$sqlBuscarAtendimentos->bindValue(':psicologo_id', $_SESSION['id_psicologo']);
$sqlBuscarAtendimentos->execute();
$listaAtendimentos = $sqlBuscarAtendimentos->fetchAll();

$listaPacientes = array(); // Inicializa a variável $listaPacientes como um array vazio

// CONVERTER O AGENDAMENTO EM PACIENTE
foreach ($listaAgendamentos as $linha) {
    $idPaciente = $linha["Paciente"];
    $sqlBuscarPaciente = $conexao->prepare("SELECT * FROM paciente WHERE ID = :id_paciente");
    $sqlBuscarPaciente->bindValue(':id_paciente', $idPaciente);
    $sqlBuscarPaciente->execute();
    $paciente = $sqlBuscarPaciente->fetch();

    $listaPacientes[] = $paciente;
}

// INSERINDO (FINALIZANDO) ATENDIMENTO
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

    echo "<script language='javascript'> window.location='index.php?acao=$item4&alert=success'; </script>";
}

// Sistema para buscar atendimentos - CONVERSÃO DE ATENDIMENTO PARA PACIENTE
if (isset($_GET['btnBuscarAtendimentos']) && $_GET['txtBuscarAtendimentos'] != "") {
    $txtBuscarAtendimentos = isset($_GET['txtBuscarAtendimentos']) ? "%" . $_GET['txtBuscarAtendimentos'] . "%" : "%";
    $sqlBuscarPaciente = $conexao->prepare("SELECT * FROM paciente WHERE Nome LIKE :buscarNome");
    $sqlBuscarPaciente->bindValue(':buscarNome', $txtBuscarAtendimentos);
    $sqlBuscarPaciente->execute();



    if ($sqlBuscarPaciente->rowCount() > 0) {
        $pacientes = $sqlBuscarPaciente->fetchAll();

        $listaAtendimentos = array(); // Inicializa a lista de atendimentos

        foreach ($pacientes as $paciente) {
            $idPaciente = $paciente["ID"];

            $sqlBuscarAtendimentos = $conexao->prepare("SELECT * FROM atendimento WHERE Psicologo = :psicologo_id AND Paciente = :paciente_id ORDER BY ID DESC");
            $sqlBuscarAtendimentos->bindValue(':psicologo_id', $_SESSION['id_psicologo']);
            $sqlBuscarAtendimentos->bindValue(':paciente_id', $idPaciente);
            $sqlBuscarAtendimentos->execute();

            $atendimentos = $sqlBuscarAtendimentos->fetchAll();

            $listaAtendimentos = array_merge($listaAtendimentos, $atendimentos); // Adiciona os atendimentos encontrados à lista geral
        }
    } else {
        $listaAtendimentos = array(); // Caso nenhum paciente seja encontrado, a lista de atendimentos será vazia
    }
} else {
    $sqlBuscarAtendimentos = $conexao->prepare("SELECT * FROM atendimento WHERE Psicologo = :psicologo_id ORDER BY ID DESC");
    $sqlBuscarAtendimentos->bindValue(':psicologo_id', $_SESSION['id_psicologo']);
    $sqlBuscarAtendimentos->execute();
    $listaAtendimentos = $sqlBuscarAtendimentos->fetchAll();
}

$listaPacientesAtendimentos = array(); // Inicializa a variável $listaPacientesAtendimentos como um array vazio

// Definir o número de itens por página
$itensPorPagina = 6;

// Contar o número total de atendimentos
$totalAtendimentos = count($listaAtendimentos);

// Calcular o número total de páginas
$totalPaginas = ceil(count($listaAtendimentos) / $itensPorPagina);

// Verificar a página atual
$paginaAtual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

// Calcular o índice inicial e final dos itens a serem exibidos na página atual
$indiceInicial = ($paginaAtual - 1) * $itensPorPagina;
$indiceFinal = $indiceInicial + $itensPorPagina;

// Obter os atendimentos a serem exibidos na página atual
$atendimentosPagina = array_slice($listaAtendimentos, $indiceInicial, $itensPorPagina);

foreach ($atendimentosPagina as $linha) {
    $idPaciente = $linha["Paciente"];
    $sqlBuscarPaciente = $conexao->prepare("SELECT * FROM paciente WHERE ID = :id_paciente");
    $sqlBuscarPaciente->bindValue(':id_paciente', $idPaciente);
    $sqlBuscarPaciente->execute();
    $paciente = $sqlBuscarPaciente->fetch();

    $listaPacientesAtendimentos[] = $paciente;
}
?>

<h1 class="ml-2">Próximos atendimentos</h1>
<div class="row">
    <?php if (count($listaAgendamentos) > 0) { ?>
        <?php foreach ($listaAgendamentos as $indice => $linha) { ?>
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card card-atendimento">
                    <a href="index.php?acao=<?php echo $item4; ?>&funcao=atender&idPaciente=<?php echo $listaPacientes[$indice]['ID']; ?>&idAgendamento=<?php echo $listaAgendamentos[$indice]["ID"]; ?>" class="card-atendimento">
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
        <?php }
    } else { ?>
        <div class="col-12">
            <p>Você não tem nenhum atendimento para hoje :)</p>
        </div>
    <?php } ?>
</div>

<h1 class="ml-2">Atendimentos realizados</h1>


<div class="row mb-1">
    <div class="col">
        <div class="float-right">
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2 " type="search" placeholder="Buscar atendimento" aria-label="Search" name="txtBuscarAtendimentos" value="<?php echo isset($_GET['txtBuscarAtendimentos']) ? $_GET['txtBuscarAtendimentos'] : ''; ?>">
                <button class="btn btn-outline-primary my-2 my-sm-0" type="submit" name="btnBuscarAtendimentos">Buscar</button>
            </form>
        </div>
    </div>
</div>

<div class="row card-realizado">
    <?php if (count($atendimentosPagina) > 0) { ?>
        <?php foreach ($atendimentosPagina as $indice => $linha) { ?>
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card card-atendimento">
                    <a href="index.php?acao=<?php echo $item4; ?>&funcao=visualizarAtendimento&idPaciente=<?php echo $listaPacientesAtendimentos[$indice]['ID']; ?>&idAtendimento=<?php echo $listaAtendimentos[$indice]["ID"]; ?>" class="card-atendimento">
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
    <?php } else { ?>
        <div class="col-12">
            <p>Nenhum atendimento encontrado.</p>
        </div>
    <?php } ?>
</div>



<div class="modal fade modal-paciente novo-modal" data-backdrop="static" id="modalAtenderVisualizar" tabindex="-1" role="dialog" aria-labelledby="#modalAtenderVisualizar" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAtenderVisualizar">ATENDIMENTO</h5>
                <div class="col-lg-1 mr-lg-2 d-none d-lg-block"><img class="logo-lateral" src="../img/logosistemapsico.png"></div>
            </div>
            <div class="modal-body">
                <?php

                if (isset($_GET['funcao']) and $_GET['funcao'] == "visualizarAtendimento") {
                    $visualizar = true;
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

                        $sqlBuscarAtendimento = $conexao->prepare("SELECT * FROM atendimento WHERE (ID = $idAtendimento)");
                        $sqlBuscarAtendimento->execute();
                        $dadosAtendimento = $sqlBuscarAtendimento->fetch();
                    } catch (Exception $e) {
                        echo $e;
                    }
                } else {
                    $visualizar = false;
                }
                if (isset($_GET['funcao']) and $_GET['funcao'] == "atender") {
                    try {
                        $idPaciente = $_GET["idPaciente"];
                        $idAgendamento = $_GET["idAgendamento"];

                        $sqlBuscarPaciente = $conexao->prepare("SELECT * FROM paciente WHERE (ID = $idPaciente)");
                        $sqlBuscarPaciente->execute();
                        $dadosPaciente = $sqlBuscarPaciente->fetch();


                        $convenioPaciente = $dadosPaciente["Convenio"];
                        $sqlBuscarConvenio = $conexao->prepare("SELECT * FROM convenios WHERE (ID = :convenio_id)");
                        $sqlBuscarConvenio->bindValue(':convenio_id', $convenioPaciente);
                        $sqlBuscarConvenio->execute();
                        $dadosConvenio = $sqlBuscarConvenio->fetch();

                        $sqlBuscarAgendamento = $conexao->prepare("SELECT * FROM agendar WHERE (ID = $idAgendamento)");
                        $sqlBuscarAgendamento->execute();
                        $dadosAgendamento = $sqlBuscarAgendamento->fetch();
                    } catch (Exception $e) {
                        echo $e;
                    }
                }


                if (isset($_GET['idPaciente'])) { ?>

                    <form id="formModalAtendimento" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?acao=<?php echo $item4; ?>">
                        <div class="row">
                            <div class="col-md-2 col-sm-12">
                                <div class=" text-center mt-3">
                                    <?php if (isset($dadosPaciente["Foto"])) { ?>
                                        <img class="card-img-top foto-paciente " width="200em" src="<?php echo $dadosPaciente["Foto"]; ?>"> <br><br>
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

                                <label for="Prontuario">Prontuário:</label><textarea readonly rows="4" id="Prontuario" class="form-control"><?php echo $dadosPaciente["Prontuario"]; ?></textarea>

                                <label for="Motivo">Motivo:</label>
                                <textarea class="form-control" id="Motivo" <?php if ($visualizar == 1) {
                                                                                echo "readonly";
                                                                            } ?> name="Motivo"><?php if ($visualizar == 1) {
                                                                                                                                                    echo $dadosAtendimento["Motivo"];
                                                                                                                                                } else {
                                                                                                                                                    echo $dadosAgendamento["Motivo"];
                                                                                                                                                } ?></textarea>

                            </div>
                            <div class="col-md-5 col-sm-12">
                                <label for="Registro">Registro:</label>
                                <textarea class="form-control" id="Registro" <?php echo $visualizar == 1 ? "readonly" : "" ?> rows="13" name="Registro"><?php echo $visualizar == 1 ? $dadosAtendimento["Registro"] : "" ?></textarea>
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
                                <textarea rows="4" class="form-control" <?php echo $visualizar == 1 ? "readonly" : "" ?> id="OBS" name="OBS"><?php echo $visualizar == 1 ? $dadosAtendimento["OBS"] : $dadosAgendamento['OBS.']; ?></textarea>
                            </div>
                        </div>


                        <input type="hidden" name="DataInicio" value="<?php echo date('Y-m-d H:i:s'); ?>">
                        <input type="hidden" name="idPaciente" value="<?php echo $dadosPaciente["ID"]; ?>">
                        <?php if ($visualizar == 0) { ?><input type="hidden" name="idAgendamento" value="<?php echo $dadosAgendamento["ID"]; ?>"> <?php } ?>

                    </form>
                <?php } ?>
            </div>

            <div class="modal-footer">
                <?php if ($visualizar == 1) { ?>
                    <div class="text-left mr-auto">
                        <a form="formModalPaciente" class="btn btn-warning text-white" href="export/atendimento.php?idAtendimento=<?php if (isset($dadosAtendimento["ID"])) {
                                                                                                                                        echo $dadosAtendimento["ID"];
                                                                                                                                    } ?>" target="_blank">Imprimir Relatório</a>
                    </div>
                <?php } ?>

                <?php if ($visualizar == 0) { ?><input form="formModalAtendimento" type="submit" class="btn btn-success" value="Finalizar Atendimento" name="btnFinalizarAtendimento"> <?php } ?>
                <button form="formModalPaciente" type="reset" data-dismiss="modal" class="btn btn-danger" name="<?php echo $item4 ?>"><?php echo $visualizar == 1 ? "Fechar" : "Cancelar" ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Exibir a paginação -->
<div class="row">
    <div class="col-12">
        <nav aria-label="Navegação de página">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPaginas; $i++) { ?>
                    <li class="page-item <?php if ($i == $paginaAtual) echo 'active'; ?>">
                        <a class="page-link" href="?acao=atendimento&<?php if (isset($_GET['txtBuscarAtendimentos'])) {
                                                                            echo "&txtBuscarAtendimentos=" . $_GET['txtBuscarAtendimentos'];
                                                                        }
                                                                        if (isset($_GET['btnBuscarAtendimentos'])) {
                                                                            echo "&btnBuscarAtendimentos=";
                                                                        } ?>&pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php } ?>

            </ul>
        </nav>
    </div>
</div>

<?php
if (@($_GET['funcao']) == "atender") {
    echo '<script> $("#modalAtenderVisualizar").modal("show"); </script>';
} elseif (@($_GET['funcao']) == "visualizarAtendimento") {
    echo '<script> $("#modalAtenderVisualizar").modal("show"); </script>';
}

?>