<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
<script src="../js/mascaras.js"></script>

<?php
include_once('../conexao.php');
@session_start();

//O convenio com nome "Particular" deve ser cadastrado antes de utilizar o sistema

// Buscar por Convenios Cadastrados
$sql = $conexao -> prepare("SELECT * FROM convenios where (Psicologo = $_SESSION[id_psicologo] and Nome != 'Particular')");
$sql->execute();
$listaconvenios = $sql->fetchALL();


// Sistema para buscar pacientes
if (isset($_GET['btnBuscarPacientes']) and $_GET['txtBuscarPacientes'] != "") {
  $txtBuscarPaciente = "%" . $_GET['txtBuscarPacientes'] . "%";
  $sqlBuscarPacientes = $conexao->prepare("SELECT * FROM paciente where Nome like '$txtBuscarPaciente' order by Nome asc");
  $sqlBuscarPacientes->execute();
  $listapacientes = $sqlBuscarPacientes->fetchALL();
} else {
  $sqlBuscarPacientes = $conexao->prepare("SELECT * FROM paciente order by Nome asc");
  $sqlBuscarPacientes->execute();
  $listapacientes = $sqlBuscarPacientes->fetchALL();
}

//INSERIR NOVO PACIENTE
if (isset($_POST['btnNovoPaciente'])) {
  $nome = $_POST['Nome'];
  $telefone = $_POST['Telefone'];
  $email = $_POST['Email'];
  $nascimento = $_POST['Nascimento'];
  $convenio = $_POST['Convenio'];
  $CPF = $_POST['CPF'];
  $genero = $_POST['Genero'];

  $sqlBuscaConvenio = $conexao->prepare("SELECT ID FROM convenios where (Nome = '$convenio')");
  $sqlBuscaConvenio -> execute();
  $idconvenio = $sqlBuscaConvenio->fetchAll(PDO::FETCH_ASSOC);

  if (empty($nome) or empty($telefone) or empty($email) or empty($nascimento) or empty($convenio) or empty($CPF)) {
    echo "<script language='javascript'> window.alert('Campo obrigatório em branco'); </script>";
    echo "<script language='javascript'> window.location='index.php'; </script>";
  } else {
    try {
      $sql = $conexao->prepare("INSERT INTO paciente VALUES (null,?,?,?,?,?,null,?,?,?)");
      $sql->execute(array(
        $nome, $telefone, $email, $nascimento, $idconvenio[0]['ID'], $genero, $CPF, $_SESSION['id_psicologo']));
      echo "<script language='javascript'> window.location='index.php?acao=pacientes'; </script>";
    } catch (Exception $e) {
      echo "<script language='javascript'> window.alert('Erro ao cadastrar paciente!'); </script>";
      echo "<script language='javascript'> window.location='index.php?acao=pacientes'; </script>";

    }
  }
}

// EXCLUSÃO DE PACIENTE
if (isset($_GET['idexcluir'])) {
  $sql = $conexao->prepare("DELETE FROM paciente WHERE id = $_GET[idexcluir]");
  $sql->execute();
  echo "<script language='javascript'> window.location='index.php?acao=pacientes'; </script>";
}
?>

<!-- BOTÃO DE NOVO PACIENTE E BOTÃO DE PESQUISA -->
<div class="row mt-1"> <!-- botão alinhado a borda da tabela -->

  <div class="col-md-6 col-sm-12">
    <button type="button" class="btn btn-secondary novo-paciente" data-toggle="modal" data-target="#botaoNovoPaciente">
      <span style="font-size: 16pt;">+</span> Novo paciente
    </button>
  </div>

  <!-- Form para envio dos dados para pesquisa -->

  <div class="col-md-6 col-sm-12">
    <div class="float-right">
      <form class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="search" placeholder="Buscar paciente" aria-label="Search" name="txtBuscarPacientes" value="<?php if (isset($_GET['btnBuscarPacientes']) and $_GET['txtBuscarPacientes'] != "") echo  $_GET['txtBuscarPacientes'];   //manter o nome pesquisado no input   
                                                                                                                                              ?>">
        <button class="btn btn-outline-primary my-2 my-sm-0" type="submit" name="btnBuscarPacientes">Buscar</button>
      </form>
    </div>
  </div>
</div>

