// estrutura.js

/* =========================================================
   POBJ • Engine de Estrutura Organizacional
   ========================================================= */

   const Estrutura = (function () {
    const lookups = {
      segmentos: new Map(),
      diretorias: new Map(),
      regionais: new Map(),
      agencias: new Map(),
      gerentesGestao: new Map(),
      gerentes: new Map(),
      familias: new Map(),
      indicadores: new Map(),
      subindicadores: new Map(),
    };
  
    const filterOptions = {
      segmento: [],
      diretoria: [],
      gerencia: [],
      agencia: [],
      gerenteGestao: [],
      gerente: [],
      familia: [],
      indicador: [],
      subindicador: [],
    };
  
    /* -------------------------------
         Helpers internos
    --------------------------------*/
    const normalizeKey = v => limparTexto(v) || "";
    const normalizeLabel = row => limparTexto(row.label || row.nome || "");
  
    function buildLookup(rows) {
      const map = new Map();
      if (!Array.isArray(rows)) return map;
  
      for (const raw of rows) {
        if (!raw) continue;
  
        const key =
          normalizeKey(
            raw.id ||
              raw.codigo ||
              raw.id_segmento ||
              raw.id_diretoria ||
              raw.id_regional ||
              raw.id_agencia
          );
  
        if (!key) continue;
  
        const label = normalizeLabel(raw) || key;
  
        map.set(key, { ...raw, id: key, nome: label, label });
      }
  
      return map;
    }
  
    function buildOptions(rows) {
      if (!Array.isArray(rows)) return [];
  
      return uniqById(
        rows.map(r =>
          normOpt({
            id: r.id ?? "",
            label: normalizeLabel(r) || "",
          })
        )
      );
    }
  
    /* -------------------------------
         Inicialização
    --------------------------------*/
    function register(dim) {
      lookups.segmentos = buildLookup(dim.segmentos);
      lookups.diretorias = buildLookup(dim.diretorias);
      lookups.regionais = buildLookup(dim.regionais);
      lookups.agencias = buildLookup(dim.agencias);
      lookups.gerentesGestao = buildLookup(dim.gerentesGestao);
      lookups.gerentes = buildLookup(dim.gerentes);
      lookups.familias = buildLookup(dim.familias);
      lookups.indicadores = buildLookup(dim.indicadores);
      lookups.subindicadores = buildLookup(dim.subindicadores);
  
      filterOptions.segmento = buildOptions(dim.segmentos);
      filterOptions.diretoria = buildOptions(dim.diretorias);
      filterOptions.gerencia = buildOptions(dim.regionais);
      filterOptions.agencia = buildOptions(dim.agencias);
      filterOptions.gerenteGestao = buildOptions(dim.gerentesGestao);
      filterOptions.gerente = buildOptions(dim.gerentes);
      filterOptions.familia = buildOptions(dim.familias);
      filterOptions.indicador = buildOptions(dim.indicadores);
      filterOptions.subindicador = buildOptions(dim.subindicadores);
    }
  
    /* -------------------------------
         API pública
    --------------------------------*/
    function getLookup(name) {
      return lookups[name] || new Map();
    }
  
    function get(name, id) {
      const key = normalizeKey(id);
      if (!key) return null;
      const map = getLookup(name);
      return map.get(key) || null;
    }
  
    function label(name, id, fallback = "") {
      const entry = get(name, id);
      if (!entry) return fallback || normalizeKey(id);
      return entry.label || entry.nome || entry.id;
    }
  
    /* Derivação específica */
    function deriveGerenteGestaoFromAgencia(id) {
      const ag = get("agencias", id);
      if (!ag) return "";
  
      const candidates = [
        ag.gerente_gestao_id,
        ag.gerenteGestaoId,
        ag.gerente_gestao,
        ag.gerenteGestao,
      ];
  
      for (const c of candidates) {
        const clean = normalizeKey(c);
        if (clean) return clean;
      }
      return "";
    }
  
    /* Carrega da API + inicializa */
    async function init() {
      const response = await apiGet("/estrutura", { _t: Date.now() })
        .catch(() => null);
  
      const data =
        response?.success && response?.data ? response.data : response || {};
  
      const rawData = {
        segmentos: data.segmentos || [],
        diretorias: data.diretorias || [],
        regionais: data.regionais || [],
        agencias: data.agencias || [],
        gerentesGestao: data.gerentes_gestao || [],
        gerentes: data.gerentes || [],
        familias: data.familias || [],
        indicadores: data.indicadores || [],
        subindicadores: data.subindicadores || [],
      };
  
      register(rawData);
  
      // Retorna os dados originais (arrays) para processamento posterior
      return rawData;
    }
  
    return {
      init,
      getLookup,
      get,
      label,
      deriveGerenteGestaoFromAgencia,
      filterOptions,
    };
  })();
  
  /* Export global se necessário */
  if (typeof window !== "undefined") {
    window.Estrutura = Estrutura;
  }

