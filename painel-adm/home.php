<?php
@session_start();
?>

<div class="bemvindo d-none d-lg-block ml-4">
    <h1>Seja bem-vindo, <?php echo $_SESSION['nome_psicologo']; ?></h1>
</div>

<div class="area_cards">
    <div class="row">

        <div class="col-sm-12 col-md-12 col-lg-4 mb-4">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5 col-md-4">
                            <div class="icone-card text-center icon-warning mr-2 mb-1">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="titulo-card">Total de Pacientes</p>
                                <p class="subtitulo-card"><?php echo $_SESSION['totalPacientes']; ?>
                                <p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer rodape-card">
                    Módulo Meus Pacientes
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-12 col-lg-4 mb-4">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5 col-md-4">
                            <div class="icone-card text-center icon-warning mr-2 mb-1">
                                <i class="bi bi-calendar-event-fill"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="titulo-card">Atendimentos na semana</p>
                                <p class="subtitulo-card"><?php echo $_SESSION['totalAgendamentos7']; ?>
                                <p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer rodape-card">
                    Módulo Agenda
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-12 col-lg-4 mb-4">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5 col-md-4">
                            <div class="icone-card text-center icon-warning mr-2 mb-1">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="titulo-card">Atendimentos no dia</p>
                                <p class="subtitulo-card"><?php echo $_SESSION['totalAgendamentosHoje']; ?>
                                <p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer rodape-card">
                    Módulo Atendimento
                </div>
            </div>
        </div>


    </div>
</div>