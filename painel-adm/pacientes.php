<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
<script src="../js/mascaras.js"></script>

<?php
include_once('../alerts.php');
@session_start();


//O convenio com nome "Particular" é cadastrado automaticamente em AUTENTICAR.PHP

// Buscar por Convenios Cadastrados
$where = "Psicologo = $_SESSION[id_psicologo]";
$listaconvenios = select('convenios', $where);

// Sistema para buscar pacientes
if (isset($_GET['btnBuscarPacientes']) and $_GET['txtBuscarPacientes'] != "") {
  $txtBuscarPaciente = "%" . $_GET['txtBuscarPacientes'] . "%";
  $where = "Nome like '$txtBuscarPaciente' order by Nome asc";
  $listapacientes = select('paciente', $where);
} else {
  $where = "Psicologo = $_SESSION[id_psicologo] order by Nome asc";
  $listapacientes = select('paciente', $where);
}
?>

<?php
//INSERIR NOVO PACIENTE
if (isset($_POST['btnNovoPaciente'])) {
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

  try {
    $valores = array(
      'Nome' => $_POST['Nome'],
      'Telefone' => $_POST['Telefone'],
      'Email' => $_POST['Email'],
      'Data_Nascimento' => $_POST['Nascimento'],
      'Convenio' => $_POST['Convenio'],
      'CPF' => $_POST['CPF'],
      'Genero' => $_POST['Genero'],
      'Prontuario' => $_POST['Prontuario'],
      'Endereco' => $_POST['Endereco'],
      'Psicologo' => $_SESSION['id_psicologo'],
      'Foto' => $enderecoFoto
    );
    $pacienteId = insert('paciente', $valores);

    if (!empty($_FILES['Anexos']['name'])) {
      $anexos = $_FILES['Anexos'];

      foreach ($anexos['tmp_name'] as $index => $tmp) {
        $anexoNome = $anexos['name'][$index];
        $anexoErro = $anexos['error'][$index];

        // Verificar se não houve erros no upload do anexo
        if ($anexoErro === UPLOAD_ERR_OK) {
          // Definir o diretório de destino para salvar o anexo
          $diretorioDestino = './anexosPacientes/';

          // Gerar um nome único para o anexo (pode ser o ID do paciente, por exemplo)
          $nomeAnexo = $anexoNome . " - " . $pacienteId . '.' . pathinfo($anexoNome, PATHINFO_EXTENSION);

          // Mover o arquivo temporário para o diretório de destino com o nome único
          if (move_uploaded_file($tmp, $diretorioDestino . $nomeAnexo)) {
            // Endereço do anexo para armazenar no banco de dados
            $enderecoAnexo = $diretorioDestino . $nomeAnexo;

            // Inserir o anexo no banco de dados (dentro do loop)
            $dados = array(
              'Nome' => $nomeAnexo,
              'Anexo' => $enderecoAnexo,
              'Paciente' => $pacienteId
            );
            insert('anexos', $dados);
          }
        }
      }
    }
    echo "<script language='javascript'> window.location='index.php?acao=$item2&alert=success'; </script>";
  } catch (Exception $e) {
    echo $e;
  }
}

