<?php
include_once('../alerts.php');
// ADICIONAR AGENDAMENTO
if (isset($_POST['btnNovoAgendamento'])) {
  $nomePaciente = $_POST['NomePaciente'];
  $dataDia = $_POST['DataAgendamento'];
  $horaAgendamento = $_POST['HoraAgendamento'];
  $minutoAgendamento = $_POST['MinutoAgendamento'];
  $motivoAgendamento = $_POST['MotivoAgendamento'];
  $obsAgendamento = $_POST['OBSAgendamento'];
  $valorAgendamento = $_POST['ValorAgendamento'];

  $duracaoAgendamento = '+45 minutes';
  $dataAgendamentoInicio = date('Y-m-d H:i:s', strtotime($dataDia . ' ' . $horaAgendamento . ':' . $minutoAgendamento));
  $dataAgendamentoFim = date('Y-m-d H:i:s', strtotime($dataAgendamentoInicio . $duracaoAgendamento));

  $sqlBuscaPaciente = $conexao->prepare("SELECT ID FROM paciente where (Nome = '$nomePaciente')");
  $sqlBuscaPaciente->execute();
  $idPaciente = $sqlBuscaPaciente->fetchAll(PDO::FETCH_ASSOC);

  if (empty($nomePaciente) or empty($dataDia) or empty($horaAgendamento) or empty($minutoAgendamento)) {
    echo "<script language='javascript'> window.alert('Campo obrigatório em branco'); </script>";
    echo "<script language='javascript'> window.location='index.php?acao=$item3&alert=danger'; </script>";
  } else {
    try {
      $sql = $conexao->prepare("INSERT INTO agendar VALUES (?,?,null,?,?,?,?,?)");
      $sql->execute(array(
        $_SESSION['id_psicologo'], $idPaciente[0]['ID'], $dataAgendamentoInicio, $dataAgendamentoFim, $motivoAgendamento, $obsAgendamento, $valorAgendamento
      ));
      echo "<script language='javascript'> window.location='index.php?acao=$item3&alert=success'; </script>";
    } catch (Exception $e) {
    }
  }
}
?>

<!-- BOTÃO DE NOVO AGENDAMENTO -->
<div class="col-md-6 col-sm-12">
  <button type="button" class="btn btn-secondary novo-agendamento" data-toggle="modal" data-target="#botaoNovoAgendamento">
    <span style="font-size: 16pt;">+</span> Novo agendamento
  </button>
</div>

<!-- MODAL DE NOVO AGENDAMENTO -->
<div class="modal fade novo-agendamento novo-modal" id="botaoNovoAgendamento" tabindex="-1" role="dialog" aria-labelledby="#modalNovoAgendamento" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title ml-3" id="modalNovoAgendamento">AGENDAMENTO</h5>
        <div class="col-lg-1 mr-lg-2 d-none d-lg-block"><img class="logo-lateral" src="../img/logosistemapsico.png"></div>
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
                  echo "<option>" . $linha['Nome'];
                  "</option>";
                }; ?>
              </select>
            </div>
            <div class="form-group col-md-3 col-sm-12">
              <label for="DataAgendamento">Data</label>
              <input class="form-control" type="date" id="DataAgendamento" name="DataAgendamento" placeholder="Data do Agendamento">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-2">
              <label for="HoraAgendamento">Horas</label>
              <select id="HoraAgendamento" name="HoraAgendamento" class="form-control">
                <?php for ($i = 00; $i <= 23; $i++) {
                  echo "<option>$i</option>";
                }; ?>
              </select>
            </div>
            <div class="form-group col-md-2">
              <label for="MinutoAgendamento">Minutos</label>
              <select id="MinutoAgendamento" class="form-control" name="MinutoAgendamento">
                <?php for ($i = 00; $i <= 59; $i += 5) {
                  echo "<option>$i</option>";
                }; ?>
              </select>
            </div>

            <div class="form-group col-md-8">
              <label for="MotivoAgendamento">Motivo</label>
              <textarea class="form-control" name="MotivoAgendamento" id="MotivoAgendamento" placeholder="Motivo do Atendimento"></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="ValorAgendamento">Valor</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">R$</div>
                  <input type="number" id="ValorAgendamento" name="ValorAgendamento" class="form-control" placeholder="Valor do Atendimento">
                </div>
              </div>
            </div>
            <div class="form-group col-md-8">
              <label for="OBSAgendamento">OBS.</label>
              <textarea class="form-control" name="OBSAgendamento" id="OBSAgendamento" placeholder="Observações"></textarea>
            </div>
          </div>
        </form>

      </div>

      <div class="modal-footer">
        <button form="CadastroAgendamento" type="submit" class="btn btn-success" name="btnNovoAgendamento">Agendar</button>
        <button form="CadastroAgendamento" type="reset" data-dismiss="modal" class="btn btn-danger" name="<?php echo $item3 ?>">Cancelar</button>
      </div>
    </div>
  </div>
</div>



<!-- FullCalendar -->
<link href='calendar/css/fullcalendar.css' rel='stylesheet' />
<link href='calendar/css/fullcalendar.print.min.css' rel='stylesheet' media='print' />

<div class="calendarioAgenda">
<?php
date_default_timezone_set('America/Sao_Paulo');
$db = $conexao;

// BUSCA OS EVENTOS CADASTRADOS
$sql = "SELECT ID, Paciente, Motivo, Data_Inicio, Data_Fim FROM agendar WHERE Psicologo = {$_SESSION['id_psicologo']}";
$req = $db->prepare($sql);
$req->execute();
$events = $req->fetchAll();

// CONVERTE O ID DO PACIENTE PARA O NOME DELE
foreach ($events as $i => $linha) {
  $sqlPaciente = $db->prepare("SELECT Nome FROM paciente WHERE ID = {$linha['Paciente']}");
  $sqlPaciente->execute();
  $nomePaciente = $sqlPaciente->fetchAll();
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


  <!-- Modal Adicionar Evento -->
  <?php include('calendar/evento/modal/modalAdd.php'); ?>


  <!-- Modal Editar/Mostrar/Deletar Evento -->
  <?php include('calendar/evento/modal/modalEdit.php'); ?>

</div>

<!-- jQuery Version 3.6.0 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Core JavaScript NÃO ABRE O MENU -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">

<!-- FullCalendar -->
<script src='calendar/js/moment.min.js'></script>
<script src='calendar/js/fullcalendar.min.js'></script>
<script src='calendar/locale/pt-br.js'></script>
<?php include_once('calendar/calendario.php'); ?>
</div>