<?php
//CONEXÃO A PARTE PARA TAREFA CRON


include_once('../config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $conexao = new PDO("mysql:host=108.167.132.36;dbname=hgsys947_systempsi;charset=utf8", "hgsys947_admin", "systempsi23");
} catch (Exception $e)  {
}
date_default_timezone_set('America/Sao_Paulo'); // Defina o fuso horário correto para o Brasil


// Obtém a data atual
$dataAtual = date('Y-m-d');

try {
    // Consulta para obter todos os psicólogos cadastrados
    $sqlPsicologos = "SELECT * FROM psicologo";
    $stmtPsicologos = $conexao->prepare($sqlPsicologos);
    $stmtPsicologos->execute();
    $psicologos = $stmtPsicologos->fetchAll(PDO::FETCH_ASSOC);

    // Loop para enviar o email para cada psicólogo
    foreach ($psicologos as $psicologo) {
        $psicologoId = $psicologo['ID'];

        // Consulta para obter os agendamentos do dia atual para o psicólogo atual
        $sqlAgendamentos = "SELECT * FROM agendar WHERE Data_Inicio > :dataInicio AND Data_Inicio < :dataFim AND Realizado = 0 and Psicologo = :psicologoId";
        $stmtAgendamentos = $conexao->prepare($sqlAgendamentos);
        $stmtAgendamentos->bindValue(':dataInicio', $dataAtual . " 00:00:00");
        $stmtAgendamentos->bindValue(':dataFim', $dataAtual . " 23:59:59");
        $stmtAgendamentos->bindValue(':psicologoId', $psicologoId);
        $stmtAgendamentos->execute();
        $agendamentos = $stmtAgendamentos->fetchAll(PDO::FETCH_ASSOC);

        if (count($agendamentos) > 0) {
            // Monta o corpo do e-mail com os agendamentos
            $mensagem = "<h1>Resumo dos Agendamentos para o dia " . date('d/m/Y') . "</h1>";
            $mensagem .= "<p>Olá, " . $psicologo['Nome'] . ". Tudo bem? <br> Acompanhe sua agenda para o dia de hoje!</p>";
            $mensagem .= "<ul>";

            // Loop para buscar as informações de cada paciente
            foreach ($agendamentos as $agendamento) {
                $idPaciente = $agendamento['Paciente'];

                // Consulta para obter as informações do paciente
                $sqlPaciente = "SELECT * FROM paciente WHERE ID = :idPaciente";
                $stmtPaciente = $conexao->prepare($sqlPaciente);
                $stmtPaciente->bindParam(':idPaciente', $idPaciente);
                $stmtPaciente->execute();
                $paciente = $stmtPaciente->fetch();

                $mensagem .= "<li>Paciente: " . $paciente['Nome'] . "</li>";
                $mensagem .= "<li>Horário: " . date('H:i', strtotime($agendamento['Data_Inicio'])) . "</li>";
                $mensagem .= "<li>Motivo: " . formatarArrayIsset($agendamento, 'Motivo') . "</li>";
                $mensagem .= "<li>OBS.: " . formatarArrayIsset($agendamento, 'OBS') . "</li>";
                $mensagem .= "<br>";
            }
            $mensagem .= "</ul>";
            $mensagem .= "<p>Tenha um ótimo dia!</p> <br> <h3>SystemPsi - seu consultório na palma da sua mão.</h3>";


            // Envio do e-mail para o psicólogo
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.titan.email';
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Username = 'contato@systempsi.com.br';
            $mail->Password = 'contatoSystempsi23!';
            $mail->SMTPSecure = 'SSL';  // Corrigido para 'ssl' em letra minúscula
            $mail->CharSet = 'UTF-8';
            $mail->setFrom('contato@systempsi.com.br', 'Agendamentos');
            $mail->addAddress($psicologo['Email']);  // Supondo que $psicologo é um array associativo
            $mail->Subject = "Resumo dos Agendamentos - " . date('d/m/Y');
            $mail->isHTML(true);
            $mail->Body = "$mensagem";
            $mail->send();
            
        }
    }
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}

?>