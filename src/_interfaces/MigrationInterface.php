<?php

namespace markorm_migration\_interfaces;

interface MigrationInterface {

    public function getConnection(): \PDO;

    public function getTablesFolder(): string;
    public function getMigrationsFolder(): string;

} 