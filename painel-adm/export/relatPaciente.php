<?php
@session_start();
include_once("../../conexao.php");

// Verifica se a variável "id" foi passada como parâmetro
if (isset($_GET['id'])) {
    $idPaciente = $_GET['id'];

    // Busca os relatórios de atendimento do paciente com base no ID do paciente
    $sqlBuscarAtendimentos = $conexao->prepare("SELECT * FROM atendimento WHERE Paciente = :idPaciente");
    $sqlBuscarAtendimentos->bindValue(':idPaciente', $idPaciente);
    $sqlBuscarAtendimentos->execute();
    $dadosAtendimentos = $sqlBuscarAtendimentos->fetchAll();

    $sqlBuscarPaciente = $conexao->prepare("SELECT * from paciente where id = $idPaciente");
    $sqlBuscarPaciente->execute();
    $dadosPaciente = $sqlBuscarPaciente->fetch();

    $convenioPaciente = $dadosPaciente["Convenio"];
    $sqlBuscarConvenio = $conexao->prepare("SELECT * FROM convenios WHERE (ID = :convenio_id)");
    $sqlBuscarConvenio->bindValue(':convenio_id', $convenioPaciente);
    $sqlBuscarConvenio->execute();
    $dadosConvenio = $sqlBuscarConvenio->fetch();

    $visualizar = true;
    ?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="../../css/painel.css">
    <link rel="shortcut icon" href="../img/favicon/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../img/favicon/favicon.ico" type="image/x-icon">
    <title>Relatório de todos os Atendimento - 
        <?php echo $dadosPaciente["Nome"]; ?>
    </title>
</head>
    <body>

        <button class="d-print-none btn btn-primary mb-5 mt-2 ml-2" onclick="window.close()"><i
                class="bi bi-arrow-left"></i> Clique aqui para retornar ao sistema</button>


        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="text-center mt-3 mb-5">RELATÓRIO GERAL DE ATENDIMENTOS</h1>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <div class="text-center ">
                        <?php if (isset($dadosPaciente["Foto"])) { ?>
                            <img class="card-img-top foto-paciente" width="200em"
                                src="../<?php echo $dadosPaciente["Foto"]; ?>"><br><br>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-7">
                    <h2>Paciente:
                        <?php echo $dadosPaciente["Nome"]; ?>
                    </h2>
                    <h3>Gênero:
                        <?php echo $dadosPaciente["Genero"]; ?>
                    </h3>
                    <h3>Convênio:
                        <?php echo $dadosConvenio["Nome"]; ?>
                    </h3>
                </div>
                <div class="col-md-3">
                    <?php
                    date_default_timezone_set('America/Sao_Paulo');
                    $hoje = date('d/m/Y' . " - " . 'H:i');
                    echo $hoje ?>
                    <hr>
                    <h2>SystemPsi</h2>
                    <h6>
                        <?php echo $_SESSION['nome_psicologo']; ?> - CRP:
                        <?php echo $_SESSION['CRP_psicologo']; ?>
                    </h6>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label for="Prontuario">Prontuário:</label>
                    <textarea readonly rows="4" id="Prontuario"
                        class="form-control auto-resize"><?php echo $dadosPaciente["Prontuario"]; ?></textarea>
                </div>
            </div>

            <br>
            <hr><br>

        </div>




        <?php // Loop através dos relatórios de atendimento e exibir cada um em uma página separada
            foreach ($dadosAtendimentos as $dadosAtendimento) {
                $dataInicio = date("d/m", strtotime($dadosAtendimento["Data_Inicio"]));
                $horaInicio = date("H:i", strtotime($dadosAtendimento["Data_Inicio"]));
                $horaFim = date("H:i", strtotime($dadosAtendimento["Data_Fim"]));

                // Aqui você pode exibir os detalhes do relatório de atendimento
                // por exemplo, echo $dadosAtendimento['Data'], echo $dadosAtendimento['Descricao'], etc.
                ?>

            <div class="container">
                <div class="row">
                    <div class="col-md-10">
                        <h1>Atendimento -
                            <?php echo $dataInicio; ?>
                        </h1>
                        <h2>
                            <?php echo $horaInicio . " - " . $horaFim; ?>
                        </h2>
                    </div>
                    <div class="col-md-2">
                        <img class="card-img-top foto-paciente" src="../../img/logosistemapsico.png">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-sm-12">

                        <label for="Motivo">Motivo:</label>
                        <textarea class="form-control auto-resize" id="Motivo" readonly
                            name="Motivo"><?php echo $dadosAtendimento['Motivo']; ?></textarea>

                        <label for="Valor">Valor:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">R$</div>
                            </div>
                            <input type="number" id="Valor" name="Valor" class="form-control" readonly
                                placeholder="Valor do Atendimento" value="<?php echo $dadosAtendimento['Valor']; ?>">
                        </div>

                        <label for="FormaPGTO">Forma de Pagamento:</label>
                        <select class="form-control" id="FormaPGTO" disabled name="FormaPGTO">
                            <option>
                                <?php echo $dadosAtendimento["Forma_Pgto"]; ?>
                            </option>
                        </select>

                        <label for="OBS">OBS.</label>
                        <textarea rows="4" class="form-control auto-resize" readonly id="OBS"
                            name="OBS"><?php echo $dadosAtendimento['OBS']; ?></textarea>

                    </div>
                    <div class="col-md-8">
                        <label for="Registro">Registro:</label>
                        <textarea class="form-control auto-resize" id="Registro" readonly rows="10"
                            name="Registro"><?php echo $dadosAtendimento['Registro']; ?></textarea>
                    </div>
                </div>
            </div>
            <br>
            <hr><br>
            <?php
            }
}
?>
</body>

<footer class="relatorio d-none d-print-block">
    <?php
    date_default_timezone_set('America/Sao_Paulo');
    $hoje = date('d/m/Y' . " - " . 'H:i');
    echo $hoje ?>
    <hr>
    <h2>SystemPsi</h2>
    <h6>
        <?php echo $_SESSION['nome_psicologo']; ?> - CRP:
        <?php echo $_SESSION['CRP_psicologo']; ?>
    </h6>
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

</html>