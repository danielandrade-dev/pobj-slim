-- Script de Teste de Queries e Filtros do Projeto POBJ
-- Este script testa todas as principais queries e filtros do sistema

-- ============================================
-- 1. TESTE: Query de Detalhes (FDetalhesRepository)
-- ============================================

-- 1.1. Query base sem filtros (limitado a 10 registros)
SELECT
    COALESCE(det.registro_id, fr.id_contrato) AS registro_id,
    COALESCE(det.competencia, fr.data_realizado) AS data,
    COALESCE(det.competencia, fr.data_realizado) AS competencia,
    YEAR(COALESCE(det.competencia, fr.data_realizado)) AS ano,
    MONTH(COALESCE(det.competencia, fr.data_realizado)) AS mes,
    CASE MONTH(COALESCE(det.competencia, fr.data_realizado))
        WHEN 1 THEN 'Janeiro'
        WHEN 2 THEN 'Fevereiro'
        WHEN 3 THEN 'Março'
        WHEN 4 THEN 'Abril'
        WHEN 5 THEN 'Maio'
        WHEN 6 THEN 'Junho'
        WHEN 7 THEN 'Julho'
        WHEN 8 THEN 'Agosto'
        WHEN 9 THEN 'Setembro'
        WHEN 10 THEN 'Outubro'
        WHEN 11 THEN 'Novembro'
        WHEN 12 THEN 'Dezembro'
    END AS mes_nome,
    est.segmento_id,
    seg.nome AS segmento,
    est.diretoria_id,
    dir.nome AS diretoria_nome,
    est.regional_id AS gerencia_id,
    reg.nome AS gerencia_nome,
    est.agencia_id,
    ag.nome AS agencia_nome,
    CASE 
        WHEN est.cargo_id = 4 THEN fr.funcional
        ELSE NULL
    END AS gerente_id,
    CASE 
        WHEN est.cargo_id = 4 THEN est.nome
        ELSE NULL
    END AS gerente_nome,
    CASE 
        WHEN est.cargo_id = 4 AND est.agencia_id IS NOT NULL THEN (
            SELECT ggestao.funcional 
            FROM d_estrutura AS ggestao
            WHERE ggestao.agencia_id = est.agencia_id
            AND ggestao.cargo_id = 3
            LIMIT 1
        )
        WHEN est.cargo_id = 3 THEN fr.funcional
        ELSE NULL
    END AS gerente_gestao_id,
    CASE 
        WHEN est.cargo_id = 4 AND est.agencia_id IS NOT NULL THEN (
            SELECT ggestao.nome 
            FROM d_estrutura AS ggestao
            WHERE ggestao.agencia_id = est.agencia_id
            AND ggestao.cargo_id = 3
            LIMIT 1
        )
        WHEN est.cargo_id = 3 THEN est.nome
        ELSE NULL
    END AS gerente_gestao_nome,
    prod.familia_id,
    fam.nm_familia AS familia_nome,
    prod.indicador_id AS id_indicador,
    ind.nm_indicador AS ds_indicador,
    prod.subindicador_id AS id_subindicador,
    sub.nm_subindicador AS subindicador,
    prod.peso,
    fr.realizado AS valor_realizado,
    meta.meta_mensal AS valor_meta,
    meta.meta_mensal AS meta_mensal,
    det.canal_venda,
    det.tipo_venda,
    det.condicao_pagamento AS modalidade_pagamento,
    det.dt_vencimento,
    det.dt_cancelamento,
    det.motivo_cancelamento,
    det.status_id
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
LEFT JOIN segmentos AS seg ON seg.id = est.segmento_id
LEFT JOIN diretorias AS dir ON dir.id = est.diretoria_id
LEFT JOIN regionais AS reg ON reg.id = est.regional_id
LEFT JOIN agencias AS ag ON ag.id = est.agencia_id
LEFT JOIN familia AS fam ON fam.id = prod.familia_id
LEFT JOIN indicador AS ind ON ind.id = prod.indicador_id
LEFT JOIN subindicador AS sub ON sub.id = prod.subindicador_id
LEFT JOIN f_meta AS meta
    ON meta.funcional = fr.funcional
    AND meta.produto_id = fr.produto_id
    AND DATE_FORMAT(meta.data_meta, '%Y-%m') = DATE_FORMAT(fr.data_realizado, '%Y-%m')
