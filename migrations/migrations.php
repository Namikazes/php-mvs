<?php

define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/vendor/autoload.php";

$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(BASE_DIR);
$dotenv->load();

class Migrations
{

    const SCRIPT_DIR = __DIR__ . '/script/';
    const MIGRATIONS_FILE = '0_migrations';

    public function __construct()
    {
        try {

            db()->beginTransaction();

            $this->createMigrationsTable();
            $this->runMigrations();

            if(db()->inTransaction()) {
                db()->commit();
            }

        } catch (PDOException $exception) {
            d($exception->getMessage(), $exception->getTrace());
            if(db()->inTransaction()) {
                db()->rollBack();
            }
        }
    }

    protected function runMigrations(): void
    {
        $migrations = scandir(static::SCRIPT_DIR);
        $migrations = array_values(array_diff(
           $migrations, ['.', '..', static::MIGRATIONS_FILE . '.sql']
        ));

        foreach($migrations as $migration) {
            if(!$this->checkIfMigrationsRun($migration)) {
                d("- Run '$migration' ...");
                $query = $this->getScript($migration);

                if($query->execute()) {
                    $this->logIntoMigrations($migration);
                    d("- '$migration' done");
                }
            } else {
                d("- '$migration' sckip");
            }

        }
    }

    protected function logIntoMigrations(string $migration): void
    {
        $query = db()->prepare("INSERT INTO migrations (name) VALUES (:name)");
        $query->bindParam('name', $migration);
        $query->execute();
    }

    protected function checkIfMigrationsRun($migration): bool
    {
        $query = db()->prepare("SELECT id FROM migrations WHERE name = :name");
        $query->bindParam('name', $migration);
        $query->execute();

        return (bool) $query->fetch();
    }


    protected function createMigrationsTable(): void
    {
        d('--------- Migrations start ---------');

        $query = $this->getScript(static::MIGRATIONS_FILE . '.sql');

        $result = match ($query->execute()){
            true => '- Migration created',
            false => '- Failed'
        };

        d($result,'--------- Migrations finish ---------');
    }

    protected function getScript(string $migration): PDOStatement
    {
        $sql = file_get_contents(static::SCRIPT_DIR . $migration);
        return db()->prepare($sql);
    }

} new Migrations();