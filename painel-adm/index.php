<?php
@session_start();
$Nnotificacoes = 0;
require_once("../config.php");
$conexao = conectar();

/*
O código é responsável pelo menu
Sistema de rotas via ACAO
Sistema de manter selecionado no menu
Cabeçalho e rodapé
Buscas gerais no banco (convenio e pacientes)
*/


//Se não estiver setado o nome do psicologo ele vai retornar a tela inicial - segurança
if (!isset($_SESSION['nome_psicologo'])) {
    echo "<script language='javascript'> window.location='index.php'; </script>";
};

$where = "Psicologo = $_SESSION[id_psicologo]";
$listaconvenios = select('convenios', $where);

// Busca dos pacientes - array com todos os pacientes com situacao 2 ativos
$id = $_SESSION['id_psicologo'];
$where = "Psicologo = $id and Situacao = 1 order by Nome asc";
$listapacientes = select('paciente', $where);
$_SESSION['totalPacientes'] = count($listapacientes); // Quantidade total de pacientes para tela inicial



//Manipulação de datas e busca para contagem dos cards iniciais
// Data de hoje
$datetimeToday = date('Y-m-d');
// Data daqui a 7 dias
$datetimeNextWeek = date('Y-m-d', strtotime('+7 days'));

// Busca de todos os agendamentos nos próximos 7 dias
$where = "Psicologo = $_SESSION[id_psicologo] AND Data_Inicio >= $datetimeToday AND Data_Fim <= $datetimeNextWeek AND Realizado = 0";
$listaAgendamentos7 = select('agendar', $where);
$_SESSION['totalAgendamentos7'] = count($listaAgendamentos7);

// Busca de todos os agendamentos do dia de hoje/amanhã
$datetimeAmanha = date('Y-m-d', strtotime('+1 day'));
$where = "Psicologo = $_SESSION[id_psicologo] AND Data_Inicio >= $datetimeToday AND Data_Inicio < $datetimeAmanha AND Realizado = 0";
$listaAgendamentosHoje = select('agendar', $where);
$_SESSION['totalAgendamentosHoje'] = count($listaAgendamentosHoje);

//Variável com os itens dos menus para redirecionamenot/rotas
$item1 = 'home';
$item2 = 'pacientes';
$item3 = 'agenda';
$item4 = 'atendimento';
$item5 = 'configuracoes';
$item6 = 'notificacoes';
$item7 =  'arquivados';
$suporte = 'suporte';
$novopsi = 'novopsi';

//Sistema de verificação do clique do menu - os botões de search também estão incluidos no sistema de rotas

$item1ativo = ''; // Inicializa a variável $item1ativo
$item2ativo = ''; // Inicializa a variável $item2ativo
$item3ativo = ''; // Inicializa a variável $item3ativo
$item4ativo = ''; // Inicializa a variável $item4ativo
$item5ativo = ''; // Inicializa a variável $item5ativo
$item6ativo = ''; // Inicializa a variável $item6ativo
$item7ativo = ''; // Inicializa a variável $item7ativo
$suporteativo = ''; // Inicializa a variável $suporteativo

