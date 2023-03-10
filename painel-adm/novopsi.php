<h1>Cadastro de psicólogos auxiliares</h1>
<P>Cadastre psicólogos auxiliares para ter acesso aos seus pacientes.</P>
<h5>Preencha o formulário</h5>

<form  method="POST">
    <input type="text" placeholder="Nome do Psicólogo" class="form-control mb-2" name="nome">
    <input type="email" placeholder="Email do Psicólogo" class="form-control mb-2" name="email">
    <input type="text" placeholder="Telefone do Psicólogo" class="form-control mb-2" name="telefone">
    <input type="text" placeholder="CRP do Psicólogo" class="form-control mb-2" name="CRP">
    <input type="password" placeholder="Senha de acesso do Psicólogo" class="form-control mb-2" name="senha">
    <a href="index.php?acao=configuracoes" class="btn btn-primary">Voltar</a>
    <input type="submit" class="btn btn-primary" name=btnNovoPsi>
</form>

<?php

if (isset($_POST['btnNovoPsi'])){
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $CRP = $_POST['CRP'];
    $senha = md5($_POST['senha']);

if (empty($nome) or empty($telefone) or empty($email) or empty($CRP) or empty($senha)) {
    echo "<script language='javascript'> window.alert('Campo obrigatório em branco'); </script>";
    echo "<script language='javascript'> window.location='index.php?acao=configuracoes'; </script>";
} else {
try {
    $sql = $conexao->prepare("INSERT INTO psicologo VALUES (null,?,?,?,?,?)");
    $sql -> execute(array($nome, $senha, $email, $telefone, $CRP));
    echo "<script language='javascript'> window.location='index.php?acao=configuracoes'; </script>";
} catch (Exception $e)  {
    echo "<script language='javascript'> window.alert('Dados duplicados.'); </script>";
    echo "<script language='javascript'> window.location='index.php?acao=configuracoes'; </script>";
}
}

}
?>