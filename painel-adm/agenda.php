<!-- FullCalendar -->
<link href='../calendar/css/fullcalendar.css' rel='stylesheet' />
<link href='../calendar/css/fullcalendar.print.min.css' rel='stylesheet' media='print' />

<?php
require_once('../calendar/evento/action/conexao.php');
date_default_timezone_set('America/Sao_Paulo');

$database = new Database();
$db = $database->conectar();

$sql = "SELECT id_evento, titulo, descricao, inicio, termino, cor, fk_id_destinatario, fk_id_remetente, status FROM eventos as e
LEFT JOIN convites as c ON e.id_evento = c.fk_id_evento
Where fk_id_usuario = 2";
$req = $db->prepare($sql);
$req->execute();
$events = $req->fetchAll();
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
	<?php include('../calendar/evento/modal/modalAdd.php'); ?>


	<!-- Modal Editar/Mostrar/Deletar Evento -->
	<?php include('../calendar/evento/modal/modalEdit.php'); ?>

</div>

<!-- jQuery Version 1.11.1 -->
<script src="../calendar/js/jquery.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="../calendar/js/bootstrap.min.js"></script>

<!-- FullCalendar -->
<script src='../calendar/js/moment.min.js'></script>
<script src='../calendar/js/fullcalendar.min.js'></script>
<script src='../calendar/locale/pt-br.js'></script>
<?php include('../calendar/calendario.php'); ?>