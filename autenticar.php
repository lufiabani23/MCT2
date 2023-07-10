<?php

require_once("conexao.php");

@session_start();

if (empty($_POST['usuario']) or empty($_POST['senha'])) {
    header("location:index.php");
}

$usuario = $_POST['usuario'];
$senha = md5($_POST['senha']);

$result = $conexao->prepare("SELECT * from psicologo where Email = :usuario and Senha = :senha ");

$result->bindValue(":usuario", $usuario);
$result->bindValue(":senha", $senha);

$result->execute();

$dados = $result->fetchAll(PDO::FETCH_ASSOC);
$linhas = count($dados);


if ($linhas > 0 and $linhas < 2) {
    $_SESSION['id_psicologo'] = $dados[0]['ID'];
    $_SESSION['nome_psicologo'] = $dados[0]['Nome'];
    $_SESSION['CRP_psicologo'] = $dados[0]['CRP'];
    
    //Verificação do convenio "Particular"
    $sqlConvenioParticular = $conexao->prepare("SELECT * FROM convenios WHERE (Psicologo = $_SESSION[id_psicologo] and Nome = 'Particular')");
    $sqlConvenioParticular->execute();
    $listaConvenio = $sqlConvenioParticular->fetchAll(PDO::FETCH_ASSOC);
    if (count($listaConvenio) < 1) {
        $sqlInserirParticular = $conexao->prepare("INSERT INTO convenios (Nome,Psicologo) VALUE ('Particular','$_SESSION[id_psicologo]')");
        $sqlInserirParticular->execute();
    }

    $sqlFormaPGTO = $conexao->prepare("SELECT * FROM forma_pgto WHERE (Psicologo = $_SESSION[id_psicologo] and Nome = 'Dinheiro')");
    $sqlFormaPGTO->execute();
    $listaPGTO = $sqlFormaPGTO->fetchAll(PDO::FETCH_ASSOC);
    if (count($listaPGTO) < 1) {
        $sqlInserirDinheiro = $conexao->prepare("INSERT INTO forma_pgto (Nome,Psicologo) VALUE ('Dinheiro','$_SESSION[id_psicologo]')");
        $sqlInserirDinheiro->execute();
    }
    header("location:painel-adm/index.php");
} else {
    echo "<script language='javascript'> window.alert('Dados Incorretos.'); </script>";
    echo "<script language='javascript'> window.location='index.php'; </script>";
}
