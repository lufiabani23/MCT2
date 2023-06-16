<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
<script src="../js/mascaras.js"></script>

<?php
require_once('../conexao.php');
include_once('../alerts.php');
@session_start();


//O convenio com nome "Particular" é cadastrado automaticamente em AUTENTICAR.PHP

// Buscar por Convenios Cadastrados
$sql = $conexao->prepare("SELECT * FROM convenios where (Psicologo = $_SESSION[id_psicologo])");
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
?>

<?php
//INSERIR NOVO PACIENTE
if (isset($_POST['btnNovoPaciente'])) {

  $nome = $_POST['Nome'];
  $telefone = $_POST['Telefone'];
  $email = $_POST['Email'];
  $nascimento = $_POST['Nascimento'];
  $convenio = $_POST['Convenio'];
  $CPF = $_POST['CPF'];
  $genero = $_POST['Genero'];
  $prontuario = $_POST['Prontuario'];
  $endereco = $_POST['Endereco'];

  if (!empty($_FILES['Foto']['name'])) {
    // Obter informações sobre o arquivo de foto
    $fotoNome = $_FILES['Foto']['name'];
    $fotoTmp = $_FILES['Foto']['tmp_name'];
    $fotoTamanho = $_FILES['Foto']['size'];
    $fotoErro = $_FILES['Foto']['error'];

    // Verificar se não houve erros no upload da foto
    if ($fotoErro === UPLOAD_ERR_OK) {
      // Definir o diretório de destino para salvar a foto
      $diretorioDestino = './fotosPacientes/';

      // Gerar um nome único para a foto (pode ser o ID do paciente, por exemplo)
      $nomeFoto = uniqid('paciente_') . '.' . pathinfo($fotoNome, PATHINFO_EXTENSION);

      // Mover o arquivo temporário para o diretório de destino com o nome único
      if (move_uploaded_file($fotoTmp, $diretorioDestino . $nomeFoto)) {
        // Endereço da foto para armazenar no banco de dados
        $enderecoFoto = $diretorioDestino . $nomeFoto;
      }
    }
  } else {
    $enderecoFoto = null;
  }

  if (empty($nome) or empty($telefone) or empty($nascimento) or empty($convenio) or empty($CPF)) {
    echo "<script language='javascript'> window.alert('Campo obrigatório em branco'); </script>";
    echo "<script language='javascript'> window.location='index.php?acao=$item2'; </script>";
  } else {
    try {
      $sql = $conexao->prepare("INSERT INTO paciente VALUES (null,?,?,?,?,?,?,?,?,?,?,null,?)");
      $sql->execute(array(
        $nome, $telefone, $email, $nascimento, $convenio, $enderecoFoto, $genero, $CPF, $_SESSION['id_psicologo'], $prontuario, $endereco
      ));
      if (!empty($_FILES['Anexos']['name'])) {
        $anexos = $_FILES['Anexos'];
        $pacienteId = $conexao->lastInsertId();
        foreach ($anexos['tmp_name'] as $index => $tmp) {
          $anexoNome = $anexos['name'][$index];
          $anexoErro = $anexos['error'][$index];

          // Verificar se não houve erros no upload do anexo
          if ($anexoErro === UPLOAD_ERR_OK) {
            // Definir o diretório de destino para salvar o anexo
            $diretorioDestino = './anexosPacientes/';

            // Gerar um nome único para o anexo (pode ser o ID do paciente, por exemplo)
            $nomeAnexo =  $anexoNome . " - " . $pacienteId . '.' . pathinfo($anexoNome, PATHINFO_EXTENSION);

            // Mover o arquivo temporário para o diretório de destino com o nome único
            if (move_uploaded_file($tmp, $diretorioDestino . $nomeAnexo)) {
              // Endereço do anexo para armazenar no banco de dados
              $enderecoAnexo = $diretorioDestino . $nomeAnexo;

              // Inserir o anexo no banco de dados (dentro do loop)
              $sqlAnexo = $conexao->prepare("INSERT INTO anexos (Nome, Anexo, Paciente) VALUES (?, ?, ?)");
              $sqlAnexo->execute(array($nomeAnexo, $enderecoAnexo, $pacienteId));
            }
          }
        }
      }
      echo "<script language='javascript'> window.location='index.php?acao=pacientes&alert=success'; </script>";
    } catch (Exception $e) {
      echo $e;
    }
  }
}

