<?php
/*
Este código tem as opções de configurações
Ele adiciona e altera os convênios cadastrados
Edita as informações pessoais do psicólogo (até a senha)
Também altera a consulta particular (valor não está sendo utilizado)
*/


//Sistema para adicionar ou editar um convênio
if (isset($_POST['btnNovoConvenio'])) {
    if (!empty($_POST['idConvenio'])) {
        $dados = array('Nome' => $_POST['nomeconvenio'], 'Valor_Consulta' => $_POST['valorconsulta']);
        update('convenios', $dados, "ID = $_POST[idConvenio]");
    } else {
        $dados = array('Nome' => $_POST['nomeconvenio'], 'Valor_Consulta' => $_POST['valorconsulta'], 'Psicologo' => $_SESSION['id_psicologo']);
        insert ('convenios', $dados);
        echo "<script language='javascript'> window.alert('Convênio inserido com sucesso!'); </script>";
        echo "<script language='javascript'> window.location='index.php?acao=configuracoes'; </script>";
    }
}

// Sistema para editar os dados pessoais do psicólogo
if (isset($_POST['btnEditarPsicologo'])) {
    if ($_POST['senhaPsicologo'] == "") {
        $senhaSQL = $_POST['senhaPsicologo'];
    } else {
        $senhaSQL = md5($_POST['senhaPsicologo']);
    }

    $dados = array(
        'Nome' => $_POST['nomePsicologo'],
        'Email' => $_POST['emailPsicologo'],
        'CRP' => $_POST['CRPPsicologo'],
        'Senha' => $senhaSQL
    );

    update('psicologo', $dados, "ID = $_SESSION[id_psicologo]");

    echo "<script language='javascript'> window.alert('Dados alterados com sucesso!'); </script>";
    echo "<script language='javascript'> window.location='index.php?acao=configuracoes'; </script>";

}


//Sistema para buscar convênios além do convênio particular
$where = "(Nome != 'Particular') and Psicologo = $_SESSION[id_psicologo]";
$listaconvenios = select('convenios', $where);

//Sistema para excluir o convênio - se tiver pacientes vinculados, não exclui
if (isset($_GET['btnApagarConvenio'])) {
    $idApagarConvenio = $_GET['ID'];
    //dentro do try somente para verificação de pacientes vinculados

        if (delete('convenios', "ID = $idApagarConvenio") == false) {
            echo "<script language='javascript'> window.alert('Você ainda tem pacientes vinculados a este convênio.'); </script>";
            echo "<script language='javascript'> window.location='index.php?acao=pacientes&idc=$idApagarConvenio'; </script>";
        } else {
            echo "<script language='javascript'> window.alert('Convênio apagado com sucesso!'); </script>";
            echo "<script language='javascript'> window.location='index.php?acao=configuracoes'; </script>";
        }
}

// Sistema de edição do valor da consulta particular
if (isset($_POST['btnNovoParticular'])) {
    $novoValorCovenio = $_POST['novoValor'];
    $sqlNovoValorConvenio = $conexao->prepare("UPDATE convenios SET Valor_Consulta = $novoValorCovenio WHERE (Nome = 'Particular' and Psicologo = $_SESSION[id_psicologo])");
    $sqlNovoValorConvenio->execute();
    echo "<script language='javascript'> window.alert('Valor alterado com sucesso!'); </script>";
    echo "<script language='javascript'> window.location='index.php?acao=configuracoes'; </script>";
}

?>


<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="true">Perfil</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-convenios-tab" data-toggle="pill" href="#pills-convenios" role="tab" aria-controls="pills-convenios" aria-selected="false">Convênios</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-particular-tab" data-toggle="pill" href="#pills-particular" role="tab" aria-controls="pills-particular" aria-selected="false">Particular</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="false">Geral</a>
    </li>
</ul>

