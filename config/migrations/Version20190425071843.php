<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MariaDb1027Platform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20190425071843 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return 'Initialize `event_streams` and `projections` tables';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !\array_key_exists($this->connection->getDatabasePlatform()->getName(), $this->getPlatformToPathMapping()),
            'Unsupported database'
        );

        $platform = $this->connection->getDatabasePlatform() instanceof MariaDb1027Platform
            ? 'mariadb'
            : $this->getPlatformToPathMapping()[$this->connection->getDatabasePlatform()->getName()];

        foreach ($this->getTableFiles() as $file) {
            $migrations = explode(';', file_get_contents(
                $this->getMigrationPath($platform, $file)
            ));

            array_map(function(string $migration) {
                $migration = trim($migration);
                if ($migration) {
                    $this->addSql($migration);
                }
            }, $migrations);
        }
    }

    public function down(Schema $schema): void
    {
        foreach ($this->getTableFiles() as $table => $file) {
            $this->addSql("DROP TABLE $table");
        }
    }

    private function getMigrationPath(string $platform, string $file): string
    {
        return $this->container->get('kernel')->getProjectDir()
            . "/vendor/prooph/pdo-event-store/scripts/$platform/$file";
    }

    private function getTableFiles(): array
    {
        return [
            'event_streams' => '01_event_streams_table.sql',
            'projections' => '02_projections_table.sql',
        ];
    }

    private function getPlatformToPathMapping(): array
    {
        return [
            'mysql' => 'mysql',
            'postgresql' => 'postgres',
        ];
    }
}