if (@$_GET['acao'] == $item1) {
    $item1ativo = 'active';
} elseif (@$_GET['acao'] == $item2 or isset($_GET['btnBuscarPacientes'])) {
    $item2ativo = 'active';
} elseif (@$_GET['acao'] == $item3) {
    $item3ativo = 'active';
} elseif (@$_GET['acao'] == $item4 or isset($_GET['btnBuscarAtendimentos'])) {
    $item4ativo = 'active';
} elseif (@$_GET['acao'] == $item5 or isset($_GET[$item5])) {
    $item5ativo = 'active';
} elseif (@$_GET['acao'] == $item6) {
    $item6ativo = 'active';
}elseif (@$_GET['acao'] == $item7 or isset($_GET['btnBuscarPacientesArquivados'])) {
        $item7ativo = 'active';
} elseif (@$_GET['acao'] == $suporte) {
    $suporteativo = 'active';
} else {
    $item1ativo = 'active';
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<source lang="php">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">

    <link rel="stylesheet" type="text/css" href="../css/painel.css">

    <link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon">

    <title>SistemaPsico :: Psicólogo</title>
</head>

<body>

    <nav class="navbar navbar-dark">
        <div class="col-md-12">
            <div class="navbar-brand col-sm-12 col-lg-6"><img src="../img/logosistemapsico.png"></div>
            <li class="float-right nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Psicólogo - <?php echo $_SESSION['nome_psicologo']; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item text-dark" href="index.php?acao=<?php echo $suporte ?>">Suporte</a>
                    <a class="dropdown-item text-dark" href="../logout.php">Sair</a>
                </div>
            </li>
        </div>
    </nav>

    <div class="container-fluid mt-sm-4 row">
        <div class="col-md-3 col-sm-12 mb-3 ">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">

                <a class="nav-link <?php echo $item1ativo ?>" id="v-pills-home-tab" href="index.php?acao=<?php echo $item1 ?>" role="tab" aria-controls="v-pills-home" aria-selected="true"><i class="bi bi-house-fill mr-3"></i>Home</a>

                <a class="nav-link <?php echo $item2ativo ?>" id="link-pacientes" href="index.php?acao=<?php echo $item2 ?>" role="tab" aria-controls="link-pacientes" aria-selected="false"><i class="bi bi-person-fill mr-3"></i>Meus Pacientes</a>

                <a class="nav-link <?php echo $item3ativo ?>" id="v-pills-messages-tab" href="index.php?acao=<?php echo $item3 ?>" role="tab" aria-controls="v-pills-messages" aria-selected="false"><i class="bi bi-calendar-event-fill mr-3"></i>Agenda</a>

                <a class="nav-link <?php echo $item4ativo ?>" id="v-pills-settings-tab" href="index.php?acao=<?php echo $item4 ?>" role="tab" aria-controls="v-pills-settings" aria-selected="false"><i class="bi bi-pencil-square mr-3"></i>Atendimento</a>

                <a class="nav-link <?php echo $item7ativo ?>" id="v-pills-settings-tab" href="index.php?acao=<?php echo $item7 ?>" role="tab" aria-controls="v-pills-settings" aria-selected="false"><i class="bi bi-archive-fill mr-3"></i>Pacientes Arquivados</a>

                <a class="nav-link <?php echo $item5ativo ?>" id="v-pills-settings-tab" href="index.php?acao=<?php echo $item5 ?>" role="tab" aria-controls="v-pills-settings" aria-selected="false"><i class="bi bi-gear-fill mr-3"></i>Configurações</a>

                <?php if ($Nnotificacoes > 0) { ?>
                    <a class="nav-link <?php echo $item6ativo ?>" id="v-pills-settings-tab" href="index.php?acao=<?php echo $item6 ?>" role="tab" aria-controls="v-pills-settings" aria-selected="false"><i class="bi bi-bell-fill mr-3"></i>Notificações
                        <span class="badge badge-dark"> <?php echo $Nnotificacoes; ?> </span></a>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content" id="v-pills-tabContent">

                <div class="tab-pane fade show active" role="tabpanel">
                    <?php //Sistema de rotas com redirecionamento apartir da ACAO e botões de Search
                    if (@$_GET['acao'] == $item1)
                        include_once($item1 . ".php");
                    elseif (@$_GET['acao'] == $item2 or isset($_GET['btnBuscarPacientes']))
                        include_once($item2 . ".php");
                    elseif (@$_GET['acao'] == $item3)
                        include_once($item3 . ".php");
                    elseif (@$_GET['acao'] == $item4 or isset($_GET['btnBuscarAtendimentos']))
                        include_once($item4 . ".php");
                    elseif (@$_GET['acao'] == $item5)
                        include_once($item5 . ".php");
                    elseif (@$_GET['acao'] == $item6)
                        include_once($item6 . ".php");
                    elseif (@$_GET['acao'] == $item7 or isset($_GET['btnBuscarPacientesArquivados']))
                        include_once($item7 . ".php");
                    elseif (@$_GET['acao'] == $suporte)
                        include_once($suporte . ".php");
                    elseif (@$_GET['acao'] == $novopsi)
                        include_once($novopsi . ".php");
                    else
                        include_once($item1 . ".php");
                    ?>

                </div>
            </div>
        </div>

        <footer>
            <h2>SystemPsi</h2>
            <h6><?php echo $_SESSION['nome_psicologo']; ?> - CRP: <?php echo $_SESSION['CRP_psicologo']; ?></h6>
        </footer>
    </div>

</body>

</html>