LEFT JOIN f_detalhes AS det ON det.contrato_id = fr.id_contrato
WHERE 1=1
ORDER BY est.diretoria_id, est.regional_id, est.agencia_id, est.nome, prod.familia_id, prod.indicador_id, prod.subindicador_id, fr.id_contrato
LIMIT 10;

-- 1.2. Query com filtro de segmento
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND est.segmento_id = 1;

-- 1.3. Query com filtro de diretoria
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND est.diretoria_id = 8607;

-- 1.4. Query com filtro de regional
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND est.regional_id = 8486;

-- 1.5. Query com filtro de agência
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND est.agencia_id = 1141;

-- 1.6. Query com filtro de gerente (funcional)
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND fr.funcional = 'i010001';

-- 1.7. Query com filtro de gerente gestão (EXISTS)
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND EXISTS (
    SELECT 1 FROM d_estrutura AS ggestao 
    WHERE ggestao.funcional = 'i020219'
    AND ggestao.cargo_id = 3
    AND ggestao.segmento_id = est.segmento_id
    AND ggestao.diretoria_id = est.diretoria_id
    AND ggestao.regional_id = est.regional_id
    AND ggestao.agencia_id = est.agencia_id
);

-- 1.8. Query com filtro de família
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND prod.familia_id = 1;

-- 1.9. Query com filtro de indicador
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND prod.indicador_id = 1;

-- 1.10. Query com filtro de subindicador
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND prod.subindicador_id = 1;

-- 1.11. Query com filtro de data início
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND fr.data_realizado >= '2024-01-01';

-- 1.12. Query com filtro de data fim
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND fr.data_realizado <= '2024-12-31';

-- 1.13. Query com múltiplos filtros combinados
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1 
AND est.segmento_id = 1
AND prod.familia_id = 1
AND fr.data_realizado >= '2024-01-01'
AND fr.data_realizado <= '2024-12-31';

-- ============================================
-- 2. TESTE: Query de Resumo (ResumoRepository)
-- ============================================

-- 2.1. Query de produtos com subconsultas (sem filtros)
SELECT 
    dp.id,
    dp.familia_id as id_familia,
    f.nm_familia as familia,
    dp.indicador_id as id_indicador,
    i.nm_indicador as indicador,
    dp.subindicador_id as id_subindicador,
    s.nm_subindicador as subindicador,
    dp.metrica,
    dp.peso,
    COALESCE(fm.total_meta, 0) AS meta,
    COALESCE(fr.total_realizado, 0) AS realizado,
    COALESCE(fp.total_pontos, 0) AS pontos,
    COALESCE(fp.total_meta_pontos, 0) AS pontos_meta,
    COALESCE(fr.total_realizado / NULLIF(fm.total_meta, 0), 0) AS ating,
    CASE 
        WHEN COALESCE(fm.total_meta, 0) > 0 
        THEN CASE 
            WHEN COALESCE(fr.total_realizado, 0) / fm.total_meta >= 1 
            THEN 1 
            ELSE 0 
        END
        ELSE 0
    END AS atingido,
    fr.ultima_atualizacao
