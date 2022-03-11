<?php

namespace App\Services;

use Exception;
use Illuminate\Database\Events\QueryExecuted;
use Log;

class AuditTrailService
{
    public function auditSql(QueryExecuted $query): void
    {
        if ($this->checkIfNeedsAudit($query)) {
            // Uncomment the following line to enable logging sql queries
            // Log::info($query->sql);

            // $bindings = json_encode($query->bindings);
            // if ($bindings) {
            //     Log::info($bindings);
            // }

            // TODO: Log slow query
            // Log::info(strval($query->time));

            // TODO: Log in mongo
            // $auditTrail = new AuditTrail();
            // $auditTrail->type = $this->getSqlType($query->sql);
            // $auditTrail->table_name = $this->getTableName($auditTrail->type, $query->sql);
            // $auditTrail->sql = $query->sql;
            // $auditTrail->new_values = json_encode($query->bindings);
            // $auditTrail->user_id = auth()->user()->id;
            // $auditTrail->save();
        }
    }

    private function checkIfNeedsAudit(QueryExecuted $query): bool
    {
        return (str_starts_with($query->sql, 'insert') ||
            str_starts_with($query->sql, 'update') ||
            str_starts_with($query->sql, 'delete')) &&

            // Ignore tables
            ! str_contains($query->sql, 'personal_access_tokens') &&
            ! str_contains($query->sql, 'audit_trails');
    }

    private function getTableName(string $type, string $sql): string
    {
        $tableName = '';

        switch ($type) {
            case 'I':
                $tableName = explode(' ', $sql)[2];
                break;
            case 'U':
                $tableName = explode(' ', $sql)[1];
                break;
            case 'D':
                $tableName = explode(' ', $sql)[2];
                break;
        }

        return trim($tableName, '`');
    }

    private function getSqlType(string $sql): string
    {
        if (str_starts_with($sql, 'insert')) {
            return 'I';
        }
        if (str_starts_with($sql, 'update')) {
            return 'U';
        }
        if (str_starts_with($sql, 'delete')) {
            return 'D';
        }

        throw new Exception('No such sql type');
    }
}
