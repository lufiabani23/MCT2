<?php
// Busca atendimentos passados
date_default_timezone_set('America/Sao_Paulo');
@session_start();



// Sistema para buscar atendimentos
if (isset($_GET['btnBuscarAtendimentos']) && $_GET['txtBuscarAtendimentos'] != "") {
    $txtBuscarAtendimentos = "%" . $_GET['txtBuscarAtendimentos'] . "%";
    $sqlBuscarPaciente = $conexao->prepare("SELECT * FROM paciente WHERE Nome LIKE :buscarNome");
    $sqlBuscarPaciente->bindValue(':buscarNome', $txtBuscarAtendimentos);
    $sqlBuscarPaciente->execute();

    if ($sqlBuscarPaciente->rowCount() > 0) {
        $pacientes = $sqlBuscarPaciente->fetchAll();

        $listaAtendimentos = array(); // Inicializa a lista de atendimentos

        foreach ($pacientes as $paciente) {
            $idPaciente = $paciente["ID"];

            $sqlBuscarAtendimentos = $conexao->prepare("SELECT * FROM atendimento WHERE Psicologo = :psicologo_id AND Paciente = :paciente_id");
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
    $sqlBuscarAtendimentos = $conexao->prepare("SELECT * FROM atendimento WHERE Psicologo = :psicologo_id");
    $sqlBuscarAtendimentos->bindValue(':psicologo_id', $_SESSION['id_psicologo']);
    $sqlBuscarAtendimentos->execute();
    $listaAtendimentos = $sqlBuscarAtendimentos->fetchAll();
}

$listaPacientesAtendimentos = array(); // Inicializa a variável $listaPacientesAtendimentos como um array vazio

foreach ($listaAtendimentos as $linha) {
    $idPaciente = $linha["Paciente"];
    $sqlBuscarPaciente = $conexao->prepare("SELECT * FROM paciente WHERE ID = :id_paciente");
    $sqlBuscarPaciente->bindValue(':id_paciente', $idPaciente);
    $sqlBuscarPaciente->execute();
    $paciente = $sqlBuscarPaciente->fetch();

    $listaPacientesAtendimentos[] = $paciente;
}


?>

<!-- BOTÃO DE NOVO PACIENTE E BOTÃO DE PESQUISA -->

<div class="row">
    <div class="col-8">
        <h1>Todos atendimentos passados</h1>
    </div>
    <div class="col-4 ">
        <div class="float-right">
            <a href="index.php?acao=atendimentosPassados">Voltar</a>
        </div>
    </div>
</div>
</div>

<div class="row mb-1">
    <div class="col-8"></div>
    <div class="col-4">
        <div class="float-right">
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2 mr-md-0" type="search" placeholder="Buscar atendimento" aria-label="Search" name="txtBuscarAtendimentos" value="<?php if (isset($_GET['btnBuscarPacientes']) and $_GET['txtBuscarPacientes'] != "") echo  $_GET['txtBuscarPacientes'];   //manter o nome pesquisado no input   
                                                                                                                                                                ?>">
                <button class="btn btn-outline-primary my-2 my-sm-0" type="submit" name="btnBuscarAtendimentos">Buscar</button>
            </form>
        </div>
    </div>
</div>



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