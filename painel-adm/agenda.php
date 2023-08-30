<?PHP
include_once('../alerts.php');

/*Este código inclui o calendário de outro arquivo.
Ele faz a inserção, edição e exclusão de agendaments.
*/

// Sistema para adicionar um novo agendamento
if (isset($_POST['btnNovoAgendamento'])) {
  $idPaciente = select('paciente', "Nome = '$_POST[NomePaciente]'");

  //Formatar a data e hora dos agendamentos - a duração é padrão de 45 minutos
  $dataDia = $_POST['DataAgendamento'];
  $horaAgendamento = $_POST['HoraAgendamento'];
  $minutoAgendamento = $_POST['MinutoAgendamento'];
  $duracaoAgendamento = '+45 minutes';
  $dataAgendamentoInicio = date('Y-m-d H:i:s', strtotime($dataDia . ' ' . $horaAgendamento . ':' . $minutoAgendamento));
  $dataAgendamentoFim = date('Y-m-d H:i:s', strtotime($dataAgendamentoInicio . $duracaoAgendamento));

  $dados = array(
    'Psicologo' => $_SESSION['id_psicologo'],
    'Paciente' => $idPaciente[0]['ID'],
    'Data_Inicio' => $dataAgendamentoInicio,
    'Data_Fim' => $dataAgendamentoFim,
    'Motivo' => $_POST['MotivoAgendamento'],
    'OBS' => $_POST['OBSAgendamento'],
    'Valor' => $_POST['ValorAgendamento'],
    'Realizado' => '0'
  );

  insert('agendar', $dados);
  echo "<script language='javascript'> window.location='index.php?acao=$item3&alert=success'; </script>";
}


// Processo de edição e exclusão de um agendamento
if (isset($_POST['btnEditarAgendamento'])) {

  $idEvento = $_POST['idEvento'];
  $deletarAgendamento = isset($_POST['DeletarAgendamento']);

  if ($deletarAgendamento == 1) {
    delete('agendar', "ID = $idEvento");
    echo "<script language='javascript'> window.location='index.php?acao=$item3&alert=success'; </script>";
  } else {
    //Processamento da data e hora do agendamento
    $dataDia = $_POST['DataAgendamento'];
    $horaAgendamento = $_POST['HoraAgendamento'];
    $minutoAgendamento = $_POST['MinutoAgendamento'];
    $duracaoAgendamento = '+45 minutes';
    $dataAgendamentoInicio = date('Y-m-d H:i:s', strtotime($dataDia . ' ' . $horaAgendamento . ':' . $minutoAgendamento));
    $dataAgendamentoFim = date('Y-m-d H:i:s', strtotime($dataAgendamentoInicio . $duracaoAgendamento));

    $idPaciente = select('paciente', "Nome = '$_POST[NomePaciente]'");

    $dados = array(
      'Paciente' => $idPaciente[0]['ID'],
      'Data_Inicio' => $dataAgendamentoInicio,
      'Data_Fim' => $dataAgendamentoFim,
      'Motivo' => $_POST['MotivoAgendamento'],
      'OBS' => $_POST['OBSAgendamento'],
      'Valor' => $_POST['ValorAgendamento']
    );
    update('agendar', $dados, "ID = $_POST[idEvento]");
  }
}



?>

<!-- BOTÃO DE NOVO AGENDAMENTO -->
<div class="col-md-6 col-sm-12">
  <button type="button" class="btn btn-secondary novo-agendamento" data-toggle="modal"
    data-target="#botaoNovoAgendamento">
    <span style="font-size: 16pt;">+</span> Novo agendamento
  </button>
</div>

