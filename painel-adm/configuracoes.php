<?php

//ADICIONAR CONVENIO
if (isset($_POST['btnNovoConvenio'])) {
    $nomeconvenio = $_POST['nomeconvenio'];
    $valorconsulta = $_POST['valorconsulta'];
    $sqlConvenio = $conexao->prepare("INSERT INTO convenios VALUES (null,?,?,?)");
    $sqlConvenio->execute(array($nomeconvenio, $valorconsulta, $_SESSION['id_psicologo']));
    echo "<script language='javascript'> window.alert('Convênio inserido com sucesso!'); </script>";
    echo "<script language='javascript'> window.location='index.php?acao=configuracoes'; </script>";
}

//BUCAR CONVENIOS
$sql = $conexao->prepare("SELECT * FROM convenios where (Nome != 'Particular')");
$sql->execute();
$listaconvenios = $sql->fetchALL();

// APAGAR CONVENIO
if (isset($_GET['btnApagarConvenio'])) {
    $idApagarConvenio = $_GET['ID'];
    try {
        $sqlApagarConvenio = $conexao->prepare("DELETE FROM convenios where (ID = '$idApagarConvenio')");
        $ApagarConvenio = $sqlApagarConvenio->execute();
        echo "<script language='javascript'> window.alert('Convênio apagado com sucesso!'); </script>";
        echo "<script language='javascript'> window.location='index.php?acao=configuracoes'; </script>";
    } catch (Exception $e) {
        echo "<script language='javascript'> window.alert('Você ainda tem pacientes vinculados a este convênio.'); </script>";
        echo "<script language='javascript'> window.location='index.php?acao=pacientes&idc=$idApagarConvenio'; </script>";
    }
}

// ALTERAR VALOR PARTICULAR
if (isset($_POST['btnNovoParticular'])) {
    $novoValorCovenio = $_POST['novoValor'];
    $sqlNovoValorConvenio = $conexao->prepare("UPDATE convenios SET Valor_Consulta = $novoValorCovenio WHERE (Nome = 'Particular' and Psicologo = $_SESSION[id_psicologo])");
    $sqlNovoValorConvenio->execute();
    echo "<script language='javascript'> window.alert('Valor alterado com sucesso!'); </script>";
    echo "<script language='javascript'> window.location='index.php?acao=configuracoes'; </script>";
}

?>


<div class="mt-3"><a href="index.php?acao=novopsi&configuracoes=" class="btn btn-primary">Cadastrar Psicólogo Auxiliar</a></div>
<div class="mt-3"><a href="index.php?acao=horariosdisponiveis&configuracoes=" class="btn btn-primary">Atualizar Horários Disponíveis</a></div>

<!-- BOTÃO CONVENIO !-->
<div class="mt-3"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#botaoNovoPaciente">
        Atualizar Convênios
    </button>
</div>

<!-- MODAL CONVENIO !-->
<div class="modal fade" id="botaoNovoPaciente" tabindex="-1" role="dialog" aria-labelledby="#modalNovoPaciente" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNovoPaciente">Atualizar Convênios</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
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
                        foreach ($listaconvenios as $indice => $linha) {
                            if ($linha['Psicologo'] == $_SESSION['id_psicologo']) {
                        ?>
                                <tr>
                                    <th scope="row"><?php echo $linha['ID'] ?></th>
                                    <td><?php echo $linha['Nome'] ?></td>
                                    <td><?php echo $linha['Valor_Consulta'] ?></td>
                                    <td><a href="<?php echo $_SERVER['PHP_SELF'] . "?acao=" . $item5 . "&btnApagarConvenio=&ID=" . $linha['ID']; ?>" class="btn btn-danger" name="btnApagarConvenio">Excluir</a></td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>

                <form method="POST" action="<?php $_SERVER['PHP_SELF'] ?>?acao=configuracoes">
                    <h5>Adicionar novo Convênio</h5>
                    <div class="form-row">
                        <input placeholder="Nome" type="text" class="form-control col-md-4 col-sm-12 mr-2" name="nomeconvenio">
                        <input type="text" placeholder="Valor por Consulta" class="form-control col-md-4 col-sm-12 mr-2" name="valorconsulta">
                        <input type="submit" class="btn btn-primary col-md-2 col-sm-12" value="Cadastrar" name="btnNovoConvenio">
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- BOTÃO PARTICULAR !-->
<div class="mt-3">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#botaoParticular">
        Atualizar consulta Particular
    </button>
</div>

<!-- MODAL PARTICULAR !-->
<div class="modal fade" id="botaoParticular" tabindex="-1" role="dialog" aria-labelledby="#modalParticular" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNovoPaciente">Consulta Particular</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <?php
                //BUSCA O VALOR DA CONSULTA PARTICULAR 
                $sqlParticular = $conexao->prepare("SELECT * FROM convenios where (Nome = 'Particular' and Psicologo = $_SESSION[id_psicologo])");
                $sqlParticular->execute();
                $convenioParticular = $sqlParticular->fetchALL(PDO::FETCH_ASSOC);
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
    </div>
</div>
</div>