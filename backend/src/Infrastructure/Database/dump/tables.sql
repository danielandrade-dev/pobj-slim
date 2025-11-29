-- cargos definição

CREATE TABLE `cargos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- d_calendario definição

CREATE TABLE `d_calendario` (
  `data` date NOT NULL,
  `ano` int(11) NOT NULL,
  `mes` tinyint(4) NOT NULL,
  `mes_nome` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `dia` tinyint(4) NOT NULL,
  `dia_da_semana` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `semana` tinyint(4) NOT NULL,
  `trimestre` tinyint(4) NOT NULL,
  `semestre` tinyint(4) NOT NULL,
  `eh_dia_util` tinyint(1) NOT NULL,
  PRIMARY KEY (`data`),
  KEY `idx_d_calendario_mes` (`ano`,`mes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- d_status_indicadores definição

CREATE TABLE `d_status_indicadores` (
  `id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_d_status_nome` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- familia definição

CREATE TABLE `familia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nm_familia` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_familia` (`nm_familia`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- omega_departamentos definição

CREATE TABLE `omega_departamentos` (
  `departamento` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `departamento_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `ordem_departamento` int(11) DEFAULT NULL,
  `tipo` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `ordem_tipo` int(11) DEFAULT NULL,
  PRIMARY KEY (`departamento_id`),
  UNIQUE KEY `uq_omega_departamento_nome_tipo` (`departamento`,`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- omega_usuarios definição

CREATE TABLE `omega_usuarios` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `nome` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `funcional` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cargo` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usuario` tinyint(1) DEFAULT '1',
  `analista` tinyint(1) DEFAULT '0',
  `supervisor` tinyint(1) DEFAULT '0',
  `admin` tinyint(1) DEFAULT '0',
  `encarteiramento` tinyint(1) DEFAULT '0',
  `meta` tinyint(1) DEFAULT '0',
  `orcamento` tinyint(1) DEFAULT '0',
  `pobj` tinyint(1) DEFAULT '0',
  `matriz` tinyint(1) DEFAULT '0',
  `outros` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- segmentos definição

CREATE TABLE `segmentos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- diretorias definição

CREATE TABLE `diretorias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `segmento_id` int(10) unsigned NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_diretoria_segmento_nome` (`segmento_id`,`nome`),
  CONSTRAINT `fk_diretorias_segmento` FOREIGN KEY (`segmento_id`) REFERENCES `segmentos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8608 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- f_campanhas definição

CREATE TABLE `f_campanhas` (
  `campanha_id` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `sprint_id` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `diretoria_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `diretoria_nome` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `gerencia_regional_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `regional_nome` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `agencia_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `agencia_nome` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `gerente_gestao_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gerente_gestao_nome` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gerente_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gerente_nome` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `segmento` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `segmento_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `familia_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `id_indicador` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `ds_indicador` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `subproduto` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_subindicador` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `carteira` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `linhas` decimal(18,2) DEFAULT NULL,
  `cash` decimal(18,2) DEFAULT NULL,
  `conquista` decimal(18,2) DEFAULT NULL,
  `atividade` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` date NOT NULL,
  `familia_codigo` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `indicador_codigo` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subindicador_codigo` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`campanha_id`),
  KEY `idx_f_campanhas_data` (`data`),
  KEY `idx_f_campanhas_diretoria` (`diretoria_id`),
  KEY `idx_f_campanhas_gerencia` (`gerencia_regional_id`),
  KEY `idx_f_campanhas_indicador` (`id_indicador`),
  KEY `idx_f_campanhas_unidade` (`segmento_id`(20),`diretoria_id`(20),`gerencia_regional_id`(20),`agencia_id`(20)),
  KEY `fk_campanhas_produtos` (`id_indicador`,`id_subindicador`),
  CONSTRAINT `fk_campanhas_calendario_data` FOREIGN KEY (`data`) REFERENCES `d_calendario` (`data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- f_leads_propensos definição

CREATE TABLE `f_leads_propensos` (
  `database` date NOT NULL,
  `nome_empresa` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `cnae` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `segmento_cliente` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `segmento_cliente_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `produto_propenso` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `familia_produto_propenso` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `secao_produto_propenso` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_indicador` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_subindicador` varchar(80) COLLATE utf8_unicode_ci DEFAULT '0',
  `data_contato` date DEFAULT NULL,
  `comentario` text COLLATE utf8_unicode_ci,
  `responsavel_contato` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `diretoria_cliente` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `diretoria_cliente_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `regional_cliente` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `regional_cliente_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agencia_cliente` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agencia_cliente_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gerente_gestao_cliente` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gerente_gestao_cliente_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gerente_cliente` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gerente_cliente_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credito_pre_aprovado` decimal(18,2) DEFAULT NULL,
  `origem_lead` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`database`,`nome_empresa`(190)),
  KEY `idx_f_leads_calendario` (`database`),
  KEY `idx_f_leads_contato` (`data_contato`),
  KEY `idx_f_leads_unidade` (`segmento_cliente_id`,`diretoria_cliente_id`,`regional_cliente_id`,`agencia_cliente_id`),
  KEY `idx_f_leads_produto` (`id_indicador`,`id_subindicador`),
  KEY `idx_f_leads_diretoria` (`diretoria_cliente_id`),
  KEY `idx_f_leads_regional` (`regional_cliente_id`),
  CONSTRAINT `fk_leads_calendario_base` FOREIGN KEY (`database`) REFERENCES `d_calendario` (`data`) ON DELETE CASCADE,
  CONSTRAINT `fk_leads_calendario_contato` FOREIGN KEY (`data_contato`) REFERENCES `d_calendario` (`data`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- indicador definição

CREATE TABLE `indicador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nm_indicador` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `familia_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_indicador` (`nm_indicador`),
  KEY `indicador_familia_FK` (`familia_id`),
  CONSTRAINT `indicador_familia_FK` FOREIGN KEY (`familia_id`) REFERENCES `familia` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- omega_status definição

CREATE TABLE `omega_status` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `tone` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'neutral',
  `descricao` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL,
  `departamento_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_omega_status_departamento` (`departamento_id`),
  CONSTRAINT `fk_omega_status_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `omega_departamentos` (`departamento_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- regionais definição

CREATE TABLE `regionais` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `diretoria_id` int(10) unsigned NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_regional_diretoria_nome` (`diretoria_id`,`nome`),
  CONSTRAINT `fk_regionais_diretoria` FOREIGN KEY (`diretoria_id`) REFERENCES `diretorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8487 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- subindicador definição

CREATE TABLE `subindicador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `indicador_id` int(11) NOT NULL,
  `nm_subindicador` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_indicador_sub` (`indicador_id`,`nm_subindicador`),
  CONSTRAINT `subindicador_ibfk_1` FOREIGN KEY (`indicador_id`) REFERENCES `indicador` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- agencias definição

CREATE TABLE `agencias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `regional_id` int(10) unsigned NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `porte` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_agencia_regional_nome` (`regional_id`,`nome`),
  CONSTRAINT `fk_agencias_regional` FOREIGN KEY (`regional_id`) REFERENCES `regionais` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1268 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- d_estrutura definição

CREATE TABLE `d_estrutura` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `funcional` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cargo_id` int(10) unsigned NOT NULL,
  `segmento_id` int(10) unsigned DEFAULT NULL,
  `diretoria_id` int(10) unsigned DEFAULT NULL,
  `regional_id` int(10) unsigned DEFAULT NULL,
  `agencia_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `funcional` (`funcional`),
  KEY `fk_estrutura_cargo` (`cargo_id`),
  KEY `fk_estrutura_segmento` (`segmento_id`),
  KEY `fk_estrutura_diretoria` (`diretoria_id`),
  KEY `fk_estrutura_regional` (`regional_id`),
  KEY `fk_estrutura_agencia` (`agencia_id`),
  CONSTRAINT `fk_estrutura_agencia` FOREIGN KEY (`agencia_id`) REFERENCES `agencias` (`id`),
  CONSTRAINT `fk_estrutura_cargo` FOREIGN KEY (`cargo_id`) REFERENCES `cargos` (`id`),
  CONSTRAINT `fk_estrutura_diretoria` FOREIGN KEY (`diretoria_id`) REFERENCES `diretorias` (`id`),
  CONSTRAINT `fk_estrutura_regional` FOREIGN KEY (`regional_id`) REFERENCES `regionais` (`id`),
  CONSTRAINT `fk_estrutura_segmento` FOREIGN KEY (`segmento_id`) REFERENCES `segmentos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- d_produtos definição

CREATE TABLE `d_produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `familia_id` int(11) NOT NULL,
  `indicador_id` int(11) NOT NULL,
  `subindicador_id` int(11) DEFAULT NULL,
  `peso` decimal(10,2) NOT NULL DEFAULT '0.00',
  `metrica` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'valor',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_indicador_sub` (`indicador_id`,`subindicador_id`),
  KEY `idx_familia` (`familia_id`),
  KEY `idx_indicador` (`indicador_id`),
  KEY `idx_subindicador` (`subindicador_id`),
  CONSTRAINT `d_produtos_ibfk_1` FOREIGN KEY (`familia_id`) REFERENCES `familia` (`id`),
  CONSTRAINT `d_produtos_ibfk_2` FOREIGN KEY (`indicador_id`) REFERENCES `indicador` (`id`),
  CONSTRAINT `d_produtos_ibfk_3` FOREIGN KEY (`subindicador_id`) REFERENCES `subindicador` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- f_detalhes definição

CREATE TABLE `f_detalhes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contrato_id` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `registro_id` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `funcional` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `id_produto` int(11) NOT NULL,
  `dt_cadastro` date NOT NULL,
  `competencia` date NOT NULL,
  `valor_meta` decimal(18,2) DEFAULT NULL,
  `valor_realizado` decimal(18,2) DEFAULT NULL,
  `quantidade` decimal(18,4) DEFAULT NULL,
  `peso` decimal(18,4) DEFAULT NULL,
  `pontos` decimal(18,4) DEFAULT NULL,
  `dt_vencimento` date DEFAULT NULL,
  `dt_cancelamento` date DEFAULT NULL,
  `motivo_cancelamento` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `canal_venda` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tipo_venda` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `condicao_pagamento` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_fd_contrato` (`contrato_id`),
  KEY `idx_fd_funcional` (`funcional`),
  KEY `idx_fd_registro` (`registro_id`),
  KEY `idx_fd_produto` (`id_produto`),
  KEY `idx_fd_dt_cadastro` (`dt_cadastro`),
  KEY `idx_fd_competencia` (`competencia`),
  KEY `fk_fd_status` (`status_id`),
  CONSTRAINT `fk_fd_comp` FOREIGN KEY (`competencia`) REFERENCES `d_calendario` (`data`) ON UPDATE CASCADE,
  CONSTRAINT `fk_fd_dt_cadastro` FOREIGN KEY (`dt_cadastro`) REFERENCES `d_calendario` (`data`) ON UPDATE CASCADE,
  CONSTRAINT `fk_fd_estrutura` FOREIGN KEY (`funcional`) REFERENCES `d_estrutura` (`funcional`) ON UPDATE CASCADE,
  CONSTRAINT `fk_fd_produto` FOREIGN KEY (`id_produto`) REFERENCES `d_produtos` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_fd_status` FOREIGN KEY (`status_id`) REFERENCES `d_status_indicadores` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- f_historico_ranking_pobj definição

CREATE TABLE `f_historico_ranking_pobj` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `data` date NOT NULL,
  `funcional` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `grupo` int(11) DEFAULT NULL,
  `ranking` int(11) DEFAULT NULL,
  `realizado` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_hist_data` (`data`),
  KEY `idx_hist_funcional` (`funcional`),
  KEY `idx_hist_ranking` (`ranking`),
  CONSTRAINT `fk_hist_pobj_calendario` FOREIGN KEY (`data`) REFERENCES `d_calendario` (`data`) ON UPDATE CASCADE,
  CONSTRAINT `fk_hist_pobj_estrutura` FOREIGN KEY (`funcional`) REFERENCES `d_estrutura` (`funcional`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- f_meta definição

CREATE TABLE `f_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `data_meta` date NOT NULL,
  `funcional` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `produto_id` int(11) NOT NULL,
  `meta_mensal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ix_meta_data` (`data_meta`),
  KEY `ix_meta_func_data` (`funcional`,`data_meta`),
  KEY `fk_f_meta__produto` (`produto_id`),
  CONSTRAINT `fk_f_meta__d_estrutura` FOREIGN KEY (`funcional`) REFERENCES `d_estrutura` (`funcional`),
  CONSTRAINT `fk_f_meta__produto` FOREIGN KEY (`produto_id`) REFERENCES `d_produtos` (`id`),
  CONSTRAINT `fk_fm_cal` FOREIGN KEY (`data_meta`) REFERENCES `d_calendario` (`data`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- f_pontos definição

CREATE TABLE `f_pontos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `funcional` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `produto_id` int(11) NOT NULL,
  `meta` decimal(18,2) NOT NULL DEFAULT '0.00',
  `realizado` decimal(18,2) NOT NULL DEFAULT '0.00',
  `data_realizado` date DEFAULT NULL,
  `dt_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fp_funcional` (`funcional`),
  KEY `idx_fp_produto` (`produto_id`),
  KEY `idx_fp_data_realizado` (`data_realizado`),
  CONSTRAINT `fk_fpontos_calendario` FOREIGN KEY (`data_realizado`) REFERENCES `d_calendario` (`data`) ON UPDATE CASCADE,
  CONSTRAINT `fk_fpontos_estrutura` FOREIGN KEY (`funcional`) REFERENCES `d_estrutura` (`funcional`) ON UPDATE CASCADE,
  CONSTRAINT `fk_fpontos_produto` FOREIGN KEY (`produto_id`) REFERENCES `d_produtos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- f_realizados definição

CREATE TABLE `f_realizados` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_contrato` char(10) COLLATE utf8_unicode_ci NOT NULL,
  `funcional` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `data_realizado` date NOT NULL,
  `realizado` decimal(18,2) NOT NULL DEFAULT '0.00',
  `produto_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fr_data` (`data_realizado`),
  KEY `idx_fr_func_data` (`funcional`,`data_realizado`),
  KEY `idx_fr_contrato` (`id_contrato`),
  KEY `idx_fr_produto` (`produto_id`),
  CONSTRAINT `fk_fr_calendario` FOREIGN KEY (`data_realizado`) REFERENCES `d_calendario` (`data`) ON UPDATE CASCADE,
  CONSTRAINT `fk_fr_estrutura` FOREIGN KEY (`funcional`) REFERENCES `d_estrutura` (`funcional`) ON UPDATE CASCADE,
  CONSTRAINT `fk_fr_produto` FOREIGN KEY (`produto_id`) REFERENCES `d_produtos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- f_variavel definição

CREATE TABLE `f_variavel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `funcional` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `meta` decimal(18,2) NOT NULL DEFAULT '0.00',
  `variavel` decimal(18,2) NOT NULL DEFAULT '0.00',
  `dt_atualizacao` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fv_funcional` (`funcional`),
  KEY `idx_fv_dt` (`dt_atualizacao`),
  CONSTRAINT `fk_f_variavel_calendario` FOREIGN KEY (`dt_atualizacao`) REFERENCES `d_calendario` (`data`) ON UPDATE CASCADE,
  CONSTRAINT `fk_f_variavel_estrutura` FOREIGN KEY (`funcional`) REFERENCES `d_estrutura` (`funcional`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- omega_chamados definição

CREATE TABLE `omega_chamados` (
  `id` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_id` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_label` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `family` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `section` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `queue` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `priority` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `opened` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `requester_id` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_id` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `team_id` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `history` longtext COLLATE utf8_unicode_ci,
  `diretoria` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gerencia` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agencia` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gerente_gestao` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gerente` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attachment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_omega_chamados_status` (`status`),
  KEY `idx_omega_chamados_team` (`team_id`),
  KEY `idx_omega_chamados_requester` (`requester_id`),
  KEY `idx_omega_chamados_owner` (`owner_id`),
  CONSTRAINT `fk_omega_chamados_owner` FOREIGN KEY (`owner_id`) REFERENCES `omega_usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_omega_chamados_requester` FOREIGN KEY (`requester_id`) REFERENCES `omega_usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_omega_chamados_status` FOREIGN KEY (`status`) REFERENCES `omega_status` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_omega_chamados_team` FOREIGN KEY (`team_id`) REFERENCES `omega_departamentos` (`departamento_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;