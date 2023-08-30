<?php

//ARQUIVO COM AS CONFIGURAÇÕES/FUNÇÕES NECESSÁRIAS NO SISTEMA - PADRONIZAÇÃO

require 'vendor/autoload.php'; // Caminho para o autoload do Composer

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


function select($tabela, $where = null, $coluna = "*") {
    $conexao = conectar();

    try {
        if (isset($where)) {
            $sql = "SELECT $coluna FROM $tabela WHERE $where";
            $stmt = $conexao->prepare($sql);
        } else {
            $sql = "SELECT $coluna FROM $tabela";
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
    try {
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
            //return $conexao->lastInsertId(); // Inserção bem-sucedida
        } else {
            return false; // Erro na inserção
        }
    } catch (PDOException $e) {
        // Aqui você pode lidar com a exceção da forma desejada
        echo "Erro ao executar a consulta: " . $e->getMessage();
        return false; // Ou outro tratamento de erro apropriado
    }
}


function delete($tabela, $where) {
    $conexao = conectar();

    try {
        $sql = "DELETE FROM $tabela WHERE $where";
        $stmt = $conexao -> prepare ($sql);
        $stmt -> execute();
        return true;
        $conexao = null;
    } catch (PDOException $e) {
        return false;
    }
}

function update($tabela, $dados, $where) {
    try {
        $conexao = conectar();
        $campos = array();
        
        foreach ($dados as $campo => $valor) {
            $campos[] = "$campo = :$campo";
        }
        
        $campos_string = implode(', ', $campos);
        
        $query = "UPDATE $tabela SET $campos_string WHERE $where";
        
        $stmt = $conexao->prepare($query);
        
        foreach ($dados as $campo => $valor) {
            $stmt->bindValue(":$campo", $valor);
        }
        
        if ($stmt->execute()) {
            return true; // Atualização bem-sucedida
        } else {
            return false; // Erro na atualização
        }
    } catch (PDOException $e) {
        // Aqui você pode lidar com a exceção da forma desejada
        echo "Erro ao executar a atualização: " . $e->getMessage();
        return false; // Ou outro tratamento de erro apropriado
    }
}


function formatarArrayIsset($array, $index, $key = null) {
    if($key != null) {
        return isset($array[$key][$index]) ? $array[$key][$index] : '';
    } else{
        return isset($array[$index]) ? $array[$index] : '';
    }
}


?>

