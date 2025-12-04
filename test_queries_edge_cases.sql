-- Script de Teste de Casos Extremos e Validação de Robustez
-- Este script testa cenários limites e validações adicionais

-- ============================================
-- 1. TESTE: Validação de Filtros Vazios/Nulos
-- ============================================

-- 1.1. Query com filtros NULL (deve retornar todos os registros)
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1
AND (NULL IS NULL OR est.segmento_id = NULL)
AND (NULL IS NULL OR prod.familia_id = NULL);

-- 1.2. Query com strings vazias (deve ignorar o filtro)
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
WHERE 1=1
AND ('' = '' OR est.segmento_id = '');

-- ============================================
-- 2. TESTE: Validação de Hierarquia de Filtros
-- ============================================

-- 2.1. Verificar se filtro de gerente ignora outros filtros de estrutura
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
WHERE 1=1
AND fr.funcional = 'i010001'
-- Estes filtros devem ser ignorados quando há filtro de gerente
AND est.segmento_id = 999
AND est.diretoria_id = 999;

-- 2.2. Verificar se filtro de gerente gestão ignora outros filtros de estrutura
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
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
-- Estes filtros devem ser ignorados quando há filtro de gerente gestão
AND est.segmento_id = 999;

-- 2.3. Verificar se filtro de subindicador ignora indicador e família
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1
AND prod.subindicador_id = 1
-- Estes filtros devem ser ignorados quando há filtro de subindicador
AND prod.indicador_id = 999
AND prod.familia_id = 999;

-- ============================================
-- 3. TESTE: Validação de Datas
-- ============================================

-- 3.1. Data início maior que data fim (deve retornar 0)
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
WHERE 1=1
AND fr.data_realizado >= '2025-12-31'
AND fr.data_realizado <= '2025-01-01';

-- 3.2. Data no futuro (deve retornar 0 ou dados futuros se existirem)
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
WHERE 1=1
AND fr.data_realizado >= '2099-01-01';

-- 3.3. Data muito antiga (deve retornar 0 ou dados antigos se existirem)
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
WHERE 1=1
AND fr.data_realizado <= '1900-01-01';

-- ============================================
-- 4. TESTE: Validação de Relacionamentos
-- ============================================

-- 4.1. Verificar registros órfãos em f_realizados (sem estrutura)
SELECT COUNT(*) as total_orfos
FROM f_realizados AS fr
LEFT JOIN d_estrutura AS est ON est.funcional = fr.funcional
WHERE est.funcional IS NULL;

-- 4.2. Verificar registros órfãos em f_realizados (sem produto)
SELECT COUNT(*) as total_orfos
FROM f_realizados AS fr
LEFT JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE prod.id IS NULL;

-- 4.3. Verificar registros órfãos em f_meta (sem estrutura)
SELECT COUNT(*) as total_orfos
FROM f_meta AS m
LEFT JOIN d_estrutura AS est ON est.funcional = m.funcional
WHERE est.funcional IS NULL;

-- 4.4. Verificar registros órfãos em f_meta (sem produto)
SELECT COUNT(*) as total_orfos
FROM f_meta AS m
LEFT JOIN d_produtos AS prod ON prod.id = m.produto_id
WHERE prod.id IS NULL;

-- 4.5. Verificar registros órfãos em f_pontos (sem estrutura)
SELECT COUNT(*) as total_orfos
FROM f_pontos AS p
LEFT JOIN d_estrutura AS est ON est.funcional = p.funcional
WHERE est.funcional IS NULL;

-- ============================================
-- 5. TESTE: Validação de Agregações
-- ============================================

-- 5.1. Verificar se SUM retorna NULL quando não há dados
SELECT 
    dp.id,
    COALESCE(SUM(fr.realizado), 0) AS total_realizado
FROM d_produtos AS dp
LEFT JOIN f_realizados AS fr ON fr.produto_id = dp.id
WHERE dp.id = 999 -- Produto que não existe
GROUP BY dp.id;

-- 5.2. Verificar divisão por zero no cálculo de atingimento
SELECT 
    dp.id,
    COALESCE(fr.total_realizado / NULLIF(fm.total_meta, 0), 0) AS ating
FROM d_produtos AS dp
LEFT JOIN (
    SELECT produto_id, SUM(realizado) AS total_realizado
    FROM f_realizados
    GROUP BY produto_id
) AS fr ON fr.produto_id = dp.id
LEFT JOIN (
    SELECT produto_id, SUM(meta_mensal) AS total_meta
    FROM f_meta
    GROUP BY produto_id
) AS fm ON fm.produto_id = dp.id
WHERE 1=1
LIMIT 5;

-- ============================================
-- 6. TESTE: Validação de Performance
-- ============================================

