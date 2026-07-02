<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Illuminate\Support\Facades\DB;

/**
 * PostgreSQL-specific schema helpers used by migrations.
 */
final class PostgresSchema
{
    public static function partialUniqueIndex(string $table, string $indexName, string $columns): void
    {
        DB::statement(sprintf(
            'CREATE UNIQUE INDEX %s ON %s (%s) WHERE deleted_at IS NULL',
            $indexName,
            $table,
            $columns,
        ));
    }

    public static function partialIndex(
        string $table,
        string $indexName,
        string $columns,
        string $condition,
    ): void {
        DB::statement(sprintf(
            'CREATE INDEX %s ON %s (%s) WHERE %s',
            $indexName,
            $table,
            $columns,
            $condition,
        ));
    }

    public static function ginJsonbIndex(string $table, string $column, string $indexName): void
    {
        DB::statement(sprintf(
            'CREATE INDEX %s ON %s USING gin (%s jsonb_path_ops)',
            $indexName,
            $table,
            $column,
        ));
    }

    public static function ginTsVectorIndex(string $table, string $column, string $indexName): void
    {
        DB::statement(sprintf(
            'CREATE INDEX %s ON %s USING gin (%s)',
            $indexName,
            $table,
            $column,
        ));
    }

    /**
     * @param list<string> $weightedFields field expressions with optional weight prefix, e.g. "A:coalesce(name,'')"
     */
    public static function addWeightedSearchVector(
        string $table,
        string $column,
        array $weightedFields,
    ): void {
        $parts = [];

        foreach ($weightedFields as $field) {
            if (str_contains($field, ':')) {
                [$weight, $expression] = explode(':', $field, 2);
                $parts[] = sprintf(
                    "setweight(to_tsvector('simple', %s), '%s')",
                    $expression,
                    strtoupper($weight),
                );
            } else {
                $parts[] = sprintf("to_tsvector('simple', %s)", $field);
            }
        }

        DB::statement(sprintf(
            'ALTER TABLE %s ADD COLUMN %s tsvector GENERATED ALWAYS AS (%s) STORED',
            $table,
            $column,
            implode(' || ', $parts),
        ));
    }

    public static function textArrayColumn(string $table, string $column, string $default = '{}'): void
    {
        DB::statement(sprintf(
            "ALTER TABLE %s ADD COLUMN %s text[] NOT NULL DEFAULT '%s'",
            $table,
            $column,
            $default,
        ));
    }
}