FROM d_produtos AS dp
LEFT JOIN familia AS f ON f.id = dp.familia_id
LEFT JOIN indicador AS i ON i.id = dp.indicador_id
LEFT JOIN subindicador AS s ON s.id = dp.subindicador_id
LEFT JOIN (
    SELECT 
        m.produto_id, 
        SUM(m.meta_mensal) AS total_meta
    FROM f_meta AS m
    WHERE 1=1
    GROUP BY m.produto_id
) AS fm ON fm.produto_id = dp.id
LEFT JOIN (
    SELECT 
        r.produto_id, 
        SUM(r.realizado) AS total_realizado,
        MAX(r.data_realizado) AS ultima_atualizacao
    FROM f_realizados AS r
    WHERE r.produto_id IS NOT NULL
    GROUP BY r.produto_id
) AS fr ON fr.produto_id = dp.id
LEFT JOIN (
    SELECT 
        p.produto_id, 
        SUM(p.realizado) AS total_pontos,
        SUM(p.meta) AS total_meta_pontos
    FROM f_pontos AS p
    WHERE 1=1
    GROUP BY p.produto_id
) AS fp ON fp.produto_id = dp.id
WHERE 1=1
ORDER BY f.nm_familia ASC, i.nm_indicador ASC, s.nm_subindicador ASC
LIMIT 10;

-- 2.2. Query de produtos com filtro de data
SELECT 
    dp.id,
    dp.familia_id as id_familia,
    f.nm_familia as familia,
    COALESCE(fm.total_meta, 0) AS meta,
    COALESCE(fr.total_realizado, 0) AS realizado
FROM d_produtos AS dp
LEFT JOIN familia AS f ON f.id = dp.familia_id
LEFT JOIN (
    SELECT 
        m.produto_id, 
        SUM(m.meta_mensal) AS total_meta
    FROM f_meta AS m
    WHERE m.data_meta >= '2024-01-01' AND m.data_meta <= '2024-12-31'
    GROUP BY m.produto_id
) AS fm ON fm.produto_id = dp.id
LEFT JOIN (
    SELECT 
        r.produto_id, 
        SUM(r.realizado) AS total_realizado
    FROM f_realizados AS r
    WHERE r.produto_id IS NOT NULL
    AND r.data_realizado >= '2024-01-01' AND r.data_realizado <= '2024-12-31'
    GROUP BY r.produto_id
) AS fr ON fr.produto_id = dp.id
WHERE 1=1
LIMIT 10;

-- 2.3. Query de produtos com filtro de estrutura (agência)
SELECT 
    dp.id,
    dp.familia_id as id_familia,
    f.nm_familia as familia,
    COALESCE(fm.total_meta, 0) AS meta,
    COALESCE(fr.total_realizado, 0) AS realizado
FROM d_produtos AS dp
LEFT JOIN familia AS f ON f.id = dp.familia_id
LEFT JOIN (
    SELECT 
        m.produto_id, 
        SUM(m.meta_mensal) AS total_meta
    FROM f_meta AS m
    INNER JOIN d_estrutura AS e1 ON e1.funcional = m.funcional
    WHERE e1.agencia_id = 1141
    GROUP BY m.produto_id
) AS fm ON fm.produto_id = dp.id
LEFT JOIN (
    SELECT 
        r.produto_id, 
        SUM(r.realizado) AS total_realizado
    FROM f_realizados AS r
    INNER JOIN d_estrutura AS e2 ON e2.funcional = r.funcional
    WHERE r.produto_id IS NOT NULL
    AND e2.agencia_id = 1141
    GROUP BY r.produto_id
) AS fr ON fr.produto_id = dp.id
WHERE 1=1
LIMIT 10;

-- 2.4. Query de produtos com filtro de gerente
SELECT 
    dp.id,
    dp.familia_id as id_familia,
    f.nm_familia as familia,
    COALESCE(fm.total_meta, 0) AS meta,
    COALESCE(fr.total_realizado, 0) AS realizado
