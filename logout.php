<?php

@session_start();
unset($_SESSION['nome_psicologo']);
@session_destroy();
header ('location:index.php');
?>