<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

    </div>
    <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?acao=<?php echo $item5; ?>">
            <div class="row-form">
                <label for="nomePsicologo">Nome do psicólogo:</label>
                <input type="text" class="form-control" name="nomePsicologo" id="nomePsicologo" value="<?php echo $_SESSION['nome_psicologo']; ?>">
            </div>
            <div class="row-form">
                <label for="emailPsicologo">E-mail</label>
                <input type="text" class="form-control" name="emailPsicologo" id="emailPsicologo" value="<?php echo $_SESSION['email_psicologo']; ?>">
            </div>
            <div class="row-form">
                <label for="CRPPsicologo">CRP</label>
                <input type="text" class="form-control" name="CRPPsicologo" id="CRPPsicologo" value="<?php echo $_SESSION['CRP_psicologo']; ?>">
            </div>
            <div class="row-form">
                <label for="senhaPsicologo">Nova senha</label>
                <input type="password" class="form-control" name="senhaPsicologo" id="senhaPsicologo" minlength="8" placeholder="Altere sua senha de acesso">
                <input type="hidden" name="senhaAtual" value="<?php echo $_SESSION['senha_psicologo']; ?>">
            </div>
            <div class="row-form">
                <input type="submit" class="btn btn-primary mt-1" name="btnEditarPsicologo">
            </div>
        </form>

    </div>

    <div class="tab-pane fade" id="pills-convenios" role="tabpanel" aria-labelledby="pills-convenios-tab">
        <table class="table table-striped mt-2">
            <thead>
                <tr>
                    <th scope="col">Cod.</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Valor Consulta</th>
                    <th scope="col">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($listaconvenios) < 1) {
                    $nenhumConvenio = "Nenhum convênio encontrado.";
                }
                foreach ($listaconvenios as $indice => $linha) {
                    if ($linha['Psicologo'] == $_SESSION['id_psicologo']) {
                ?>
                        <tr>
                            <th scope="row"><?php echo $linha['ID'] ?></th>
                            <td><?php echo $linha['Nome'] ?></td>
                            <td><?php echo $linha['Valor_Consulta'] ?></td>
                            <td>
                                <a href="<?php echo $_SERVER['PHP_SELF'] . "?acao=" . $item5 . "&btnApagarConvenio=&ID=" . $linha['ID']; ?>" class="btn btn-danger" name="btnApagarConvenio">Excluir</a>
                                <a href="#" class="btn btn-primary btn-editar-convenio" data-toggle="modal" data-target="#modalEditarConvenio" data-id="<?php echo $linha['ID']; ?>" data-nome="<?php echo $linha['Nome']; ?>" data-valor="<?php echo $linha['Valor_Consulta']; ?>">Editar</a>
                            </td>

                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>
        <?php if (isset($nenhumConvenio)) {
            echo $nenhumConvenio;
        } ?> <br> <br>


        <form method="POST" action="<?php $_SERVER['PHP_SELF'] ?>?acao=configuracoes" id="formEditarConvenio">
            <div class="ml-2">
                <h5>Adicionar novo Convênio</h5>
                <div class="form-row">
                    <input placeholder="Nome" type="text" class="form-control col-md-4 col-sm-12 mr-2" name="nomeconvenio" id="editNomeConvenio">
                    <input type="text" placeholder="Valor por Consulta" class="form-control col-md-4 col-sm-12 mr-2" name="valorconsulta" id="editValorConsulta">
                    <input type="hidden" name="idConvenio" id="editIdConvenio">
                    <input type="submit" class="btn btn-primary col-md-1 col-sm-12" value="Salvar" name="btnNovoConvenio">
                    <input type="reset" class="btn btn-outline-primary col-md-1 ml-1">
                </div>
            </div>
        </form>

        <script>
            $(document).ready(function() {
                $('.btn-editar-convenio').click(function() {
                    var idConvenio = $(this).data('id');
                    var nomeConvenio = $(this).data('nome');
                    var valorConsulta = $(this).data('valor');

                    $('#editIdConvenio').val(idConvenio);
                    $('#editNomeConvenio').val(nomeConvenio);
                    $('#editValorConsulta').val(valorConsulta);
                });
            });
        </script>
    </div>

    <div class="tab-pane fade" id="pills-particular" role="tabpanel" aria-labelledby="pills-particular-tab">
        <?php
        //BUSCA O VALOR DA CONSULTA PARTICULAR 
        $where = "(Nome = 'Particular' and Psicologo = $_SESSION[id_psicologo])";
        $convenioParticular = select('convenios', $where);
        ?>

        <p style="font-size: 1.5em; text-align: center">Valor atual da consulta particular: <b><?php echo $convenioParticular[0]['Valor_Consulta']; ?></b></p>
        <h5>Alterar valor</h5>
        <div class="row"></div>
        <form action="index.php?acao=configuracoes" method="POST">
            <div class="form-row">
                <input type="text" placeholder="Novo valor" class="form-control col-lg-9 col-sm-12 mr-1" name="novoValor">
                <input type="submit" class="btn btn-primary col-lg-2 col-sm-12" value="Alterar" name="btnNovoParticular">
            </div>
        </form>
    </div>
</div>