<!-- MODAL DE NOVO AGENDAMENTO -->
<div class="modal fade novo-agendamento novo-modal" data-backdrop="static" id="botaoNovoAgendamento" tabindex="-1"
  role="dialog" aria-labelledby="#modalNovoAgendamento" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNovoAgendamento">NOVO AGENDAMENTO</h5>
        <div class="col-lg-1 mr-lg-2 d-none d-lg-block"><img class="logo-lateral" src="../img/logosistemapsico.png">
        </div>
        </button>
      </div>
      <div class="modal-body">

        <form id="CadastroAgendamento" method="POST" action="index.php?acao=<?php echo $item3 ?>">
          <div class="form-row">
            <div class="form-group col-md-9 col-sm-12">
              <label for="NomePaciente">Paciente</label>
              <select class="form-control" id="NomePaciente" name="NomePaciente">
                <option>--Selecione--</option>
                <?php
                foreach ($listapacientes as $indice => $linha) {
                  echo "<option>" . $linha['Nome'] .
                    "</option>";
                }
                ; ?>
              </select>
            </div>
            <div class="form-group col-md-3 col-sm-12">
              <label for="DataAgendamento">Data</label>
              <input class="form-control" type="date" id="DataAgendamento" name="DataAgendamento"
                placeholder="Data do Agendamento" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-2">
              <label for="HoraAgendamento">Horas</label>
              <select id="HoraAgendamento" name="HoraAgendamento" class="form-control">
                <?php for ($i = 00; $i <= 23; $i++) {
                  echo "<option>$i</option>";
                }
                ; ?>
              </select>
            </div>
            <div class="form-group col-md-2">
              <label for="MinutoAgendamento">Minutos</label>
              <select id="MinutoAgendamento" class="form-control" name="MinutoAgendamento">
                <?php for ($i = 00; $i <= 59; $i += 5) {
                  echo "<option>$i</option>";
                }
                ; ?>
              </select>
            </div>

            <div class="form-group col-md-8">
              <label for="MotivoAgendamento">Motivo</label>
              <textarea class="form-control" name="MotivoAgendamento" id="MotivoAgendamento"
                placeholder="Motivo do Atendimento"></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="ValorAgendamento">Valor</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">R$</div>
                  <input type="number" id="ValorAgendamento" name="ValorAgendamento" class="form-control"
                    placeholder="Valor do Atendimento">
                </div>
              </div>
            </div>
            <div class="form-group col-md-8">
              <label for="OBSAgendamento">OBS.</label>
              <textarea class="form-control" name="OBSAgendamento" id="OBSAgendamento"
                placeholder="Observações"></textarea>
            </div>
          </div>
        </form>

      </div>

      <div class="modal-footer">
        <button form="CadastroAgendamento" type="submit" class="btn btn-success"
          name="btnNovoAgendamento">Agendar</button>
        <button form="CadastroAgendamento" type="reset" data-dismiss="modal" class="btn btn-danger"
          name="<?php echo $item3 ?>">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL DE EDITAR AGENDAMENTO -->
