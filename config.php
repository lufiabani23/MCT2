<?php

//ARQUIVO COM AS CONFIGURAÇÕES/FUNÇÕES NECESSÁRIAS NO SISTEMA - PADRONIZAÇÃO

date_default_timezone_set('America/Sao_Paulo'); // Defina o fuso horário correto para o Brasil


function conectar() {
    $host = "108.167.132.36";
    $dbname = "hgsys947_systempsi";
    $user = "hgsys947_admin";
    $senha = "systempsi23";
    try {
        $conexao = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", "$user", "$senha");
        return $conexao;
    } catch (Exception $e)  {
        echo "Erro ao concetar com o banco de dados!" . $e;
    }
}


function select($tabela, $where = null) {
    $conexao = conectar();

    try {
        if (isset($where)) {
            $sql = "SELECT * FROM $tabela WHERE $where";
            $stmt = $conexao->prepare($sql);
        } else {
            $sql = "SELECT * FROM $tabela";
            $stmt = $conexao->prepare($sql);
        }

        $stmt->execute();
        $resultado = $stmt->fetchAll();
        $conexao = null;
        return $resultado !== false ? $resultado : array(); // Retorne um array vazio se não houver resultados
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}

function insert($tabela, $dados) {
    $conexao = conectar();
    $campos = array();
    $valores = array();
    
    foreach ($dados as $campo => $valor) {
        $campos[] = $campo;
        $valores[] = ":$campo";
    }
    
    $campos_string = implode(', ', $campos);
    $valores_string = implode(', ', $valores);
    
    $query = "INSERT INTO $tabela ($campos_string) VALUES ($valores_string)";
    
    $stmt = $conexao->prepare($query);
    
    foreach ($dados as $campo => $valor) {
        $stmt->bindValue(":$campo", $valor);
    }
    
    if ($stmt->execute()) {
        return $conexao -> lastInsertId(); // Inserção bem-sucedida
    } else {
        return false; // Erro na inserção
    }
}

function delete ($tabela, $where) {
    $conexao = conectar();

    try {
        $sql = "DELETE FROM $tabela WHERE $where";
        $stmt = $conexao -> prepare ($sql);
        $stmt -> execute();
        $conexao = null;
    } catch (PDOException $e) {
        echo $e;
    }
}

function update($tabela, $dados, $id) {
    $conexao = conectar();
    $campos = array();
    foreach ($dados as $campo => $valor) {
        $campos[] = "$campo = :$campo";
    }
    $campos_string = implode(', ', $campos);

    $query = "UPDATE $tabela SET $campos_string WHERE id = $id";

    $stmt = $conexao->prepare($query);

    foreach ($dados as $campo => $valor) {
        $stmt->bindValue(":$campo", $valor);
    }

    if ($stmt->execute()) {
        return true; // Atualização bem-sucedida
    } else {
        return false; // Erro na atualização
    }
}

?>