<!-- CAIXA MODAL DE NOVO PACIENTE -->
<div class="modal fade" id="botaoNovoPaciente" tabindex="-1" role="dialog" aria-labelledby="#modalNovoPaciente" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNovoPaciente">Cadastrar novo paciente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="CadastroPaciente" method="POST" action="index.php?acao=pacientes">
          <div class="form-row">
            <div class="form-group col-md-4 col-sm-12">
              <label for="Nome">Nome Completo</label>
              <input type="text" class="form-control" id="Nome" name="Nome" placeholder="Nome do paciente" required>
            </div>

            <div class="form-group col-md-4 col-sm-12">
              <label for="Telefone">Telefone</label>
              <input type="text" class="form-control" id="Telefone" name="Telefone" placeholder="Telefone do paciente">
            </div>
            <div class="form-group col-md-4 col-sm-12">
              <label for="Email">E-mail</label>
              <input type="email" class="form-control" id="Email" name="Email" placeholder="E-mail do paciente">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4 col-sm-12">
              <label for="CPF">CPF</label>
              <input type="text" class="form-control" id="CPF" name="CPF" placeholder="CPF do paciente" required>
            </div>

            <div class="form-group col-md-4 col-sm-12">
              <label for="Convenio">Convênio</label>
              <select id="Convenio" name="Convenio" class="form-control" required>
                <option selected>Particular</option>
                <?php
                foreach ($listaconvenios as $indice => $linha) {
                ?>
                <option><?php echo $linha['Nome']; ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group col-md-4 col-sm-12">
              <label for="Nascimento">Data de Nascimento</label>
              <input type="date" class="form-control" id="Nascimento" name="Nascimento" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6 col-sm-12">
              <label for="Endereco">Endereço</label>
              <input id="Endereco" type="text" placeholder="Endereço do paciente" class="form-control">
            </div>
            <div class="form-group col-md-6 col-sm-12">
              <label for="Genero">Gênero</label>
              <select id="Genero" name="Genero" class="form-control" required>
                <option>Masculino</option>
                <option>Feminino</option>
                <option>Outro</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12 col-sm-12">
              <label for="Prontuario">Prontuário</label>
              <textarea id="Prontuario" class="form-control" name="Prontuario"></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6 col-sm-12">
              <label for="Foto">Foto <sub>(png, jpeg, jpg)</sub></label>
              <input type="file" id="Foto" class="form-control" name="Foto">
            </div>
            <div class="form-group col-md-6 col-sm-12">
              <label for="Anexos">Anexos</label>
              <input type="file" id="Anexos" multiple="multiple" class="form-control" name="Anexos">
            </div>
          </div>

        </form>
      </div>

      <div class="modal-footer">
        <button form="CadastroPaciente" type="submit" class="btn btn-success" name="btnNovoPaciente">Cadastrar Paciente</button>
        <button form="CadastroPaciente" type="reset" class="btn btn-danger" name="<?php echo $item2 ?>">Limpar Dados</button>
      </div>
    </div>
  </div>
</div>

<!-- TABELA DE PACIENTE -->
<!-- botão excluir leva o ID do paciente da linha -->
<table class="table table-striped mt-2 lista-pacientes">
  <thead>
    <tr>
      <th scope="col">Cod.</th>
      <th scope="col">Nome Completo</th>
      <th scope="col">Convênio</th>
      <th scope="col" class="d-none d-sm-block">Telefone</th>
      <th scope="col">Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php
    foreach ($listapacientes as $indice => $linha) {
      if ($linha['Psicologo'] == $_SESSION['id_psicologo']) {
    ?>
        <tr>
          <th scope="row"><?php echo $linha['ID'] ?></th>
          <td><?php echo $linha['Nome'] ?></td>
          <td>
            <?php
              // Converter o ID convenio para Nome Convenio
              $sqlNomeConvenio = $conexao -> prepare("SELECT Nome FROM convenios where (ID = '$linha[Convenio]')");
              $sqlNomeConvenio -> execute();
              $nomeConvenio = $sqlNomeConvenio -> fetchAll(PDO::FETCH_ASSOC);
              if (count($nomeConvenio) == 1){
                echo $nomeConvenio[0]['Nome'];
              }
            ?>
          </td>
          <td class="d-none d-sm-block"><?php echo $linha['Telefone'] ?></td>
          <td>
            <a onclick="apagar(<?php echo $linha['ID'] ?>)" href="#" class="btn btn-danger mt-1" id="btnExcluir"> Excluir </a>
            <a href="#" class="btn btn-warning mt-1">Editar</a>
          </td>
        </tr>

    <?php }
    } ?>
  </tbody>
</table>

<script>
  function apagar(id) {
    if (confirm("Você relamente deseja excluir este paciente?")) {
      location.href = "<?php echo 'pacientes.php' ?>?idexcluir=" + id;
    }
  }
</script>