// EDITAR PACIENTE
if (isset($_POST['btnEditarPaciente'])) {
  $idEditarPaciente = $_GET['id'];

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
    $where = "ID = $idEditarPaciente";
    $resultadoFotoAntiga = select('paciente', $where);

    if (($resultadoFotoAntiga[0]["Foto"]) <> null) {
      unlink($resultadoFotoAntiga[0]["Foto"]);
    }
    ;

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
    $where = "ID = $idEditarPaciente";
    $resultadoFotoAntiga = select('paciente', $where);

    if (($resultadoFotoAntiga[0]["Foto"]) <> null) {
      unlink($resultadoFotoAntiga[0]["Foto"]);
    }
    ;
    $enderecoFoto = null;
  }

  if (!empty($_FILES['Anexos']['name'])) {
    $anexos = $_FILES['Anexos'];
    $pacienteId = $idEditarPaciente;
    foreach ($anexos['tmp_name'] as $index => $tmp) {
      $anexoNome = $anexos['name'][$index];
      $anexoErro = $anexos['error'][$index];

      // Verificar se não houve erros no upload do anexo
      if ($anexoErro === UPLOAD_ERR_OK) {
        // Definir o diretório de destino para salvar o anexo
        $diretorioDestino = './anexosPacientes/';

        // Gerar um nome único para o anexo (pode ser o ID do paciente, por exemplo)
        $nomeAnexo = $anexoNome . " - " . $pacienteId . '.' . pathinfo($anexoNome, PATHINFO_EXTENSION);

        // Mover o arquivo temporário para o diretório de destino com o nome único
        if (move_uploaded_file($tmp, $diretorioDestino . $nomeAnexo)) {
          // Endereço do anexo para armazenar no banco de dados
          $enderecoAnexo = $diretorioDestino . $nomeAnexo;

          // Inserir o anexo no banco de dados (dentro do loop)
          $valores = array(
            'Nome' => $nomeAnexo,
            'Anexo' => $enderecoAnexo,
            'Paciente' => $pacienteId
          );
          insert('anexos', $valores);
        }
      }
    }
  }

  // Verificar se há anexos marcados para exclusão
  if (isset($_POST['apagarAnexo'])) {
    $anexosExcluir = $_POST['apagarAnexo'];

    // Percorrer a lista de anexos marcados para exclusão
    foreach ($anexosExcluir as $anexoID) {
      // Consultar o banco de dados para obter o caminho do anexo
      $where = "ID = $anexoID";
      $resultadoAnexo = select('anexos', $where);

      // Verificar se o anexo existe
      if ($resultadoAnexo) {
        // Excluir o anexo do banco de dados
        delete('anexos', "ID = $anexoID");

        // Excluir o arquivo físico do anexo
        unlink($resultadoAnexo[0]['Anexo']);
      }
    }
  }


  try {
    $dados = array(
      'Nome' => $_POST['Nome'],
      'Telefone' => $_POST['Telefone'],
      'Email' => $_POST['Email'],
      'Data_Nascimento' => $_POST['Nascimento'],
      'Genero' => $_POST['Genero'],
      'Convenio' => $_POST['Convenio'],
      'Foto' => $enderecoFoto,
      'CPF' => $_POST['CPF'],
      'Prontuario' => $_POST['Prontuario'],
      'Endereco' => $_POST['Endereco']
    );
    update ('paciente', $dados, $idEditarPaciente);
    
    echo "<script language='javascript'> window.location='index.php?acao=$item2&alert=success'; </script>";
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}



if (@($_GET['funcao']) == "exclusao") {
  $idexclusao = $_GET['id'];
  // Procura atendimentos futuros do paciente
  $dataHoje = date_create()->format("Y-m-d H:i:s");
  $where = "Paciente = $idexclusao AND Data_Fim > '$dataHoje'";
  $BuscarAgendamentos = select('agendar', $where);

  print_r($BuscarAgendamentos);
  if (count($BuscarAgendamentos) >= 1) {
    echo "<script language='javascript'> window.location='index.php?acao=$item2&alert=danger'; </script>";
  } elseif (count($BuscarAgendamentos) < 1) {
    // Apaga atendimentos passados
    delete('agendar', "Paciente = $idexclusao");
    delete('atendimento', "Paciente = $idexclusao");

    // Busca a foto do paciente para apagar
    $fotoPaciente = select('paciente', "ID = $idexclusao");

    if (isset($fotoPaciente[0]['Foto'])) {
      unlink($fotoPaciente[0]['Foto']);
    }

    //Busca anexos do paciente para apagar
    $anexosPaciente = select('anexos', "Paciente = $idexclusao");

    if (!empty($anexosPaciente)) {
      foreach ($anexosPaciente as $indice => $linha) {
        unlink($linha['Anexo']);
      }
      delete('anexos', "Paciente = $idexclusao");
    }
    //Apaga Paciente
    delete('paciente', "ID = $idexclusao");
    echo "<script language='javascript'> window.location='index.php?acao=$item2&alert=success'; </script>";
  }
}
?>

