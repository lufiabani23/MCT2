<h1>Recuperação de Senha - SystemPsi</h1>

<?php 

include_once('conexao.php');
 /*
$buscarPsicologo -> prepare ("SELECT * from psicologo where ID = :id");
$buscarPsicologo -> bindParam (':id' , $idPsicologo);
$buscarPsicologo -> execute();
*/

if (isset($_GET['id'])) {
    $idPsicologo = $_GET['id'];
}

if (isset($_POST['btnAlterarSenha'])) {
    $senhaNova = md5($_POST['senhaNova']);
    $sqlAlterarSenha = $conexao -> prepare("UPDATE psicologo SET Senha = :senhaNova where ID = :id");
    $sqlAlterarSenha -> bindParam (':senhaNova' , $senhaNova);
    $sqlAlterarSenha -> bindParam (':id', $idPsicologo);
    $sqlAlterarSenha -> execute();

    echo "<script language='javascript'>alert('Senha alterada com sucesso. Faça login novamente no Sistema!');</script>";
    echo "<script language='javascript'>window.location='index.php';</script>";
}

?>



<form action="" method="POST">
    <label>Digite sua nova senha:</label>
    <input name="senhaNova">
    <button type="submit" name="btnAlterarSenha">Alterar</button>
</form>