<div class="modal fade novo-agendamento novo-modal" data-backdrop="static" id="botaoEditarAgendamento" tabindex="-1"
  role="dialog" aria-labelledby="#modalEditarAgendamento" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarAgendamento">EDITAR AGENDAMENTO</h5>
        <div class="col-lg-1 mr-lg-2 d-none d-lg-block"><img class="logo-lateral" src="../img/logosistemapsico.png">
        </div>
        </button>
      </div>
      <div class="modal-body">

        <form id="EditarAgendamento" method="POST" action="index.php?acao=<?php echo $item3 ?>">
          <input type="hidden" id="idEvento" name="idEvento">
          <div class="form-row">
            <div class="form-group col-md-9 col-sm-12">
              <label for="NomePaciente">Paciente</label>
              <select class="form-control" id="NomePaciente" name="NomePaciente">
                <?php
                foreach ($listapacientes as $indice => $linha) {
                  echo "<option>" . $linha['Nome'] .
                    "</option>";
                }
                ; ?>
              </select>
            </div>
            <div class="form-group col-md-3 col-sm-12">
              <label for="DataAgendamento">Data</label>
              <input class="form-control" type="date" id="DataAgendamento" name="DataAgendamento"
                placeholder="Data do Agendamento">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-2">
              <label for="HoraAgendamento">Horas</label>
              <select id="HoraAgendamento" name="HoraAgendamento" class="form-control">
                <?php for ($i = 00; $i <= 23; $i++) {
                  echo "<option>$i</option>";
                }
                ; ?>
              </select>
            </div>
            <div class="form-group col-md-2">
              <label for="MinutoAgendamento">Minutos</label>
              <select id="MinutoAgendamento" class="form-control" name="MinutoAgendamento">
                <?php for ($i = 00; $i <= 59; $i += 5) {
                  echo "<option>$i</option>";
                }
                ; ?>
              </select>
            </div>

            <div class="form-group col-md-8">
              <label for="MotivoAgendamento">Motivo</label>
              <textarea class="form-control" name="MotivoAgendamento" id="MotivoAgendamento"
                placeholder="Motivo do Atendimento"></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="ValorAgendamento">Valor</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">R$</div>
                  <input type="number" id="ValorAgendamento" name="ValorAgendamento" class="form-control"
                    placeholder="Valor do Atendimento">
                </div>
              </div>
            </div>
            <div class="form-group col-md-8">
              <label for="OBSAgendamento">OBS.</label>
              <textarea class="form-control" name="OBSAgendamento" id="OBSAgendamento"
                placeholder="Observações"></textarea>
            </div>
          </div>

          <input type="checkbox" name="DeletarAgendamento" id="DeletarAgendamento"> <label
            for="DeletarAgendamento">Deletar agendamento</label>
        </form>

      </div>

      <div class="modal-footer">
        <button form="EditarAgendamento" type="submit" class="btn btn-success"
          name="btnEditarAgendamento">Editar</button>
        <button form="EditarAgendamento" type="reset" data-dismiss="modal" class="btn btn-danger"
          name="<?php echo $item3 ?>">Cancelar</button>
      </div>
    </div>
  </div>
</div>



<!-- FullCalendar -->
<link href='calendar/css/fullcalendar.css' rel='stylesheet' />
<link href='calendar/css/fullcalendar.print.min.css' rel='stylesheet' media='print' />

<div class="calendarioAgenda">
  <?php
  //Importação do calendário externo com os dados.
  date_default_timezone_set('America/Sao_Paulo');
  $db = $conexao;

  // Consulta para buscar os agendamentos cadastrados
  $pacientes = select('paciente' , "Psicologo = $_SESSION[id_psicologo] and Situacao = 1 ");
  $events = array();
  foreach($pacientes as $linha){
    $agendamento = select('agendar', "Psicologo = $_SESSION[id_psicologo] and Paciente = $linha[ID]");
        // Adiciona cada agendamento ao array $events
        foreach ($agendamento as $evento) {
          $events[] = $evento;
      }
  }
  // Sistema de conversão - pega cada paciente de cada agendamento e busca o nome do paciente e armazena numa array na coluna Paciente
  foreach ($events as $i => $linha) {
    $nomePaciente = select('paciente', "ID = $linha[Paciente]", "Nome");
    $events[$i]["Paciente"] = $nomePaciente[0][0];
  }
  ?>

  <!-- Page Content -->
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center">
        <p class="lead"></p>
        <div id="calendar" class="col-centered">
        </div>
      </div>
    </div>
    <!-- /.row -->

    <!-- Valida data dos Modals -->
    <script type="text/javascript">
      function validaForm(erro) {
        if (erro.inicio.value > erro.termino.value) {
          alert('Data de Inicio deve ser menor ou igual a de termino.');
          return false;
        } else if (erro.inicio.value == erro.termino.value) {
          alert('Defina um horario de inicio e termino.(24h)');
          return false;
        }
      }
    </script>


  </div>

  <!-- FullCalendar -->

  <script src='calendar/js/moment.min.js'></script>
  <script src='calendar/js/fullcalendar.min.js'></script>
  <script src='calendar/locale/pt-br.js'></script>
  <?php include('calendar/calendario.php'); ?>
</div>