// EDITAR PACIENTE
if (isset($_POST['btnEditarPaciente'])) {
  $idEditarPaciente = $_GET['id'];
  $nome = $_POST['Nome'];
  $telefone = $_POST['Telefone'];
  $email = $_POST['Email'];
  $nascimento = $_POST['Nascimento'];
  $genero = $_POST['Genero'];
  $convenio = $_POST['Convenio'];
  $CPF = $_POST['CPF'];
  $prontuario = $_POST['Prontuario'];
  $endereco = $_POST['Endereco'];
  if (isset($_POST['enderecoFoto'])) {
    $enderecoFoto = $_POST['enderecoFoto'];
  } else {
    $enderecoFoto = null;
  } // Verifica se tem uma foto antiga no hidden
  if (isset($_POST['apagarFoto'])) {
    $apagarFoto = 1;
  } else {
    $apagarFoto = 0;
  } // Verifica se foi marcada a opção Apagar Foto

  // Verifica se foi anexado algum arquivo no campo foto
  if (!empty($_FILES['Foto']['name'])) {
    $sqlFotoAntiga = $conexao->prepare("SELECT Foto FROM paciente WHERE ID = :idEditarPaciente");
    $sqlFotoAntiga->bindParam(':idEditarPaciente', $idEditarPaciente);
    $sqlFotoAntiga->execute();
    $resultadoFotoAntiga = $sqlFotoAntiga->fetch(PDO::FETCH_ASSOC);
    if (($resultadoFotoAntiga["Foto"]) <> null) {
      unlink($resultadoFotoAntiga["Foto"]);
    };

    // Obter informações sobre o arquivo de foto
    $fotoNome = $_FILES['Foto']['name'];
    $fotoTmp = $_FILES['Foto']['tmp_name'];
    $fotoTamanho = $_FILES['Foto']['size'];
    $fotoErro = $_FILES['Foto']['error'];

    // Verificar se não houve erros no upload da foto
    if ($fotoErro === UPLOAD_ERR_OK) {
      // Definir o diretório de destino para salvar a foto
      $diretorioDestino = 'fotosPacientes/';

      // Gerar um nome único para a foto (pode ser o ID do paciente, por exemplo)
      $nomeFoto = uniqid('paciente_') . '.' . pathinfo($fotoNome, PATHINFO_EXTENSION);

      // Mover o arquivo temporário para o diretório de destino com o nome único
      if (move_uploaded_file($fotoTmp, $diretorioDestino . $nomeFoto)) {
        // Endereço da foto para armazenar no banco de dados
        $enderecoFoto = $diretorioDestino . $nomeFoto;
      }
    }
  } elseif ($apagarFoto == 1) {
    $sqlFotoAntiga = $conexao->prepare("SELECT Foto FROM paciente WHERE ID = :idEditarPaciente");
    $sqlFotoAntiga->bindParam(':idEditarPaciente', $idEditarPaciente);
    $sqlFotoAntiga->execute();
    $resultadoFotoAntiga = $sqlFotoAntiga->fetch(PDO::FETCH_ASSOC);
    if (($resultadoFotoAntiga["Foto"]) <> null) {
      unlink($resultadoFotoAntiga["Foto"]);
    };
    $enderecoFoto = null;
  }

  try {
    $sqlEditarPaciente = $conexao->prepare("UPDATE paciente SET
    Nome = :nome,
    Telefone = :telefone,
    Email = :email,
    Data_Nascimento = :nascimento,
    Genero = :genero,
    Convenio = :convenio,
    Foto = :foto,
    CPF = :cpf,
    Prontuario = :prontuario,
    Endereco = :endereco
    WHERE ID = :idEditarPaciente");

    $sqlEditarPaciente->execute(
      array(
        ':nome' => $nome,
        ':telefone' => $telefone,
        ':email' => $email,
        ':nascimento' => $nascimento,
        ':genero' => $genero,
        ':convenio' => $convenio,
        ':foto' => $enderecoFoto,
        ':cpf' => $CPF,
        ':prontuario' => $prontuario,
        ':endereco' => $endereco,
        ':idEditarPaciente' => $idEditarPaciente
      )
    );
    echo "<script language='javascript'> window.location='index.php?acao=pacientes&alert=success'; </script>";
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}
?>

<?php
// ABRIR MODAL
if (@($_GET['funcao']) == "editar" or @($_GET['funcao']) == "novo") {
  if (isset($_GET['id'])) {
    $idEditarPaciente = $_GET['id'];
    $sqlEditarPaciente =  $conexao->query("SELECT * from Paciente where(ID = $idEditarPaciente)");
    $dadosEditarPaciente = $sqlEditarPaciente->fetchAll(PDO::FETCH_ASSOC);
    //Busca anexos do paciente para listar
    $sqlBuscarAnexos = $conexao->prepare("SELECT * FROM Anexos where Paciente = :id");
    $sqlBuscarAnexos->bindParam(":id", $idEditarPaciente);
    $sqlBuscarAnexos->execute();
    $anexosPaciente = $sqlBuscarAnexos->fetchAll(PDO::FETCH_ASSOC);
  };
?>
  <!-- MODAL DE PACIENTE -->
  <div class="modal fade modal-paciente novo-modal" id="modalPaciente" tabindex="-1" role="dialog" aria-labelledby="#modalPaciente" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPaciente">CADASTRO DE PACIENTE</h5>
          <div class="col-lg-1 mr-lg-2 d-none d-lg-block"><img class="logo-lateral" src="../img/logosistemapsico.png"></div>
          </button>
        </div>
        <div class="modal-body">
          <form id="formModalPaciente" enctype="multipart/form-data" method="POST" action="index.php?acao=pacientes<?php if (isset($idEditarPaciente)) {
                                                                                                                      echo "&id=$idEditarPaciente";
                                                                                                                    } ?>">
            <div class="form-row">
              <div class="form-group col-md-10 col-sm-12">
                <label for="Nome">Nome Completo</label>
                <input type="text" class="form-control" id="Nome" name="Nome" placeholder="Nome do Paciente" value="<?php if (isset($dadosEditarPaciente[0]["Nome"])) {
                                                                                                                      echo $dadosEditarPaciente[0]["Nome"];
                                                                                                                    } ?>" required>
              </div>

              <div class="form-group col-md-2 col-sm-12">
                <label for="Convenio">Convênio</label>
                <select id="Convenio" name="Convenio" class="form-control" required>
                  <?php
                  foreach ($listaconvenios as $indice => $linha) {
                    $convenio = $linha['Nome'];
                    $convenioId = $linha['ID'];
                    if (isset($dadosEditarPaciente[0]["Convenio"]) && $dadosEditarPaciente[0]["Convenio"] == $convenioId) {
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
                <input type="date" class="form-control" id="Nascimento" name="Nascimento" value="<?php if (isset($dadosEditarPaciente[0]["Data_Nascimento"])) {
                                                                                                    echo $dadosEditarPaciente[0]["Data_Nascimento"];
                                                                                                  } ?>" required>
              </div>

              <div class="form-group col-md-4 col-sm-12">
                <label for="Genero">Gênero</label>
                <select id="Genero" name="Genero" class="form-control" required>
                  <option value="Masculino" <?php if (isset($dadosEditarPaciente[0]["Genero"]) && $dadosEditarPaciente[0]["Genero"] == "Masculino") {
                                              echo 'selected';
                                            } ?>>Masculino</option>
                  <option value="Feminino" <?php if (isset($dadosEditarPaciente[0]["Genero"]) && $dadosEditarPaciente[0]["Genero"] == "Feminino") {
                                              echo 'selected';
                                            } ?>>Feminino</option>
                  <option value="Outro" <?php if (isset($dadosEditarPaciente[0]["Genero"]) && $dadosEditarPaciente[0]["Genero"] == "Outro") {
                                          echo 'selected';
                                        } ?>>Outro</option>
                </select>
              </div>

              <div class="form-group col-md-4 col-sm-12">
                <label for="CPF">CPF</label>
                <input type="text" class="form-control" id="CPF" name="CPF" placeholder="CPF do Paciente" value="<?php if (isset($dadosEditarPaciente[0]["CPF"])) {
                                                                                                                    echo $dadosEditarPaciente[0]["CPF"];
                                                                                                                  } ?>" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6 col-sm-12">
                <label for="Telefone">Telefone</label>
                <input type="text" class="form-control" id="Telefone" name="Telefone" placeholder="Telefone do Paciente" value="<?php if (isset($dadosEditarPaciente[0]["Telefone"])) {
                                                                                                                                  echo $dadosEditarPaciente[0]["Telefone"];
                                                                                                                                } ?>" required>
              </div>

              <div class="form-group col-md-6 col-sm-12">
                <label for="Email">E-mail</label>
                <input type="email" class="form-control" id="Email" name="Email" placeholder="E-mail do Paciente" value="<?php if (isset($dadosEditarPaciente[0]["Email"])) {
                                                                                                                            echo $dadosEditarPaciente[0]["Email"];
                                                                                                                          } ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-12 col-sm-12">
                <label for="Endereco">Endereço</label>
                <input id="Endereco" name="Endereco" type="text" placeholder="Endereço do Paciente" class="form-control" value="<?php if (isset($dadosEditarPaciente[0]["Endereco"])) {
                                                                                                                                  echo $dadosEditarPaciente[0]["Endereco"];
                                                                                                                                } ?>">
              </div>

            </div>
            <div class="form-row">
              <div class="form-group col-md-4 col-sm-12">
                <label for="Foto">Foto <sub>(png, jpeg, jpg)</sub></label>
                <input type="file" id="Foto" class="form-control" name="Foto" enctype="multipart/form-data">
                <?php
                if (isset($dadosEditarPaciente[0]["Foto"])) { ?>
                  <input type="hidden" value="<?php echo $dadosEditarPaciente[0]["Foto"]; ?>" name="enderecoFoto">
                  <label>Foto Atual:</label>
                  <img src="<?php echo $dadosEditarPaciente[0]["Foto"] ?>" alt="Foto Atual" width="200em" class="foto-paciente mt-2 ml-2 img-thumbnail">
                  <br> <input class="" type="checkbox" id="apagarFoto" name="apagarFoto"> <label for="apagarFoto">Deletar foto</label> <?php
                                                                                                                                      }
                                                                                                                                        ?>
              </div>
              <div class="form-group col-md-8 col-sm-12">
                <label for="Prontuario">Prontuário</label>
                <textarea id="Prontuario" class="form-control" rows="7" name="Prontuario" placeholder="Prontuário do Paciente" value="<?php if (isset($dadosEditarPaciente[0]["Prontuario"])) {
                                                                                                                                        echo $dadosEditarPaciente[0]["Prontuario"];
                                                                                                                                      } ?>"></textarea>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-12 col-sm-12">
                <label for="Anexos">Anexos</label>
                <input type="file" id="Anexos" multiple="multiple" class="form-control" name="Anexos[]">
                <?php
                if (!empty($anexosPaciente)) { ?>
                  <table class="table table-estriped">
                    <tr>
                      <th scope="col">Nome</th>
                      <th scope="col">Ação</th>
                    </tr>
                    <?php foreach ($anexosPaciente as $indice => $linha) { ?>
                      <tr>
                        <td scope="row"><?php echo ($linha['Nome']); ?> </td>
                        <td scope="row"><input class="" type="checkbox" id="apagarAnexo<?php echo $linha['ID'] ?>" name="apagarAnexo<?php echo $linha['ID'] ?>"> <label for="apagarAnexo<?php echo $linha['ID'] ?>">Deletar foto</label></td>
                      </tr>
                    <?php }
                    ?>
                  </table>
                  <input type="hidden" value="<?php echo $dadosEditarPaciente[0]["Foto"]; ?>" name="enderecoFoto">
                <?php } ?>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="formModalPaciente" type="submit" class="btn btn-success" name="<?php echo ($_GET['funcao'] == 'editar') ? 'btnEditarPaciente' : 'btnNovoPaciente'; ?>"><?php echo ($_GET['funcao'] == 'editar') ? 'Editar' : 'Cadastrar'; ?></button>
          <button form="forModalPaciente" type="reset" data-dismiss="modal" class="btn btn-danger" name="<?php echo $item2 ?>">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  <?php
  if (@($_GET['funcao']) == "editar") {
    echo '<script> $("#modalPaciente").modal("show"); </script>';
  } elseif (@($_GET['funcao']) == "novo") {
    echo '<script> $("#modalPaciente").modal("show"); </script>';
  };
  ?>
