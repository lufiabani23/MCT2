<?php

function enviarMensagemWhatsApp ($telefone , $mensagem){
$url = "http://api.enviame.com.br/send-text";

  $data = array('instance' => "GWBNMJ209",
                'to' => "$telefone", //$telefone
                'token' => '65KI4-5H9-2551D',
                'message' => "$mensagem");

  $options = array('http' => array(
                 'method' => 'POST',
                 'content' => http_build_query($data)
  ));

  $stream = stream_context_create($options);

  $result = file_get_contents($url, false, $stream);

  echo $result;
}

// Função para formatar o número de telefone
function formatarTelefone($telefone) {
    // Remove caracteres especiais
    $telefone = preg_replace('/[^0-9]/', '', $telefone);

    // Adiciona o código do país (Brasil)
    if (substr($telefone, 0, 2) == '55') {
        return $telefone;
    } else {
        return '55' . $telefone;
    }
}

?>