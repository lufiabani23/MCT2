-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17-Maio-2023 às 16:56
-- Versão do servidor: 10.4.25-MariaDB
-- versão do PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `systempsi`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `agendar`
--

CREATE TABLE `agendar` (
  `Psicologo` int(11) DEFAULT NULL,
  `Paciente` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `Data_Inicio` datetime NOT NULL,
  `Data_Fim` datetime NOT NULL,
  `Motivo` text DEFAULT NULL,
  `OBS.` text DEFAULT NULL,
  `Valor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `agendar`
--

INSERT INTO `agendar` (`Psicologo`, `Paciente`, `ID`, `Data_Inicio`, `Data_Fim`, `Motivo`, `OBS.`, `Valor`) VALUES
(1, 107, 18, '2023-05-13 13:05:00', '2023-05-13 13:50:00', 'Não sei', '', 100),
(1, 107, 19, '2023-05-18 15:05:00', '2023-05-18 15:50:00', '', '', 100),
(1, 107, 20, '2023-05-26 15:00:00', '2023-05-26 15:45:00', '', '', 300);

-- --------------------------------------------------------

--
-- Estrutura da tabela `atendimento`
--

CREATE TABLE `atendimento` (
  `ID` int(11) NOT NULL,
  `Hora` varchar(5) NOT NULL,
  `Data` varchar(10) NOT NULL,
  `Valor` varchar(8) NOT NULL DEFAULT 'R$80,00',
  `Objetivo` varchar(500) NOT NULL,
  `Forma_Pgto` varchar(15) NOT NULL DEFAULT 'PIX',
  `OBS` varchar(500) DEFAULT NULL,
  `Registro` varchar(500) NOT NULL,
  `Psicologo` int(11) DEFAULT NULL,
  `Paciente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `convenios`
--

CREATE TABLE `convenios` (
  `ID` int(11) NOT NULL,
  `Nome` varchar(60) NOT NULL,
  `Valor_Consulta` varchar(10) NOT NULL,
  `Psicologo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `convenios`
--

INSERT INTO `convenios` (`ID`, `Nome`, `Valor_Consulta`, `Psicologo`) VALUES
(12, 'Particular', '100', 1),
(15, 'Particular', '500', 15);

-- --------------------------------------------------------

--
-- Estrutura da tabela `paciente`
--

CREATE TABLE `paciente` (
  `ID` int(11) NOT NULL,
  `Nome` varchar(60) NOT NULL,
  `Telefone` varchar(15) NOT NULL,
  `Email` varchar(60) DEFAULT NULL,
  `Data_Nascimento` varchar(10) NOT NULL,
  `Convenio` int(11) NOT NULL,
  `Foto` longblob DEFAULT NULL,
  `Genero` varchar(20) DEFAULT NULL,
  `CPF` varchar(15) NOT NULL,
  `Psicologo` int(11) NOT NULL,
  `Prontuario` text DEFAULT NULL,
  `Anexos` blob DEFAULT NULL,
  `Endereco` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `paciente`
--

INSERT INTO `paciente` (`ID`, `Nome`, `Telefone`, `Email`, `Data_Nascimento`, `Convenio`, `Foto`, `Genero`, `CPF`, `Psicologo`, `Prontuario`, `Anexos`, `Endereco`) VALUES
(96, 'Luis', '(00) 000', '00000@0000', '2006-02-23', 12, NULL, 'Masculino', '000.000.0', 15, '', NULL, '00000'),
(107, 'Ana', '(48) 98875-2487', 'ana@gmail.com', '2006-01-16', 12, NULL, 'Masculino', '108.705.379-03', 1, '', NULL, 'Sofá do grêmio');

-- --------------------------------------------------------

--
-- Estrutura da tabela `psicologo`
--

CREATE TABLE `psicologo` (
  `ID` int(11) NOT NULL,
  `Nome` varchar(60) NOT NULL,
  `Senha` varchar(200) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Telefone` varchar(15) NOT NULL,
  `CRP` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `psicologo`
--

INSERT INTO `psicologo` (`ID`, `Nome`, `Senha`, `Email`, `Telefone`, `CRP`) VALUES
(1, 'Tainá Fiabani', '1442b1b14603ec230cafa95ed6ecb840', 'tainafiabani@gmail.com', '(54) 99857-5848', '07/29977'),
(2, 'Hugo Vasconcelos', '827ccb0eea8a706c4c34a16891f84e7b', 'hugov@hotmail.com', '(48) 98585-7425', '00/33333'),
(15, 'Teste', 'e10adc3949ba59abbe56e057f20f883e', 'teste@teste.com', '000000', '0000000');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `agendar`
--
ALTER TABLE `agendar`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Psicologo` (`Psicologo`),
  ADD KEY `Paciente` (`Paciente`);

--
-- Índices para tabela `atendimento`
--
ALTER TABLE `atendimento`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Psicologo` (`Psicologo`),
  ADD KEY `Paciente` (`Paciente`);

--
-- Índices para tabela `convenios`
--
ALTER TABLE `convenios`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Psicologo` (`Psicologo`);

--
-- Índices para tabela `paciente`
--
ALTER TABLE `paciente`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Telefone` (`Telefone`),
  ADD UNIQUE KEY `CPF` (`CPF`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `Psicologo` (`Psicologo`),
  ADD KEY `Convenio` (`Convenio`);

--
-- Índices para tabela `psicologo`
--
ALTER TABLE `psicologo`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Senha` (`Senha`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Telefone` (`Telefone`),
  ADD UNIQUE KEY `CRP` (`CRP`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendar`
--
ALTER TABLE `agendar`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `atendimento`
--
ALTER TABLE `atendimento`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `convenios`
--
ALTER TABLE `convenios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `paciente`
--
ALTER TABLE `paciente`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT de tabela `psicologo`
--
ALTER TABLE `psicologo`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `agendar`
--
ALTER TABLE `agendar`
  ADD CONSTRAINT `agendar_ibfk_1` FOREIGN KEY (`Psicologo`) REFERENCES `psicologo` (`ID`),
  ADD CONSTRAINT `agendar_ibfk_2` FOREIGN KEY (`Paciente`) REFERENCES `paciente` (`ID`);

--
-- Limitadores para a tabela `atendimento`
--
ALTER TABLE `atendimento`
  ADD CONSTRAINT `atendimento_ibfk_1` FOREIGN KEY (`Psicologo`) REFERENCES `psicologo` (`ID`),
  ADD CONSTRAINT `atendimento_ibfk_2` FOREIGN KEY (`Paciente`) REFERENCES `paciente` (`ID`);

--
-- Limitadores para a tabela `convenios`
--
ALTER TABLE `convenios`
  ADD CONSTRAINT `convenios_ibfk_1` FOREIGN KEY (`Psicologo`) REFERENCES `psicologo` (`ID`);

--
-- Limitadores para a tabela `paciente`
--
ALTER TABLE `paciente`
  ADD CONSTRAINT `paciente_ibfk_1` FOREIGN KEY (`Psicologo`) REFERENCES `psicologo` (`ID`),
  ADD CONSTRAINT `paciente_ibfk_2` FOREIGN KEY (`Convenio`) REFERENCES `convenios` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