<?php } ?>

<?php

// MODAL EXCLUIR PACIENTE
if (@($_GET['funcao']) == "excluir") {
  $idexclusao = $_GET['id']; ?>
  <div class="modal fade" id="ConfirmExclusaoPaciente" tabindex="-1" role="dialog" aria-labelledby="#ConfirmExclusaoPaciente" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ConfirmExclusaoPaciente">Excluir paciente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Você deseja realmente excluir este paciente?
        </div>
        <div class="modal-footer">
          <a href="index.php?acao=pacientes" type="button" class="btn btn-dark">Cancelar</a>
          <a class="btn btn-danger" href="index.php?acao=pacientes&funcao=exclusao&id=<?php echo $idexclusao; ?>">Excluir</a>
        </div>
      </div>
    </div>
  </div>
  <script>
    $("#ConfirmExclusaoPaciente").modal("show")
  </script>
<?php
}

// EXCLUSAO PACIENTE
if (@($_GET['funcao']) == "exclusao") {
  $idexclusao = $_GET['id'];
  // Procura atendimentos futuros do paciente
  $dataHoje = date_create()->format("Y-m-d H:i:s");
  $sqlBuscarAtendimentos = $conexao->prepare("SELECT * FROM agendar WHERE paciente = :id AND Data_Fim > :dataHoje");
  $sqlBuscarAtendimentos->bindParam(":id", $idexclusao);
  $sqlBuscarAtendimentos->bindParam(":dataHoje", $dataHoje);
  $sqlBuscarAtendimentos->execute();
  $BuscarAtendimentos = $sqlBuscarAtendimentos->fetchAll(PDO::FETCH_ASSOC);
  if (count($BuscarAtendimentos) >= 1) {
    echo "<script language='javascript'> window.location='index.php?acao=$item2&alert=danger'; </script>";
  } elseif (count($BuscarAtendimentos) < 1) {
    // Apaga atendimentos passados
    $sqlApagarAtendimentos = $conexao->prepare("DELETE FROM agendar WHERE paciente = :id");
    $sqlApagarAtendimentos->bindParam(":id", $idexclusao);
    $sqlApagarAtendimentos->execute();
  }

  // Busca a foto do paciente para apagar
  $sqlBuscarFoto = $conexao->prepare("SELECT Foto FROM Paciente where ID = :id");
  $sqlBuscarFoto->bindParam(":id", $idexclusao);
  $sqlBuscarFoto->execute();
  $fotoPaciente = $sqlBuscarFoto->fetchAll(PDO::FETCH_ASSOC);
  if (isset($fotoPaciente[0]['Foto'])) {
    unlink($fotoPaciente[0]['Foto']);
  }

  //Busca anexos do paciente para apagar
  $sqlBuscarAnexos = $conexao->prepare("SELECT * FROM Anexos where Paciente = :id");
  $sqlBuscarAnexos->bindParam(":id", $idexclusao);
  $sqlBuscarAnexos->execute();
  $anexosPaciente = $sqlBuscarAnexos->fetchAll(PDO::FETCH_ASSOC);
  if (!empty($anexosPaciente)) {
    foreach ($anexosPaciente as $indice => $linha) {
      unlink($linha['Anexo']);
    }
    $sqlApagarAnexo = $conexao->prepare("DELETE FROM Anexos where Paciente = :id");
    $sqlApagarAnexo->bindParam(":id", $idexclusao);
    $sqlApagarAnexo->execute();
  }
  //Apaga Paciente
  $sql = $conexao->prepare("DELETE FROM paciente WHERE id = :id");
  $sql->bindParam(":id", $idexclusao);
  $sql->execute();
  echo "<script language='javascript'> window.location='index.php?acao=$item2&alert=success'; </script>";
}
?>


