-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 01-Set-2023 às 11:27
-- Versão do servidor: 5.7.23-23
-- versão do PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `hgsys947_systempsi`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `agendar`
--

CREATE TABLE `agendar` (
  `Psicologo` int(11) NOT NULL,
  `Paciente` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `Data_Inicio` datetime NOT NULL,
  `Data_Fim` datetime NOT NULL,
  `Motivo` text,
  `OBS` text,
  `Valor` int(11) NOT NULL,
  `Realizado` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `agendar`
--

INSERT INTO `agendar` (`Psicologo`, `Paciente`, `ID`, `Data_Inicio`, `Data_Fim`, `Motivo`, `OBS`, `Valor`, `Realizado`) VALUES
(1, 207, 84, '2023-07-21 19:00:00', '2023-07-21 19:45:00', 'Acompanhamento ', '', 50, 1),
(2, 206, 88, '2023-07-25 13:00:00', '2023-07-25 13:45:00', 'Acompanhamento quinzenal.', 'Via Google Meet.', 100, 1),
(2, 206, 92, '2023-07-28 17:00:00', '2023-07-28 17:45:00', 'Acompanhamento psicológico.', 'Via WhatsApp.', 100, 1),
(2, 209, 93, '2023-07-28 09:30:00', '2023-07-28 10:15:00', 'Acompanhamento Psicológico', '', 100, 1),
(2, 212, 96, '2023-07-28 14:00:00', '2023-07-28 14:45:00', 'Acompanhamento.', 'Via Meet.', 50, 1),
(1, 204, 97, '2023-07-31 20:00:00', '2023-07-31 20:45:00', '', 'Acompanhamento ', 90, 1),
(1, 200, 99, '2023-08-01 20:00:00', '2023-08-01 20:45:00', 'Acompanhamento ', '', 90, 1),
(1, 200, 101, '2023-08-21 20:00:00', '2023-08-21 20:45:00', 'Acompanhamento', '', 70, 1),
(1, 207, 102, '2023-08-11 19:00:00', '2023-08-11 19:45:00', 'Acompanhamento ', 'Atebdimento social ', 0, 0),
(2, 209, 103, '2023-08-06 11:30:00', '2023-08-06 12:15:00', 'Teste - modos de pagamento', '', 100, 1),
(2, 209, 104, '2023-08-09 18:40:00', '2023-08-09 19:25:00', 'Teste envio de e-mail novo domínio.', '', 100, 1),
(2, 209, 105, '2023-08-11 15:00:00', '2023-08-11 15:45:00', '', '', 100, 0),
(2, 206, 109, '2023-08-17 23:30:00', '2023-08-18 00:15:00', '', '', 100, 1),
(2, 206, 110, '2023-08-17 23:50:00', '2023-08-18 00:35:00', '', '', 100, 1),
(2, 206, 112, '2023-08-30 15:00:00', '2023-08-30 15:45:00', 'Teste', 'teste', 200, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `anexos`
--

CREATE TABLE `anexos` (
  `ID` int(11) NOT NULL,
  `Nome` varchar(200) NOT NULL,
  `Anexo` varchar(200) NOT NULL,
  `Paciente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `anexos`
--

INSERT INTO `anexos` (`ID`, `Nome`, `Anexo`, `Paciente`) VALUES
(52, 'Screenshot_20230804-145740_WhatsAppBusiness.jpg - 207.jpg', './anexosPacientes/Screenshot_20230804-145740_WhatsAppBusiness.jpg - 207.jpg', 207),
(53, 'Screenshot_20230804-145747_WhatsAppBusiness.jpg - 200.jpg', './anexosPacientes/Screenshot_20230804-145747_WhatsAppBusiness.jpg - 200.jpg', 200);

-- --------------------------------------------------------

--
-- Estrutura da tabela `atendimento`
--

CREATE TABLE `atendimento` (
  `ID` int(11) NOT NULL,
  `Data_Inicio` datetime NOT NULL,
  `Data_Fim` datetime NOT NULL,
  `Valor` varchar(8) NOT NULL,
  `Motivo` varchar(500) DEFAULT NULL,
  `Forma_Pgto` varchar(50) NOT NULL,
  `OBS` varchar(1000) DEFAULT NULL,
  `Registro` longtext,
  `Psicologo` int(11) NOT NULL,
  `Paciente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `atendimento`
--

INSERT INTO `atendimento` (`ID`, `Data_Inicio`, `Data_Fim`, `Valor`, `Motivo`, `Forma_Pgto`, `OBS`, `Registro`, `Psicologo`, `Paciente`) VALUES
(55, '2023-07-21 22:07:51', '2023-07-21 22:07:57', '50', 'Acompanhamento ', 'Dinheiro', '', '', 1, 207),
(59, '2023-07-25 17:37:47', '2023-07-25 17:38:24', '100', 'Acompanhamento quinzenal.', 'PIX', 'Via Google Meet.', 'Paciente apresentou evolução em tal aspecto.\r\nRecomenda-se fazer isto.', 2, 206),
(61, '2023-07-28 12:36:48', '2023-07-28 12:37:35', '100', 'Acompanhamento Psicológico', 'Transferência Bancária', 'O paciente atrasou 5 minutos. O atendimento ocorreu via WhatsApp.', 'Paciente mostrou evolução no seu quadro clínico.', 2, 209),
(62, '2023-07-28 13:16:25', '2023-07-28 13:18:12', '50', 'Acompanhamento.', 'PIX', 'Via Meet.', '', 2, 212),
(63, '2023-07-28 13:18:46', '2023-07-28 13:18:50', '100', 'Acompanhamento psicológico.', 'Dinheiro', 'Via WhatsApp.', '', 2, 206),
(64, '2023-07-31 21:07:37', '2023-07-31 21:12:31', '90', 'Acompanhamento por fobia social.', 'Dinheiro', 'Acompanhamento ', 'Paciente mostrou-se mais ativo na sessão, falou cerca de 70% da sessão. Conseguiu elaborar questões sobre relacionamento, futuros relacionamentos. Queixar-se da sua inabilidade social, dificuldade em encontrar novas pessoas e relacionamentos afetivos. \r\nEstá trabalhando e frequentando a academia. ', 1, 204),
(65, '2023-08-01 20:54:13', '2023-08-01 21:00:15', '90', 'Acompanhamento ', 'Dinheiro', '', 'Paciente relata estar em um relacionamento com um companheiro mais novo. Relatou sobre as críticas que recebeu da irmã a respeito do relacionamento. Falou sobre os conflitos com a mãe e suas atitudes da infância. Falou dos seus traumas infantis. E encerramos a sessão. ', 1, 200),
(66, '2023-08-06 11:25:47', '2023-08-06 11:25:53', '100', 'Teste - modos de pagamento', 'PIX', '', '', 2, 209),
(67, '2023-08-09 18:37:37', '2023-08-09 18:37:57', '100', 'Teste envio de e-mail novo domínio.', 'PIX', '', 'E-mail não funcionou.', 2, 209),
(68, '2023-08-17 23:30:27', '2023-08-17 23:30:41', '100', 'ew', 'PIX', 'we', 'Testen2', 2, 206),
(69, '2023-08-21 21:17:30', '2023-08-21 21:19:08', '70', 'Acompanhamento', 'PIX', '', 'Paciente reclamou que tem dificuldades sexuais. Falo-se sobre os traumas da infância, abuso sexual infantil e como isso afeta sua vida sexualmente nos dias atuais. ', 1, 200),
(70, '2023-08-23 09:05:46', '2023-08-23 09:06:14', '350', 'sono', 'PIX', 'ok', 'tudo certo', 2, 219);

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
(15, 'Particular', '500', 15),
(19, 'Particular', '100', 2),
(22, 'Particular', '', 17);

-- --------------------------------------------------------

--
-- Estrutura da tabela `paciente`
--

CREATE TABLE `paciente` (
  `ID` int(11) NOT NULL,
  `Nome` varchar(60) NOT NULL,
  `Telefone` varchar(15) NOT NULL,
  `Email` varchar(60) DEFAULT NULL,
  `Data_Nascimento` varchar(10) DEFAULT NULL,
  `Convenio` int(11) NOT NULL,
  `Foto` varchar(500) DEFAULT NULL,
  `Genero` varchar(20) DEFAULT NULL,
  `CPF` varchar(15) NOT NULL,
  `Psicologo` int(11) NOT NULL,
  `Prontuario` text,
  `Endereco` text,
  `Situacao` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `paciente`
--

INSERT INTO `paciente` (`ID`, `Nome`, `Telefone`, `Email`, `Data_Nascimento`, `Convenio`, `Foto`, `Genero`, `CPF`, `Psicologo`, `Prontuario`, `Endereco`, `Situacao`) VALUES
(200, 'Maria', '(19) 99680-1384', '', '2023-06-10', 12, 'fotosPacientes/paciente_64ce525b67a54.jpg', 'Feminino', '111.111.111-11', 1, 'Paciente remascente da Pandemia. \r\nQueixa principal: dificuldade em manter relacionamentos e feridas narcísicas.', 'Interior de São Paulo', 1),
(204, 'Wellerson', '(31) 99917-8603', 'wellerson@paciente.com', '2023-06-30', 12, 'fotosPacientes/paciente_64cd3c7d7ac4c.jpg', 'Masculino', '222.222.222-22', 1, 'Fobia Social', 'Santa Maria, Rio Grande do Sul', 1),
(206, 'Maria de Fátima', '(48) 99898-5652', 'mariadefatima@fatima.com', '1995-06-25', 19, 'fotosPacientes/paciente_64c030682215a.avif', 'Feminino', '598.655.525-15', 2, 'Paciente remanescente dos atendimentos do CRAS.\r\nTem ansiedade e depressão. Mora sozinha. Sem contato com os filhos por motivos familiares.', 'Rua do Limoeiro', 1),
(207, 'Bianca', '(54) 98706-5487', '', '2023-07-03', 12, 'fotosPacientes/paciente_64ce5245b254d.jpg', 'Feminino', '876.980.432-78', 1, 'Paciente oriunda da Pandemia. ', 'Vacaria - Rio Grande do Sul', 1),
(209, 'João da Silva', '(51) 99282-5316', 'joao@silva.com', '1995-02-23', 19, 'fotosPacientes/paciente_64cfa5258fd90.jpg', 'Masculino', '255.255.255-22', 2, 'Sofre de depressão.', 'Rua do Limoeiro', 1),
(212, 'Sandra Vieira', '(48) 99945-1160', '', '1972-11-01', 19, './fotosPacientes/paciente_64c3e73bb2b28.jpg', 'Feminino', '022.333.333-33', 2, '', '', 0),
(219, 'armando mendes neto', '(48) 99955-4994', 'armando@gmail.com', '1982-11-30', 19, NULL, 'Masculino', '555.444.333-22', 2, 'muito saudável, mas está dormindo pouco ultimamente', 'rua do IFC', 0),
(220, 'Teste Arquivamento', '(22) 2222', '2222@222', '5555-05-05', 19, NULL, 'Masculino', '555.555', 2, '', '2222', 0);

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
(1, 'Tainá Fiabani', '1442b1b14603ec230cafa95ed6ecb840', 'tainafiabani@gmail.com', '(54) 99665-2072', '07/29977'),
(2, 'Luís Fiabani', 'b1890ca342dec1e0a4aaccacb94f34a7', 'luisfiabani@gmail.com', '(51) 99282-5316', '00/33333'),
(15, 'Teste', 'e10adc3949ba59abbe56e057f20f883e', 'teste@teste.com', '000000', '0000000'),
(17, 'Metodologia', '9db643346c109c9516cafc41d51bee1f', 'metodologia@ifc.edu.br', '(48) 98814-1446', '00000/00');

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
-- Índices para tabela `anexos`
--
ALTER TABLE `anexos`
  ADD PRIMARY KEY (`ID`),
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
  ADD UNIQUE KEY `CPF` (`CPF`),
  ADD KEY `Psicologo` (`Psicologo`),
  ADD KEY `Convenio` (`Convenio`),
  ADD KEY `Nome` (`Nome`);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT de tabela `anexos`
--
ALTER TABLE `anexos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de tabela `atendimento`
--
ALTER TABLE `atendimento`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de tabela `convenios`
--
ALTER TABLE `convenios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `paciente`
--
ALTER TABLE `paciente`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT de tabela `psicologo`
--
ALTER TABLE `psicologo`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
-- Limitadores para a tabela `anexos`
--
ALTER TABLE `anexos`
  ADD CONSTRAINT `anexos_ibfk_1` FOREIGN KEY (`Paciente`) REFERENCES `paciente` (`ID`);

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
