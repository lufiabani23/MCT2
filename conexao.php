<?php

try {
    $conexao = new PDO("mysql:host=localhost;dbname=Systempsi;charset=utf8", "root", "");
} catch (Exception $e)  {
    echo "Erro ao concetar com o banco de dados!" . $e;
}



?>