<?php

try {
    $conexao = new PDO("mysql:dbname=systempsi;localhost","root","");
} catch (Exception $e)  {
    echo "Erro ao concetar com o banco de dados!" . $e;
}



?>