FROM d_produtos AS dp
LEFT JOIN familia AS f ON f.id = dp.familia_id
LEFT JOIN (
    SELECT 
        m.produto_id, 
        SUM(m.meta_mensal) AS total_meta
    FROM f_meta AS m
    WHERE m.funcional = 'i010001'
    GROUP BY m.produto_id
) AS fm ON fm.produto_id = dp.id
LEFT JOIN (
    SELECT 
        r.produto_id, 
        SUM(r.realizado) AS total_realizado
    FROM f_realizados AS r
    WHERE r.produto_id IS NOT NULL
    AND r.funcional = 'i010001'
    GROUP BY r.produto_id
) AS fr ON fr.produto_id = dp.id
WHERE 1=1
LIMIT 10;

-- ============================================
-- 3. TESTE: Query de Ranking (FHistoricoRankingPobjRepository)
-- ============================================

-- 3.1. Query de ranking base (sem filtros)
SELECT
    MAX(p.data_realizado) AS data,
    DATE_FORMAT(MAX(p.data_realizado), '%Y-%m') AS competencia,
    CAST(seg.id AS CHAR) AS segmento_id,
    seg.nome AS segmento,
    CAST(dir.id AS CHAR) AS diretoria_id,
    dir.nome AS diretoria_nome,
    CAST(reg.id AS CHAR) AS gerencia_id,
    reg.nome AS gerencia_nome,
    CAST(ag.id AS CHAR) AS agencia_id,
    ag.nome AS agencia_nome,
    CASE 
        WHEN est.cargo_id = 4 THEN est.funcional
        WHEN est.cargo_id = 3 THEN NULL
        ELSE NULL
    END AS gerente_id,
    CASE 
        WHEN est.cargo_id = 4 THEN est.nome
        WHEN est.cargo_id = 3 THEN NULL
        ELSE NULL
    END AS gerente_nome,
    CASE 
        WHEN est.cargo_id = 4 THEN ggestao.funcional
        WHEN est.cargo_id = 3 THEN est.funcional
        ELSE NULL
    END AS gerente_gestao_id,
    CASE 
        WHEN est.cargo_id = 4 THEN ggestao.nome
        WHEN est.cargo_id = 3 THEN est.nome
        ELSE NULL
    END AS gerente_gestao_nome,
    SUM(p.realizado) AS realizado_mensal,
    SUM(p.meta) AS meta_mensal,
    SUM(p.realizado) AS pontos
FROM f_pontos AS p
INNER JOIN d_estrutura AS est
    ON est.funcional = p.funcional
LEFT JOIN segmentos AS seg
    ON seg.id = est.segmento_id
LEFT JOIN diretorias AS dir
    ON dir.id = est.diretoria_id
LEFT JOIN regionais AS reg
    ON reg.id = est.regional_id
LEFT JOIN agencias AS ag
    ON ag.id = est.agencia_id
LEFT JOIN (
    SELECT 
        g1.agencia_id,
        g1.funcional,
        g1.nome
    FROM d_estrutura AS g1
    INNER JOIN (
        SELECT agencia_id, MIN(id) AS min_id
        FROM d_estrutura
        WHERE cargo_id = 3
        AND agencia_id IS NOT NULL
        GROUP BY agencia_id
    ) AS g2 ON g1.id = g2.min_id AND g1.agencia_id = g2.agencia_id
    WHERE g1.cargo_id = 3
) AS ggestao
    ON ggestao.agencia_id = est.agencia_id
WHERE 1=1
GROUP BY est.funcional, seg.id, dir.id, reg.id, ag.id, est.cargo_id, ggestao.funcional, ggestao.nome
ORDER BY pontos DESC, realizado_mensal DESC
LIMIT 10;

-- 3.2. Query de ranking com filtro de segmento
SELECT
    MAX(p.data_realizado) AS data,
    CAST(seg.id AS CHAR) AS segmento_id,
    seg.nome AS segmento,
    SUM(p.realizado) AS pontos
FROM f_pontos AS p
INNER JOIN d_estrutura AS est
    ON est.funcional = p.funcional
LEFT JOIN segmentos AS seg
    ON seg.id = est.segmento_id
WHERE 1=1
AND est.segmento_id = 1
GROUP BY est.funcional, seg.id
ORDER BY pontos DESC
LIMIT 10;