<?php
// ABRIR MODAL
if (@($_GET['funcao']) == "editar" or @($_GET['funcao']) == "novo") {
  if (isset($_GET['id'])) {
    $idEditarPaciente = $_GET['id'];
    $where = "ID = $idEditarPaciente";
    $dadosEditarPaciente = select('paciente', $where);

    //Busca anexos do paciente para listar
    $anexosPaciente = select('anexos', "Paciente = $idEditarPaciente");
  }
  ;
  ?>
  <!-- MODAL DE PACIENTE -->
  <div class="modal fade modal-paciente novo-modal" data-backdrop="static" id="modalPaciente" tabindex="-1" role="dialog"
    aria-labelledby="#modalPaciente" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPaciente">CADASTRO DE PACIENTE</h5>
          <div class="col-lg-1 mr-lg-2 d-none d-lg-block"><img class="logo-lateral" src="../img/logosistemapsico.png">
          </div>
          </button>
        </div>
        <div class="modal-body">
          <form id="formModalPaciente" enctype="multipart/form-data" method="POST"
            action="index.php?acao=<?php echo $item2; ?><?php if (isset($idEditarPaciente)) {
                 echo "&id=$idEditarPaciente";
               } ?>">
            <div class="form-row">
              <div class="form-group col-md-10 col-sm-12">
                <label for="Nome">Nome Completo</label>
                <input type="text" class="form-control" id="Nome" name="Nome" placeholder="Nome do Paciente"
                  value="<?php if (isset($dadosEditarPaciente[0]["Nome"])) {
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
                } ?>"
                  required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6 col-sm-12">
                <label for="Telefone">Telefone</label>
                <input type="text" class="form-control" id="Telefone" name="Telefone" placeholder="Telefone do Paciente"
                  value="<?php if (isset($dadosEditarPaciente[0]["Telefone"])) {
                    echo $dadosEditarPaciente[0]["Telefone"];
                  } ?>"
                  required>
              </div>

              <div class="form-group col-md-6 col-sm-12">
                <label for="Email">E-mail</label>
                <input type="email" class="form-control" id="Email" name="Email" placeholder="E-mail do Paciente"
                  value="<?php if (isset($dadosEditarPaciente[0]["Email"])) {
                    echo $dadosEditarPaciente[0]["Email"];
                  } ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-12 col-sm-12">
                <label for="Endereco">Endereço</label>
                <input id="Endereco" name="Endereco" type="text" placeholder="Endereço do Paciente" class="form-control"
                  value="<?php if (isset($dadosEditarPaciente[0]["Endereco"])) {
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
                  <img src="<?php echo $dadosEditarPaciente[0]["Foto"] ?>" alt="Foto Atual" width="200em"
                    class="foto-paciente mt-2 ml-2 img-thumbnail">
                  <br> <input class="" type="checkbox" id="apagarFoto" name="apagarFoto"> <label for="apagarFoto">Deletar
                    foto</label>
                  <?php
                }
                ?>
              </div>
              <div class="form-group col-md-8 col-sm-12">
                <label for="Prontuario">Prontuário</label>
                <textarea id="Prontuario" class="form-control" rows="7" name="Prontuario"
                  placeholder="Prontuário do Paciente"><?php if (isset($dadosEditarPaciente[0]["Prontuario"])) {
                    echo $dadosEditarPaciente[0]["Prontuario"];
                  } ?></textarea>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-12 col-sm-12">
                <label for="Anexos">Anexos</label>
                <input type="file" id="Anexos" multiple="multiple" class="form-control" name="Anexos[]">
                <?php
                if (!empty($anexosPaciente)) { ?>
                  <table class="table table-estriped mt-2">
                    <tr>
                      <th scope="col">Nome do anexo</th>
                      <th scope="col" colspan="3">Ação</th>
                    </tr>
                    <?php foreach ($anexosPaciente as $indice => $linha) { ?>
                      <tr>
                        <input type="hidden" name="anexosExistentes[]" value="<?php echo $linha['Anexo']; ?>">
                        <td scope="row">
                          <?php echo ($linha['Nome']); ?>
                        </td>
                        <td scope="row">
                          <?php $caminhoArquivo = $linha['Anexo']; ?> <a href="<?php echo $caminhoArquivo; ?>"
                            target="_blank">Abrir</a>
                        </td>
                        <td scope="row"> <a href="<?php echo $caminhoArquivo; ?>" download>Download</a> </td>
                        <td scope="row"> <input class="" type="checkbox" id="apagarAnexo<?php echo $linha['ID']; ?>"
                            name="apagarAnexo[]" value="<?php echo $linha['ID']; ?>"> <label
                            for="apagarAnexo<?php echo $linha['ID']; ?>">Deletar arquivo</label> </td>
                      </tr>
                    <?php }
                    ?>
                  </table>
                <?php } ?>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <?php if (isset($idEditarPaciente)) { ?>
            <div class="text-left mr-auto">
              <a form="formModalPaciente" class="btn btn-warning text-white"
                href="export/paciente.php?id=<?php echo $idEditarPaciente; ?>" target="_blank">Imprimir Ficha do
                Paciente</a>
              <a class="btn btn-warning text-white" href="export/relatPaciente.php?id=<?php echo $idEditarPaciente; ?>"
                target="_blank">Imprimir Todos os Relatórios de Atendimento</a>
            </div>
          <?php } ?>
          <button form="formModalPaciente" type="submit" class="btn btn-success"
            name="<?php echo ($_GET['funcao'] == 'editar') ? 'btnEditarPaciente' : 'btnNovoPaciente'; ?>"><?php echo ($_GET['funcao'] == 'editar') ? 'Editar' : 'Cadastrar'; ?></button>
          <button form="formModalPaciente" type="reset" data-dismiss="modal" class="btn btn-danger"
            name="<?php echo $item2 ?>">Cancelar</button>
        </div>

      </div>
    </div>
  </div>

  <?php
  if (@($_GET['funcao']) == "editar") {
    echo '<script> $("#modalPaciente").modal("show"); </script>';
  } elseif (@($_GET['funcao']) == "novo") {
    echo '<script> $("#modalPaciente").modal("show"); </script>';
  }
  ;
