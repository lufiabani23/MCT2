<?php
require_once('evento/action/conexao.php');
date_default_timezone_set('America/Sao_Paulo');

$database = new Database();
$db = $database->conectar();

	$sql = "SELECT ID, Paciente, Motivo, Data_Inicio, Data_Fim FROM agendar
	Where Psicologo = $_SESSION[id_psicologo]";
	$req = $db->prepare($sql);
	$req->execute();
	$events = $req->fetchAll();
?>

    	<!-- Bootstrap Core CSS -->
    	<link href="css/bootstrap.min.css" rel="stylesheet">
	
		<!-- FullCalendar -->
		<link href='css/fullcalendar.css' rel='stylesheet' />
		<link href='css/fullcalendar.print.min.css' rel='stylesheet' media='print' />

    	<!-- Custom CSS Calendario -->
    	<link href='css/calendar.css' rel='stylesheet' />

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
					if(erro.inicio.value>erro.termino.value){
						alert('Data de Inicio deve ser menor ou igual a de termino.');
						return false;
					}else if(erro.inicio.value==erro.termino.value){
						alert('Defina um horario de inicio e termino.(24h)');
						return false;
					}
				}
			</script>


			<!-- Modal Adicionar Evento -->
			<?php include ('evento/modal/modalAdd.php'); ?>
			
			
			<!-- Modal Editar/Mostrar/Deletar Evento -->
			<?php include ('evento/modal/modalEdit.php'); ?>

		</div>

		<!-- jQuery Version 1.11.1 -->
		<script src="js/jquery.js"></script>

		<!-- Bootstrap Core JavaScript -->
		<script src="js/bootstrap.min.js"></script>
		
		<!-- FullCalendar -->
		<script src='js/moment.min.js'></script>
		<script src='js/fullcalendar.min.js'></script>
		<script src='locale/pt-br.js'></script>
		<?php include ('calendario.php'); ?>
		
