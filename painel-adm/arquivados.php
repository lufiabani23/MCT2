<?php

/*
Este código lista os pacientes que estão com situacao = 0 (arquivados)
Ele permite emissão do relatório de atendimento e ficha do paciente pelo modo convencional
Tem busca pelo nome
Faz o processo de desarquivamento (Troca para sitacao = 1)
*/


//Busca geral dos pacientes arquivados
$listapacientes = select('paciente', "Psicologo = $_SESSION[id_psicologo] and Situacao = 0");

//Sistema de busca de pacientes pelo nome
if (isset($_GET['btnBuscarPacientesArquivados']) and $_GET['txtBuscarPacientes'] != "") {
    $txtBuscarPaciente = "%" . $_GET['txtBuscarPacientes'] . "%";
    $where = "Nome like '$txtBuscarPaciente' and Situacao = 0 order by Nome asc";
    $listapacientes = select('paciente', $where);
}

//Processo de desarquivamento do paciente - conversão da situacao para  1
if (isset($_GET['funcao']) and $_GET['funcao'] == "desarquivar") {
    $idDesarquivar = $_GET['id'];
    $dados = array('Situacao' => 1);
    update('paciente', $dados, "ID = $idDesarquivar");
    echo "<script language='javascript'> window.location='index.php?acao=$item7&alert=success'; </script>";
}

?>

<!-- TÍTULO E BOTÃO DE PESQUISA -->
<div class="row mt-1"> <!-- botão alinhado a borda da tabela -->

    <div class="col-md-6 col-sm-12">
        <h1>Pacientes arquivados</h1>
    </div>
    <!-- Form para envio dos dados para pesquisa -->
    <div class="col-md-6 col-sm-12">
        <div class="float-right">
            <form class="form-inline my-2 my-lg-0" action="index.php?acao=arquivados">
                <input class="form-control mr-sm-2" type="search" placeholder="Buscar paciente" aria-label="Search"
                    name="txtBuscarPacientes" value="<?php if (isset($_GET['btnBuscarPacientesArquivados']) and $_GET['txtBuscarPacientes'] != "")
                        echo $_GET['txtBuscarPacientes']; //manter o nome pesquisado no input   
                    ?>">
                <button class="btn btn-outline-primary my-2 my-sm-0" type="submit"
                    name="btnBuscarPacientesArquivados">Buscar</button>
            </form>
        </div>
    </div>
</div>

<!-- TABELA DE PACIENTE -->
<!-- botão excluir leva o ID do paciente da linha -->
<table class="table table-striped mt-2 lista-pacientes">
    <thead>
        <tr>
            <th scope="col">Cod.</th>
            <th scope="col">Nome Completo</th>
            <th scope="col">Convênio</th>
            <th scope="col" class="d-none d-sm-block">Telefone</th>
            <th scope="col">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (($listapacientes == null)) {
            $nenhumPaciente = "Nenhum paciente encontrado.";
        } else {
            foreach ($listapacientes as $indice => $linha) {
                $nenhumPaciente = null;
                if ($linha['Psicologo'] == $_SESSION['id_psicologo']) {
                    ?>
                    <tr>
                        <th scope="row">
                            <?php echo $linha['ID'] ?>
                        </th>
                        <td>
                            <?php echo $linha['Nome'] ?>
                        </td>
                        <td>
                            <?php
                            // Processo de conversão do ID do convênio para o nome do convênio do paciente
                            $nomeConvenio = select('convenios', "ID = $linha[Convenio]");
                            if (count($nomeConvenio) == 1) {
                                echo $nomeConvenio[0]['Nome'];
                            }
                            ?>
                        </td>
                        <td class="d-none d-sm-block">
                            <?php echo $linha['Telefone'] ?>
                        </td>
                        <td>
                            <a class="btn btn-warning text-white mt-1"
                                href="export/relatPaciente.php?id=<?php echo $linha['ID']; ?>" target="_blank">Relatório de
                                Atendimentos</a>
                            <a form="formModalPaciente" class="btn btn-warning text-white mt-1"
                                href="export/paciente.php?id=<?php echo $linha['ID']; ?>" target="_blank">Ficha do Paciente</a>
                            <a href="index.php?acao=<?php echo $item7; ?>&funcao=desarquivar&id=<?php echo $linha['ID']; ?>"
                                class="btn btn-warning mt-1">Desarquivar</a>
                        </td>
                    </tr>

                <?php }
            }
        } ?>
    </tbody>
</table>

<?php echo $nenhumPaciente; ?>