?>
<?php } ?>

<?php

// MODAL EXCLUIR PACIENTE
if (@($_GET['funcao']) == "excluir") {
  $idexclusao = $_GET['id']; ?>
  <div class="modal fade" id="ConfirmExclusaoPaciente" tabindex="-1" role="dialog"
    aria-labelledby="#ConfirmExclusaoPaciente" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ConfirmExclusaoPaciente">Excluir paciente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Você deseja realmente excluir este paciente? <br>
          Você irá excluir todos os agendamentos passados deste paciente e todos os registros de atendimento deste
          paciente.
        </div>
        <div class="modal-footer">
          <a href="index.php?acao=<?php echo $item2; ?>" type="button" class="btn btn-dark">Cancelar</a>
          <a class="btn btn-danger"
            href="index.php?acao=<?php echo $item2; ?>&funcao=exclusao&id=<?php echo $idexclusao; ?>">Excluir</a>
        </div>
      </div>
    </div>
  </div>
  <script>
    $("#ConfirmExclusaoPaciente").modal("show")
  </script>
  <?php
}
?>


<!-- BOTÃO DE NOVO PACIENTE E BOTÃO DE PESQUISA -->
<div class="row mt-1"> <!-- botão alinhado a borda da tabela -->

  <div class="col-md-6 col-sm-12">
    <a href="index.php?acao=<?php echo $item2; ?>&funcao=novo" class="btn btn-secondary">
      <span style="font-size: 16pt;">+</span> Novo paciente
    </a>
  </div>

  <!-- Form para envio dos dados para pesquisa -->
  <div class="col-md-6 col-sm-12">
    <div class="float-right">
      <form class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="search" placeholder="Buscar paciente" aria-label="Search"
          name="txtBuscarPacientes"
          value="<?php if (isset($_GET['btnBuscarPacientes']) and $_GET['txtBuscarPacientes'] != "")
            echo $_GET['txtBuscarPacientes']; //manter o nome pesquisado no input   
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
    if (($listapacientes == null)) {
      $nenhumPaciente = "Nenhum paciente encontrado.";
    } else {
      foreach ($listapacientes as $indice => $linha) {
        $nenhumPaciente = null;
        if ($linha['Psicologo'] == $_SESSION['id_psicologo']) {
          ?>
          <tr>
            <th scope="row">
              <?php echo $linha['ID'] ?>
            </th>
            <td>
              <?php echo $linha['Nome'] ?>
            </td>
            <td>
              <?php
              // Converter o ID convenio para Nome Convenio
              $nomeConvenio = select('convenios', "ID = $linha[Convenio]");
              /*
              $sqlNomeConvenio = $conexao->prepare("SELECT Nome FROM convenios where (ID = '$linha[Convenio]')");
              $sqlNomeConvenio->execute();
              $nomeConvenio = $sqlNomeConvenio->fetchAll(PDO::FETCH_ASSOC);
              */
              if (count($nomeConvenio) == 1) {
                echo $nomeConvenio[0]['Nome'];
              }
              ?>
            </td>
            <td class="d-none d-sm-block">
              <?php echo $linha['Telefone'] ?>
            </td>
            <td>
              <a href="index.php?acao=<?php echo $item2; ?>&funcao=excluir&id=<?php echo $linha['ID']; ?>"
                class="btn btn-danger mt-1" id="btnExcluir"> Excluir </a>
              <a href="index.php?acao=<?php echo $item2; ?>&funcao=editar&id=<?php echo $linha['ID']; ?>"
                class="btn btn-warning mt-1">Editar</a>
            </td>
          </tr>

        <?php }
      }
    } ?>
  </tbody>
</table>

<?php echo $nenhumPaciente; ?>