<!-- BOTÃO DE NOVO PACIENTE E BOTÃO DE PESQUISA -->
<div class="row mt-1"> <!-- botão alinhado a borda da tabela -->

  <div class="col-md-6 col-sm-12">
    <a href="index.php?acao=pacientes&funcao=novo" class="btn btn-secondary">
      <span style="font-size: 16pt;">+</span> Novo paciente
    </a>
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
            $sqlNomeConvenio = $conexao->prepare("SELECT Nome FROM convenios where (ID = '$linha[Convenio]')");
            $sqlNomeConvenio->execute();
            $nomeConvenio = $sqlNomeConvenio->fetchAll(PDO::FETCH_ASSOC);
            if (count($nomeConvenio) == 1) {
              echo $nomeConvenio[0]['Nome'];
            }
            ?>
          </td>
          <td class="d-none d-sm-block"><?php echo $linha['Telefone'] ?></td>
          <td>
            <a href="index.php?acao=pacientes&funcao=excluir&id=<?php echo $linha['ID']; ?>" class="btn btn-danger mt-1" id="btnExcluir"> Excluir </a>
            <a href="index.php?acao=pacientes&funcao=editar&id=<?php echo $linha['ID']; ?>" class="btn btn-warning mt-1">Editar</a>
          </td>
        </tr>

    <?php }
    } ?>
  </tbody>
</table>