/* =========================================================
   Função para processar dados de estrutura
   Cria as variáveis globais DIM_*_LOOKUP e retorna dimensões processadas
   ========================================================= */
function processEstruturaData({
  dimSegmentosRaw = [],
  dimDiretoriasRaw = [],
  dimRegionaisRaw = [],
  dimAgenciasRaw = [],
  dimGerentesGestaoRaw = [],
  dimGerentesRaw = [],
  dimFamiliasRaw = [],
  dimIndicadoresRaw = [],
  dimSubindicadoresRaw = [],
} = {}) {
  // Função auxiliar para normalizar chave
  const normalizeKey = v => (typeof limparTexto !== "undefined" ? limparTexto(v) : String(v || "").trim()) || "";
  
  // Função auxiliar para normalizar label
  const normalizeLabel = row => {
    if (!row) return "";
    const label = row.label || row.nome || "";
    return (typeof limparTexto !== "undefined" ? limparTexto(label) : String(label).trim()) || "";
  };

  // Função para construir lookup Map
  function buildLookupMap(rows) {
    const map = new Map();
    if (!Array.isArray(rows)) return map;

    for (const raw of rows) {
      if (!raw) continue;

      const key = normalizeKey(
        raw.id ||
        raw.codigo ||
        raw.id_segmento ||
        raw.id_diretoria ||
        raw.id_regional ||
        raw.id_agencia ||
        raw.funcional
      );

      if (!key) continue;

      const label = normalizeLabel(raw) || key;
      const nome = normalizeLabel(raw) || key;

      map.set(key, { ...raw, id: key, nome, label });
    }

    return map;
  }

  // Cria os Maps de lookup globais
  const DIM_SEGMENTOS_LOOKUP = buildLookupMap(dimSegmentosRaw);
  const DIM_DIRETORIAS_LOOKUP = buildLookupMap(dimDiretoriasRaw);
  const DIM_REGIONAIS_LOOKUP = buildLookupMap(dimRegionaisRaw);
  const DIM_AGENCIAS_LOOKUP = buildLookupMap(dimAgenciasRaw);
  const DIM_GGESTAO_LOOKUP = buildLookupMap(dimGerentesGestaoRaw);
  const DIM_GERENTES_LOOKUP = buildLookupMap(dimGerentesRaw);
  const DIM_FAMILIAS_LOOKUP = buildLookupMap(dimFamiliasRaw);
  const DIM_INDICADORES_LOOKUP = buildLookupMap(dimIndicadoresRaw);
  const DIM_SUBINDICADORES_LOOKUP = buildLookupMap(dimSubindicadoresRaw);

  // Disponibiliza globalmente (window e escopo global)
  const globalScope = (function() {
    if (typeof window !== "undefined") return window;
    if (typeof global !== "undefined") return global;
    if (typeof globalThis !== "undefined") return globalThis;
    return this;
  })();

  globalScope.DIM_SEGMENTOS_LOOKUP = DIM_SEGMENTOS_LOOKUP;
  globalScope.DIM_DIRETORIAS_LOOKUP = DIM_DIRETORIAS_LOOKUP;
  globalScope.DIM_REGIONAIS_LOOKUP = DIM_REGIONAIS_LOOKUP;
  globalScope.DIM_AGENCIAS_LOOKUP = DIM_AGENCIAS_LOOKUP;
  globalScope.DIM_GGESTAO_LOOKUP = DIM_GGESTAO_LOOKUP;
  globalScope.DIM_GERENTES_LOOKUP = DIM_GERENTES_LOOKUP;
  globalScope.DIM_FAMILIAS_LOOKUP = DIM_FAMILIAS_LOOKUP;
  globalScope.DIM_INDICADORES_LOOKUP = DIM_INDICADORES_LOOKUP;
  globalScope.DIM_SUBINDICADORES_LOOKUP = DIM_SUBINDICADORES_LOOKUP;

  // Converte Maps para arrays para retorno
  const dimSegmentos = Array.from(DIM_SEGMENTOS_LOOKUP.values());
  const dimDiretorias = Array.from(DIM_DIRETORIAS_LOOKUP.values());
  const dimRegionais = Array.from(DIM_REGIONAIS_LOOKUP.values());
  const dimAgencias = Array.from(DIM_AGENCIAS_LOOKUP.values());
  const dimGerentesGestao = Array.from(DIM_GGESTAO_LOOKUP.values());
  const dimGerentes = Array.from(DIM_GERENTES_LOOKUP.values());
  const dimFamilias = Array.from(DIM_FAMILIAS_LOOKUP.values());
  const dimIndicadores = Array.from(DIM_INDICADORES_LOOKUP.values());
  const dimSubindicadores = Array.from(DIM_SUBINDICADORES_LOOKUP.values());

  return {
    dimSegmentos,
    dimDiretorias,
    dimRegionais,
    dimAgencias,
    dimGerentesGestao,
    dimGerentes,
    dimFamilias,
    dimIndicadores,
    dimSubindicadores,
  };
}
  