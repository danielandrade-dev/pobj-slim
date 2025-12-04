<?php

namespace App\Infrastructure\Helpers;

use RuntimeException;

/**
 * Helper para processamento de conhecimento e integração com OpenAI
 * Implementa RAG (Retrieval-Augmented Generation) simples
 */
class KnowledgeHelper
{
    /**
     * Obtém variável de ambiente
     */
    public static function env(string $key, ?string $default = null): ?string
    {
        $v = $_ENV[$key] ?? getenv($key);
        return (is_string($v) && $v !== '') ? $v : $default;
    }

    /**
     * Faz requisição HTTP POST JSON
     */
    public static function httpPostJson(string $url, array $headers, array $payload): array
    {
        $ch = curl_init($url);
        $headers[] = 'Content-Type: application/json';
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_TIMEOUT => 90,
        ]);
        $raw = curl_exec($ch);
        if ($raw === false) {
            throw new RuntimeException('Falha de rede: ' . curl_error($ch));
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 0;
        curl_close($ch);
        $json = json_decode($raw, true);
        if (!is_array($json)) {
            throw new RuntimeException("Resposta inválida da API (HTTP $code).");
        }
        if ($code < 200 || $code >= 300) {
            $msg = $json['error']['message'] ?? ($json['error'] ?? 'Erro da API');
            throw new RuntimeException("OpenAI HTTP $code: " . $msg);
        }
        return $json;
    }

    /**
     * Verifica se pdftotext está disponível
     */
    private static function hasPdfToText(): bool
    {
        @exec('pdftotext -v', $o, $r);
        return ($r === 0 || stripos(implode("\n", $o), 'pdftotext') !== false);
    }

    /**
     * Converte PDF para texto
     */
    private static function pdfToText(string $path): string
    {
        if (!is_file($path)) {
            return '';
        }
        if (self::hasPdfToText()) {
            $tmp = sys_get_temp_dir() . '/pobj_' . uniqid() . '.txt';
            $cmd = 'pdftotext -layout ' . escapeshellarg($path) . ' ' . escapeshellarg($tmp);
            @exec($cmd);
            if (is_file($tmp)) {
                $txt = (string)file_get_contents($tmp);
                @unlink($tmp);
                return trim($txt);
            }
        }
        return "[AVISO] Não foi possível extrair texto do PDF '" . basename($path) . "' neste servidor. Converta para .txt e coloque em docs/knowledge.";
    }

    /**
     * Converte CSV para texto
     */
    private static function csvToText(string $path): string
    {
        if (!is_file($path)) {
            return '';
        }
        $fh = fopen($path, 'r');
        if (!$fh) {
            return '';
        }
        $rows = [];
        $headers = [];
        $i = 0;
        while (($row = fgetcsv($fh, 0, ';')) !== false) {
            if ($i === 0) {
                $headers = $row;
                $i++;
                continue;
            }
            $assoc = [];
            foreach ($row as $k => $v) {
                $key = isset($headers[$k]) && $headers[$k] !== '' ? $headers[$k] : "col$k";
                $assoc[$key] = $v;
            }
            $rows[] = $assoc;
            $i++;
            if ($i > 2000) {
                break; // limite de segurança
            }
        }
        fclose($fh);
        $out = "CSV: " . basename($path) . "\n";
        foreach ($rows as $r) {
            $line = [];
            foreach ($r as $k => $v) {
                $line[] = "$k: $v";
            }
            $out .= "- " . implode(' | ', $line) . "\n";
        }
        return $out;
    }

    /**
     * Converte JSON para texto
     */
    private static function jsonToText(string $path): string
    {
        if (!is_file($path)) {
            return '';
        }
        $raw = file_get_contents($path);
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        $data = json_decode($raw, true);
        if ($data === null) {
            return "JSON (texto bruto):\n" . $raw;
        }
        return "JSON: " . basename($path) . "\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Converte TXT para texto
     */
    private static function txtToText(string $path): string
    {
        $t = @file_get_contents($path);
        return is_string($t) ? trim($t) : '';
    }

    /**
     * Converte arquivo para texto baseado na extensão
     */
    private static function fileToText(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            return self::pdfToText($path);
        }
        if ($ext === 'csv') {
            return self::csvToText($path);
        }
        if ($ext === 'json') {
            return self::jsonToText($path);
        }
        if ($ext === 'txt') {
            return self::txtToText($path);
        }
        return ''; // ignorar extensões não suportadas
    }

    /**
     * Escaneia pasta de conhecimento
     */
    private static function scanKnowledge(string $dir): array
    {
        if (!is_dir($dir)) {
            return [];
        }
        $out = [];
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            /** @var \SplFileInfo $file */
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['txt', 'pdf', 'csv', 'json'], true)) {
                continue;
            }
            $path = $file->getPathname();
            $text = self::fileToText($path);
            if ($text !== '') {
                $out[] = [
                    'path' => $path,
                    'name' => basename($path),
                    'mtime' => filemtime($path) ?: time(),
                    'text' => $text
                ];
            }
        }
        return $out;
    }

    /**
     * Divide texto em chunks
     */
    private static function chunkText(string $text, int $chunkSize = 1600, int $overlap = 200): array
    {
        $text = str_replace("\r", "", $text);
        $len = strlen($text);
        $i = 0;
        $id = 1;
        $chunks = [];
        while ($i < $len) {
            $end = min($len, $i + $chunkSize);
            $slice = trim(substr($text, $i, $end - $i));
            if ($slice !== '') {
                $chunks[] = ['id' => $id++, 'text' => $slice];
            }
            if ($end >= $len) {
                break;
            }
            $i = $end - $overlap;
            if ($i < 0) {
                $i = 0;
            }
        }
        return $chunks;
    }

    /**
     * Gera embeddings para textos
     */
    private static function embed(array $texts): array
    {
        $apiKey = self::env('OPENAI_API_KEY', '');
        if ($apiKey === '') {
            throw new RuntimeException('OPENAI_API_KEY não configurada.');
        }
        $model = self::env('OPENAI_EMBED_MODEL', 'text-embedding-3-small');

        // Lotes para evitar payload muito grande
        $batch = 80;
        $out = [];
        $n = count($texts);
        for ($i = 0; $i < $n; $i += $batch) {
            $slice = array_slice($texts, $i, $batch);
            $resp = self::httpPostJson(
                'https://api.openai.com/v1/embeddings',
                ['Authorization: Bearer ' . $apiKey],
                ['model' => $model, 'input' => $slice]
            );
            foreach ($resp['data'] as $k => $row) {
                $out[$i + $k] = $row['embedding'];
            }
        }
        return $out;
    }

    /**
     * Constrói ou carrega índice de conhecimento
     */
    public static function buildOrLoadIndex(string $dir, string $indexPath): array
    {
        $files = self::scanKnowledge($dir);
        $sig = [];
        foreach ($files as $f) {
            $sig[$f['path']] = $f['mtime'];
        }

        // Carrega índice existente se assinatura confere
        if (is_file($indexPath)) {
            $idx = json_decode((string)file_get_contents($indexPath), true);
            if (is_array($idx) && ($idx['signature'] ?? null) === $sig) {
                return $idx;
            }
        }

        // (Re)constrói índice
        $items = [];  // cada item = ['source'=>path, 'name'=>name, 'chunk_id'=>X, 'text'=>...]
        foreach ($files as $f) {
            $chunks = self::chunkText($f['text']);
            foreach ($chunks as $c) {
                $items[] = [
                    'source' => $f['path'],
                    'name' => $f['name'],
                    'chunk_id' => $c['id'],
                    'text' => $c['text'],
                ];
            }
        }
        if (empty($items)) {
            $idx = ['items' => [], 'embeddings' => [], 'signature' => $sig, 'built_at' => time()];
            @file_put_contents($indexPath, json_encode($idx, JSON_UNESCAPED_UNICODE));
            return $idx;
        }

        $inputs = [];
        foreach ($items as $i) {
            $inputs[] = $i['text'];
        }
        $emb = self::embed($inputs);

        $idx = ['items' => $items, 'embeddings' => $emb, 'signature' => $sig, 'built_at' => time()];
        @file_put_contents($indexPath, json_encode($idx, JSON_UNESCAPED_UNICODE));
        return $idx;
    }

    /**
     * Calcula similaridade cosseno
     */
    private static function cosine(array $a, array $b): float
    {
        $dot = 0.0;
        $na = 0.0;
        $nb = 0.0;
        $n = min(count($a), count($b));
        for ($i = 0; $i < $n; $i++) {
            $dot += $a[$i] * $b[$i];
            $na += $a[$i] * $a[$i];
            $nb += $b[$i] * $b[$i];
        }
        return ($na > 0 && $nb > 0) ? $dot / (sqrt($na) * sqrt($nb)) : 0.0;
    }

    /**
     * Recupera top-K trechos mais relevantes para a query
     */
    public static function retrieveTopK(string $query, array $index, int $k = 6): array
    {
        if ($query === '' || empty($index['items'])) {
            return [];
        }
        $qEmbeds = self::embed([$query]);
        $q = isset($qEmbeds[0]) ? $qEmbeds[0] : [];
        $scored = [];
        foreach ($index['items'] as $i => $it) {
            $e = isset($index['embeddings'][$i]) ? $index['embeddings'][$i] : null;
            if (!is_array($e)) {
                continue;
            }
            $scored[] = [
                'score' => self::cosine($q, $e),
                'source' => $it['source'],
                'name' => $it['name'],
                'chunk_id' => $it['chunk_id'],
                'text' => $it['text']
            ];
        }
        usort($scored, function ($a, $b) {
            return ($a['score'] < $b['score']) ? 1 : -1;
        });
        return array_slice($scored, 0, max(1, $k));
    }
}