-- 3.3. Query de ranking com filtro de data
SELECT
    MAX(p.data_realizado) AS data,
    DATE_FORMAT(MAX(p.data_realizado), '%Y-%m') AS competencia,
    SUM(p.realizado) AS pontos
FROM f_pontos AS p
INNER JOIN d_estrutura AS est
    ON est.funcional = p.funcional
WHERE 1=1
AND p.data_realizado >= '2024-01-01'
AND p.data_realizado <= '2024-12-31'
GROUP BY est.funcional
ORDER BY pontos DESC
LIMIT 10;

-- 3.4. Query de ranking com filtro de gerente
SELECT
    MAX(p.data_realizado) AS data,
    est.funcional AS gerente_id,
    est.nome AS gerente_nome,
    SUM(p.realizado) AS pontos
FROM f_pontos AS p
INNER JOIN d_estrutura AS est
    ON est.funcional = p.funcional
WHERE 1=1
AND est.funcional = 'i010001'
GROUP BY est.funcional, est.nome
ORDER BY pontos DESC;

-- 3.5. Query de ranking com filtro de gerente gestão (EXISTS)
SELECT
    MAX(p.data_realizado) AS data,
    SUM(p.realizado) AS pontos
FROM f_pontos AS p
INNER JOIN d_estrutura AS est
    ON est.funcional = p.funcional
WHERE 1=1
AND EXISTS (
    SELECT 1 FROM d_estrutura AS ggestao 
    WHERE ggestao.funcional = 'i020219'
    AND ggestao.cargo_id = 3
    AND ggestao.segmento_id = est.segmento_id
    AND ggestao.diretoria_id = est.diretoria_id
    AND ggestao.regional_id = est.regional_id
    AND ggestao.agencia_id = est.agencia_id
)
GROUP BY est.funcional
ORDER BY pontos DESC
LIMIT 10;

-- ============================================
-- 4. TESTE: Query de Variável (ResumoRepository::findVariavel)
-- ============================================

-- 4.1. Query de variável base (sem filtros)
SELECT 
    v.id as registro_id,
    v.funcional,
    v.meta as variavel_meta,
    v.variavel as variavel_real,
    c.data as dt_atualizacao,
    c.data as data,
    c.data as competencia,
    e.nome as nome_funcional,
    seg.nome as segmento,
    CAST(e.segmento_id AS CHAR) as segmento_id,
    d.nome as diretoria_nome,
    CAST(e.diretoria_id AS CHAR) as diretoria_id,
    r.nome as regional_nome,
    CAST(e.regional_id AS CHAR) as gerencia_id,
    a.nome as agencia_nome,
    CAST(e.agencia_id AS CHAR) as agencia_id
FROM f_variavel AS v
LEFT JOIN d_calendario AS c ON c.data = v.dt_atualizacao
LEFT JOIN d_estrutura AS e ON e.funcional = v.funcional
LEFT JOIN segmentos AS seg ON seg.id = e.segmento_id
LEFT JOIN diretorias AS d ON d.id = e.diretoria_id
LEFT JOIN regionais AS r ON r.id = e.regional_id
LEFT JOIN agencias AS a ON a.id = e.agencia_id
WHERE 1=1
ORDER BY c.data DESC
LIMIT 10;

-- 4.2. Query de variável com filtro de estrutura
SELECT 
    v.id as registro_id,
    v.funcional,
    v.meta as variavel_meta,
    v.variavel as variavel_real,
    c.data as data
FROM f_variavel AS v
LEFT JOIN d_calendario AS c ON c.data = v.dt_atualizacao
LEFT JOIN d_estrutura AS e ON e.funcional = v.funcional
WHERE 1=1
AND e.agencia_id = 1141
ORDER BY c.data DESC
LIMIT 10;

