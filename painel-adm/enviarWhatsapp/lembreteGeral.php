<?php
//CONEXÃO A PARTE PARA TAREFA CRON
try {
    $conexao = new PDO("mysql:host=108.167.132.36;dbname=hgsys947_systempsi;charset=utf8", "hgsys947_admin", "systempsi23");
} catch (Exception $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    exit;
}

include_once('funcoesWhatsapp.php');

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

        print_r($agendamentos);

        if (count($agendamentos) > 0) {
            
            // Loop para enviar mensagem para cada paciente com agendamento
            foreach ($agendamentos as $agendamento) {
                $idPaciente = $agendamento['Paciente'];

                // Consulta para obter as informações do paciente
                $sqlPaciente = "SELECT * FROM paciente WHERE ID = :idPaciente";
                $stmtPaciente = $conexao->prepare($sqlPaciente);
                $stmtPaciente->bindParam(':idPaciente', $idPaciente);
                $stmtPaciente->execute();
                $paciente = $stmtPaciente->fetch(PDO::FETCH_ASSOC);

                // Formata o número de telefone do paciente
                $telefonePaciente = formatarTelefone($paciente['Telefone']);

                // Monta a mensagem para o paciente
                $mensagemPaciente = "*LEMBRETE DE AGENDAMENTO*

Olá, " . $paciente['Nome'] . ". Você tem um agendamento para hoje com o Psicólogo $psicologo[Nome]!

Horário: " . date('H:i', strtotime($agendamento['Data_Inicio'])) . 

"

Em caso de dúvidas, confirmação do agendamento, alteração do horário ou cancelamento, entre em contato com o *Psicólogo " .  $psicologo['Nome'] . "*, através do link https://api.whatsapp.com/send?phone=". formatarTelefone($psicologo['Telefone']) . "


_-MENSAGEM AUTOMÁTICA- favor não responder_
_*SystemPsi* - Seu consultório na palma da sua mão!_
";

                // Substitua "sua_api_whatsapp" pela função ou método da sua API para enviar a mensagem pelo WhatsApp
                enviarMensagemWhatsApp($telefonePaciente, $mensagemPaciente);
            }
        }
    }
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}

try {
    // Consulta para obter todos os psicólogos cadastrados
    $sqlPsicologos = "SELECT * FROM psicologo";
    $stmtPsicologos = $conexao->prepare($sqlPsicologos);
    $stmtPsicologos->execute();
    $psicologos = $stmtPsicologos->fetchAll(PDO::FETCH_ASSOC);

    // Loop para enviar o email e mensagem para cada psicólogo
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

        // Monta a mensagem para o psicólogo
        $mensagemPsicologo = "*Resumo dos Agendamentos para o dia " . date('d/m/Y') . "*\n";
        $mensagemPsicologo .= "Olá, " . $psicologo['Nome'] . ". Aqui está o resumo dos seus agendamentos para hoje:
        \n";

        // Verifica se há agendamentos para o psicólogo atual
        if (count($agendamentos) > 0) {
            // Loop para adicionar os agendamentos ao resumo
            foreach ($agendamentos as $agendamento) {
                $telefonePsicologo = formatarTelefone($psicologo['Telefone']);
                $idPaciente = $agendamento['Paciente'];

                // Consulta para obter as informações do paciente
                $sqlPaciente = "SELECT * FROM paciente WHERE ID = :idPaciente";
                $stmtPaciente = $conexao->prepare($sqlPaciente);
                $stmtPaciente->bindParam(':idPaciente', $idPaciente);
                $stmtPaciente->execute();
                $paciente = $stmtPaciente->fetch(PDO::FETCH_ASSOC);

                // Formata o horário do agendamento
                $horarioAgendamento = date('H:i', strtotime($agendamento['Data_Inicio']));

                $mensagemPsicologo .= "Paciente: " . $paciente['Nome'] . "\n";
                $mensagemPsicologo .= "Horário: " . $horarioAgendamento . "\n";
                $mensagemPsicologo .= "Motivo: " . $agendamento['Motivo'] . "\n";
                $mensagemPsicologo .= "OBS.: " . $agendamento['OBS.'] . "\n\n";
            }
            $mensagemPsicologo .= "

Todos os pacientes agendados para hoje foram devidamente avisados por meio do WhatsApp. Em caso de alguma alteração ou necessidade de contato, por favor, entre em contato diretamente com eles. Agradecemos sua atenção e disponibilidade em utilizar o SystemPsi para uma experiência ainda mais eficiente. Estamos à disposição para qualquer suporte necessário.


_-MENSAGEM AUTOMÁTICA- favor não responder_
_*SystemPsi* - Seu consultório na palma da sua mão!_";
        // Substitua "sua_api_whatsapp" pela função ou método da sua API para enviar a mensagem pelo WhatsApp
        enviarMensagemWhatsApp($telefonePsicologo, $mensagemPsicologo);
        }
    }
}catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}



?>
