<?php

namespace App\Infrastructure\Helpers;

class RowMapper
{
    public static function map(array $row, array $mapping)
    {
        $result = [];
        
        foreach ($mapping as $targetKey => $sourceKey) {
            if (is_callable($sourceKey)) {
                $result[$targetKey] = $sourceKey($row);
            } elseif (is_string($sourceKey)) {
                $result[$targetKey] = isset($row[$sourceKey]) ? $row[$sourceKey] : null;
            } else {
                $result[$targetKey] = $sourceKey;
            }
        }
        
        return $result;
    }
    
    public static function toString($value)
    {
        return $value !== null ? (string)$value : null;
    }
    
    public static function toFloat($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        if (is_numeric($value)) {
            return (float)$value;
        }
        
        return null;
    }
    
    public static function formatDate($value)
    {
        return DateFormatter::toIsoDate($value);
    }
}

