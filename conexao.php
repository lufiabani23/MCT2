<?php

try {
    $conexao = new PDO("mysql:host=108.167.132.36;dbname=hgsys947_systempsi;charset=utf8", "hgsys947_admin", "systempsi23");
} catch (Exception $e)  {
    echo "Erro ao concetar com o banco de dados!" . $e;
}
?>