<?php

@session_start();
@session_destroy();
unset($_Session['nome_psicologo']);
header ('location:index.php');
?>