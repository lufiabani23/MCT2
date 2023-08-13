<?php

require_once("config.php");
$conexao = conectar();
@session_start();

if (empty($_POST['usuario']) or empty($_POST['senha'])) {
    header("location:index.php");
}

$usuario = $_POST['usuario'];
$senha = md5($_POST['senha']);

$where = "Email = '$usuario' and Senha = '$senha'";
$dados = select('psicologo', $where);
$linhas = count($dados);

if ($linhas > 0 and $linhas < 2) {
    $_SESSION['id_psicologo'] = $dados[0]['ID'];
    $_SESSION['CRP_psicologo'] = $dados[0]['CRP'];
    $_SESSION['nome_psicologo'] = $dados[0]['Nome'];
    $_SESSION['email_psicologo'] = $dados[0]['Email'];
    $_SESSION['senha_psicologo'] = $dados[0]['Senha'];
    
    //Verificação do convenio "Particular"
    $where = "Psicologo = $_SESSION[id_psicologo] and Nome = 'Particular'";
    $listaConvenio = select('convenios', $where);

    if (count($listaConvenio) < 1) {
        $dados = array('Nome' => 'Particular', 'Psicologo' => $_SESSION['id_psicologo']);
        insert ('convenios', $dados);
    }

    header("location:painel-adm/index.php");
} else {
    echo "<script language='javascript'> window.alert('Dados Incorretos.'); </script>";
    echo "<script language='javascript'> window.location='index.php'; </script>";
}