-- 6.1. Query com muitos JOINs (teste de performance)
SELECT 
    fr.id_contrato,
    est.nome,
    prod.id,
    seg.nome AS segmento,
    dir.nome AS diretoria,
    reg.nome AS regional,
    ag.nome AS agencia,
    fam.nm_familia AS familia,
    ind.nm_indicador AS indicador
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
LEFT JOIN segmentos AS seg ON seg.id = est.segmento_id
LEFT JOIN diretorias AS dir ON dir.id = est.diretoria_id
LEFT JOIN regionais AS reg ON reg.id = est.regional_id
LEFT JOIN agencias AS ag ON ag.id = est.agencia_id
LEFT JOIN familia AS fam ON fam.id = prod.familia_id
LEFT JOIN indicador AS ind ON ind.id = prod.indicador_id
LIMIT 10;

-- 6.2. Query com subconsultas aninhadas
SELECT 
    dp.id,
    (SELECT COUNT(*) FROM f_realizados WHERE produto_id = dp.id) AS total_realizados,
    (SELECT COUNT(*) FROM f_meta WHERE produto_id = dp.id) AS total_metas,
    (SELECT COUNT(*) FROM f_pontos WHERE produto_id = dp.id) AS total_pontos
FROM d_produtos AS dp
LIMIT 5;

-- ============================================
-- 7. TESTE: Validação de Tipos de Dados
-- ============================================

-- 7.1. Verificar tipos de dados em f_realizados
SELECT 
    id_contrato,
    funcional,
    produto_id,
    realizado,
    data_realizado,
    CASE 
        WHEN realizado REGEXP '^[0-9]+\.?[0-9]*$' THEN 'NUMERIC'
        ELSE 'NON_NUMERIC'
    END AS tipo_realizado
FROM f_realizados
LIMIT 5;

-- 7.2. Verificar tipos de dados em f_meta
SELECT 
    funcional,
    produto_id,
    meta_mensal,
    data_meta,
    CASE 
        WHEN meta_mensal REGEXP '^[0-9]+\.?[0-9]*$' THEN 'NUMERIC'
        ELSE 'NON_NUMERIC'
    END AS tipo_meta
FROM f_meta
LIMIT 5;

-- ============================================
-- 8. TESTE: Validação de Constraints
-- ============================================

-- 8.1. Verificar duplicatas em f_realizados (id_contrato deve ser único)
SELECT 
    id_contrato,
    COUNT(*) as total
FROM f_realizados
GROUP BY id_contrato
HAVING COUNT(*) > 1;

-- 8.2. Verificar valores negativos (se não devem existir)
SELECT COUNT(*) as total_negativos
FROM f_realizados
WHERE realizado < 0;

SELECT COUNT(*) as total_negativos
FROM f_meta
WHERE meta_mensal < 0;

SELECT COUNT(*) as total_negativos
FROM f_pontos
WHERE realizado < 0 OR meta < 0;

-- 8.3. Verificar datas inválidas
SELECT COUNT(*) as total_invalidos
FROM f_realizados
WHERE data_realizado IS NULL OR data_realizado = '0000-00-00';

SELECT COUNT(*) as total_invalidos
FROM f_meta
WHERE data_meta IS NULL OR data_meta = '0000-00-00';

-- ============================================
-- 9. TESTE: Validação de Filtros Combinados Complexos
-- ============================================

-- 9.1. Todos os filtros de estrutura combinados (deve aplicar apenas o mais específico)
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
WHERE 1=1
-- Apenas o filtro de agência deve ser aplicado
AND est.segmento_id = 1
AND est.diretoria_id = 8607
AND est.regional_id = 8486
AND est.agencia_id = 1141;

-- 9.2. Todos os filtros de produto combinados (deve aplicar apenas o mais específico)
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1
-- Apenas o filtro de subindicador deve ser aplicado
AND prod.familia_id = 1
AND prod.indicador_id = 1
AND prod.subindicador_id = 1;

-- 9.3. Filtros de estrutura e produto combinados
SELECT COUNT(DISTINCT fr.id_contrato) as total
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
INNER JOIN d_produtos AS prod ON prod.id = fr.produto_id
WHERE 1=1
AND est.agencia_id = 1141
AND prod.familia_id = 1
AND fr.data_realizado >= '2025-11-01'
AND fr.data_realizado <= '2025-11-30';

-- ============================================
-- 10. TESTE: Validação de Paginação
-- ============================================

-- 10.1. Primeira página (LIMIT 10 OFFSET 0)
SELECT 
    fr.id_contrato,
    est.nome,
    fr.data_realizado
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
ORDER BY fr.id_contrato
LIMIT 10 OFFSET 0;

-- 10.2. Segunda página (LIMIT 10 OFFSET 10)
SELECT 
    fr.id_contrato,
    est.nome,
    fr.data_realizado
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
ORDER BY fr.id_contrato
LIMIT 10 OFFSET 10;

-- 10.3. Offset maior que total de registros (deve retornar vazio)
SELECT 
    fr.id_contrato,
    est.nome,
    fr.data_realizado
FROM f_realizados AS fr
INNER JOIN d_estrutura AS est ON est.funcional = fr.funcional
ORDER BY fr.id_contrato
LIMIT 10 OFFSET 1000;

