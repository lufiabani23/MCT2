<?php
@session_start();
$idPaciente = $_GET['id'];
include_once("../../conexao.php");

$sqlBuscarPaciente = $conexao -> prepare("SELECT * from paciente where id = $idPaciente");
$sqlBuscarPaciente -> execute();
$dadosPaciente = $sqlBuscarPaciente -> fetchAll(PDO::FETCH_ASSOC);

// Buscar por Convenios Cadastrados
$sql = $conexao->prepare("SELECT * FROM convenios where (Psicologo = $_SESSION[id_psicologo])");
$sql->execute();
$listaconvenios = $sql->fetchALL();

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

    <title>Relatório - <?php echo $dadosPaciente[0]["Nome"]; ?></title>
</head>

<body>

<div class="relatorio d-none d-print-block">
    <div class="row">
        <div class="col-4">
        <div class="col-sm-12 col-lg-6"><img class="relatorio" src="../../img/logosistemapsico.png"></div>
        </div>
        <div class="col-8">
        <h1 class="mt-5">RELATÓRIO DE PACIENTE</h1>
        </div>
    </div>
</div>

<h2 class="d-print-none">Feche esta aba para voltar ao sistema!</h2>

<div class="container">
    <form id="formModalPaciente" enctype="multipart/form-data" method="POST" action="index.php?acao=pacientes<?php if (isset($dadosPaciente)) {
                                                                                                                      echo "&id=$dadosPaciente";
                                                                                                                    } ?>">
            
            <div class="form-row">
              <div class="form-group col-md-10 col-sm-12">
                <label for="Nome">Nome Completo</label>
                <input type="text" class="form-control" id="Nome" name="Nome" placeholder="Nome do Paciente" value="<?php if (isset($dadosPaciente[0]["Nome"])) {
                                                                                                                      echo $dadosPaciente[0]["Nome"];
                                                                                                                    } ?>" required>
              </div>

              <div class="form-group col-md-2 col-sm-12">
                <label for="Convenio">Convênio</label>
                <select id="Convenio" name="Convenio" class="form-control" required>
                  <?php
                  foreach ($listaconvenios as $indice => $linha) {
                    $convenio = $linha['Nome'];
                    $convenioId = $linha['ID'];
                    if (isset($dadosPaciente[0]["Convenio"]) && $dadosPaciente[0]["Convenio"] == $convenioId) {
                      echo '<option value="' . $convenioId . '" selected>' . $convenio . '</option>';
                    } else {
                      echo '<option value="' . $convenioId . '">' . $convenio . '</option>';
                    }
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-4 col-sm-12">
                <label for="Nascimento">Data de Nascimento</label>
                <input type="date" class="form-control" id="Nascimento" name="Nascimento" value="<?php if (isset($dadosPaciente[0]["Data_Nascimento"])) {
                                                                                                    echo $dadosPaciente[0]["Data_Nascimento"];
                                                                                                  } ?>" required>
              </div>

              <div class="form-group col-md-4 col-sm-12">
                <label for="Genero">Gênero</label>
                <select id="Genero" name="Genero" class="form-control" required>
                  <option value="Masculino" <?php if (isset($dadosPaciente[0]["Genero"]) && $dadosPaciente[0]["Genero"] == "Masculino") {
                                              echo 'selected';
                                            } ?>>Masculino</option>
                  <option value="Feminino" <?php if (isset($dadosPaciente[0]["Genero"]) && $dadosPaciente[0]["Genero"] == "Feminino") {
                                              echo 'selected';
                                            } ?>>Feminino</option>
                  <option value="Outro" <?php if (isset($dadosPaciente[0]["Genero"]) && $dadosPaciente[0]["Genero"] == "Outro") {
                                          echo 'selected';
                                        } ?>>Outro</option>
                </select>
              </div>

              <div class="form-group col-md-4 col-sm-12">
                <label for="CPF">CPF</label>
                <input type="text" class="form-control" id="CPF" name="CPF" placeholder="CPF do Paciente" value="<?php if (isset($dadosPaciente[0]["CPF"])) {
                                                                                                                    echo $dadosPaciente[0]["CPF"];
                                                                                                                  } ?>" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6 col-sm-12">
                <label for="Telefone">Telefone</label>
                <input type="text" class="form-control" id="Telefone" name="Telefone" placeholder="Telefone do Paciente" value="<?php if (isset($dadosPaciente[0]["Telefone"])) {
                                                                                                                                  echo $dadosPaciente[0]["Telefone"];
                                                                                                                                } ?>" required>
              </div>

              <div class="form-group col-md-6 col-sm-12">
                <label for="Email">E-mail</label>
                <input type="email" class="form-control" id="Email" name="Email" placeholder="E-mail do Paciente" value="<?php if (isset($dadosPaciente[0]["Email"])) {
                                                                                                                            echo $dadosPaciente[0]["Email"];
                                                                                                                          } ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-12 col-sm-12">
                <label for="Endereco">Endereço</label>
                <input id="Endereco" name="Endereco" type="text" placeholder="Endereço do Paciente" class="form-control" value="<?php if (isset($dadosPaciente[0]["Endereco"])) {
                                                                                                                                  echo $dadosPaciente[0]["Endereco"];
                                                                                                                                } ?>">
              </div>

            </div>
            <div class="form-row">
              <div class="form-group col-md-4 col-sm-12">
                <label for="Foto">Foto</label>
                <?php
                if (isset($dadosPaciente[0]["Foto"])) { ?>
                  <input type="hidden" value="<?php echo $dadosPaciente[0]["Foto"]; ?>" name="enderecoFoto">
                  <img src="../<?php echo $dadosPaciente[0]["Foto"] ?>" alt="Foto Atual" width="200em" class="foto-paciente mt-2 ml-2 img-thumbnail">
                   <?php
                                                                                                                                      }
                                                                                                                                        ?>
              </div>
              <div class="form-group col-md-8 col-sm-12">
                <label for="Prontuario">Prontuário</label>
                <textarea id="Prontuario" class="form-control" rows="7" name="Prontuario" placeholder="Prontuário do Paciente"><?php if (isset($dadosPaciente[0]["Prontuario"])) {echo $dadosPaciente[0]["Prontuario"];} ?></textarea>
            </div>
            </div>

          </form>

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
        const textarea = document.getElementById('Prontuario');
        textarea.style.height = 'auto'; // Redefine a altura para recalcular corretamente
        textarea.style.height = textarea.scrollHeight + 'px'; // Define a altura com base no conteúdo
    }

    // Chama a função inicialmente para redimensionar o textarea
    resizeTextarea();
</script>


</body>