-- 4.3. Query de variável com filtro de data
SELECT 
    v.id as registro_id,
    v.funcional,
    v.meta as variavel_meta,
    v.variavel as variavel_real,
    c.data as data
FROM f_variavel AS v
LEFT JOIN d_calendario AS c ON c.data = v.dt_atualizacao
WHERE 1=1
AND c.data >= '2024-01-01'
AND c.data <= '2024-12-31'
ORDER BY c.data DESC
LIMIT 10;

-- ============================================
-- 5. TESTE: Validação de Integridade dos Dados
-- ============================================

-- 5.1. Verificar se há registros em f_realizados
SELECT COUNT(*) as total_realizados FROM f_realizados;

-- 5.2. Verificar se há registros em f_meta
SELECT COUNT(*) as total_metas FROM f_meta;

-- 5.3. Verificar se há registros em f_pontos
SELECT COUNT(*) as total_pontos FROM f_pontos;

-- 5.4. Verificar se há registros em d_estrutura
SELECT COUNT(*) as total_estrutura FROM d_estrutura;

-- 5.5. Verificar se há registros em d_produtos
SELECT COUNT(*) as total_produtos FROM d_produtos;

-- 5.6. Verificar relacionamentos entre f_realizados e d_estrutura
SELECT 
    COUNT(*) as total,
    COUNT(DISTINCT fr.funcional) as funcionais_distintos,
    COUNT(DISTINCT est.funcional) as funcionais_com_estrutura
FROM f_realizados AS fr
LEFT JOIN d_estrutura AS est ON est.funcional = fr.funcional;

-- 5.7. Verificar relacionamentos entre f_realizados e d_produtos
SELECT 
    COUNT(*) as total,
    COUNT(DISTINCT fr.produto_id) as produtos_distintos,
    COUNT(DISTINCT prod.id) as produtos_com_dados
FROM f_realizados AS fr
LEFT JOIN d_produtos AS prod ON prod.id = fr.produto_id;

-- 5.8. Verificar cargos na estrutura
SELECT 
    cargo_id,
    COUNT(*) as total
FROM d_estrutura
GROUP BY cargo_id
ORDER BY cargo_id;

-- 5.9. Verificar hierarquia de estrutura (segmento -> diretoria -> regional -> agência)
SELECT 
    seg.id as segmento_id,
    seg.nome as segmento,
    dir.id as diretoria_id,
    dir.nome as diretoria,
    reg.id as regional_id,
    reg.nome as regional,
    ag.id as agencia_id,
    ag.nome as agencia,
    COUNT(DISTINCT est.funcional) as total_funcionais
FROM d_estrutura AS est
LEFT JOIN segmentos AS seg ON seg.id = est.segmento_id
LEFT JOIN diretorias AS dir ON dir.id = est.diretoria_id
LEFT JOIN regionais AS reg ON reg.id = est.regional_id
LEFT JOIN agencias AS ag ON ag.id = est.agencia_id
GROUP BY seg.id, seg.nome, dir.id, dir.nome, reg.id, reg.nome, ag.id, ag.nome
ORDER BY seg.id, dir.id, reg.id, ag.id
LIMIT 20;

-- 5.10. Verificar hierarquia de produtos (familia -> indicador -> subindicador)
SELECT 
    fam.id as familia_id,
    fam.nm_familia as familia,
    ind.id as indicador_id,
    ind.nm_indicador as indicador,
    sub.id as subindicador_id,
    sub.nm_subindicador as subindicador,
    COUNT(DISTINCT prod.id) as total_produtos
FROM d_produtos AS prod
LEFT JOIN familia AS fam ON fam.id = prod.familia_id
LEFT JOIN indicador AS ind ON ind.id = prod.indicador_id
LEFT JOIN subindicador AS sub ON sub.id = prod.subindicador_id
GROUP BY fam.id, fam.nm_familia, ind.id, ind.nm_indicador, sub.id, sub.nm_subindicador
ORDER BY fam.id, ind.id, sub.id
LIMIT 20;

