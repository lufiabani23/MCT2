<div class="col-md-6 col-sm-12">
    <button type="button" class="btn btn-secondary novo-agendamento" data-toggle="modal" data-target="#botaoNovoAgendamento">
      <span style="font-size: 16pt;">+</span> Novo agendamento
    </button>
  </div>

<!-- MODAL DE NOVO PACIENTE -->
<div class="modal fade novo-agendamento novo-modal" id="botaoNovoAgendamento" tabindex="-1" role="dialog" aria-labelledby="#modalNovoAgendamento" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title ml-3" id="modalNovoAgendamento">AGENDAMENTO</h5>
        <div class="col-lg-1 mr-lg-2 d-none d-lg-block"><img class="logo-lateral" src="../img/logosistemapsico.png"></div>
        </button>
      </div>
      <div class="modal-body">

        <form id="CadastroPaciente" method="POST" action="index.php?acao=pacientes">
          <div class="form-row">
            <div class="form-group col-md-9 col-sm-12">
              <label for="NomePaciente">Paciente</label>
              <select class="form-control" id="NomePaciente" name="NomePaciente">
                <option>--Selecione--</option>
                <?php
                  foreach ($listapacientes as $indice => $linha) {
                    echo "<option>". $linha['Nome'];"</option>";
                  }; ?>
              </select>
            </div>
            <div class="form-group col-md-3 col-sm-12">
              <label for="DataAgendamento">Data</label>
              <input class="form-control" type="date" id="DataAgendamento" name="DataAgendamento">
            </div>
          </div>

          <label for="HoraAgendamento">Horário</label>
          <div class="form-row">
            <div class="form-group col-md-2">
              <select id="HoraAgendameto" name="HoraAgendamento" class="form-control">
                <?php for ($i = 00; $i<=23 ; $i++) {
                  echo "<option>$i</option>";
                }; ?>
              </select>
            </div>
            :
            <div class="form group col-md-2">
              <select id="MinutoAgendameto" class="form-control">
                <?php for ($i = 00; $i<=59 ; $i+=5) {
                  echo "<option>$i</option>";
                }; ?>
              </select>
            </div>
          </div>
        </form>

      </div>

      <div class="modal-footer">
        <button form="CadastroPaciente" type="submit" class="btn btn-success" name="btnNovoPaciente">Agendar</button>
        <button form="CadastroPaciente" type="reset" data-dismiss="modal" class="btn btn-danger" name="<?php echo $item2 ?>">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<!-- FullCalendar -->
<link href='calendar/css/fullcalendar.css' rel='stylesheet' />
<link href='calendar/css/fullcalendar.print.min.css' rel='stylesheet' media='print' />

<?php
require_once('calendar/evento/action/conexao.php');
date_default_timezone_set('America/Sao_Paulo');

$database = new Database();
$db = $database->conectar();

// BUSCA OS EVENTOS CADASTRADOS
$sql = "SELECT ID, Paciente, Motivo, Data_Inicio, Data_Fim FROM agendar
Where Psicologo = $_SESSION[id_psicologo]";
$req = $db->prepare($sql);
$req->execute();
$events = $req->fetchAll();

// CONVERTE O ID DO PACIENTE PARA O NOME DELE
foreach ($events as $i => $linha) {
	$sqlPaciente = $db -> prepare("SELECT Nome FROM paciente where (ID = $linha[Paciente])");
	$sqlPaciente -> execute();
	$nomePaciente = $sqlPaciente ->fetchAll();
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

<!-- jQuery Version 1.11.1 -->
<script src="calendar/js/jquery.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="calendar/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>


<!-- FullCalendar -->
<script src='calendar/js/moment.min.js'></script>
<script src='calendar/js/fullcalendar.min.js'></script>
<script src='calendar/locale/pt-br.js'></script>
<?php include('calendar/calendario.php'); ?>

