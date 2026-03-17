<?php
declare(strict_types=1);

namespace DistrictUI\Command;

use Cake\Command\Command;
use Cake\Command\PluginAssetsCopyCommand;
use Cake\Command\PluginAssetsRemoveCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Plugin;
use DistrictUI\Service\AssetInstaller;
use RuntimeException;

class InstallCommand extends Command
{
    public static function defaultName(): string
    {
        return 'district_ui install';
    }

    public static function getDescription(): string
    {
        return 'Build DistrictUI assets and copy them into the application webroot.';
    }

    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $this->syncAssets($io);
        $this->ensureGitignoreEntry($io);
        $this->removePluginAssets($io);
        $this->copyPluginAssets($io, (bool)$args->getOption('overwrite'));

        $io->out();
        $io->success('DistrictUI installation completed.');

        return static::CODE_SUCCESS;
    }

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(static::getDescription())
            ->addOption('overwrite', [
                'help' => 'Overwrite existing files in the application webroot.',
                'default' => false,
                'boolean' => true,
            ]);
    }

    protected function syncAssets(ConsoleIo $io): void
    {
        $io->info('Building and syncing DistrictUI assets...');

        try {
            (new AssetInstaller())->install($io);
        } catch (RuntimeException $exception) {
            $io->error($exception->getMessage());
            $io->error('DistrictUI asset sync failed.');
            $this->abort();
        }
    }

    protected function removePluginAssets(ConsoleIo $io): void
    {
        $io->info('Removing existing DistrictUI assets from the application webroot...');

        $result = $this->executeCommand(PluginAssetsRemoveCommand::class, ['DistrictUI'], $io);
        if ($result !== static::CODE_SUCCESS && $result !== null) {
            $io->error('Removing existing DistrictUI assets failed.');
            $this->abort($result);
        }
    }

    protected function copyPluginAssets(ConsoleIo $io, bool $overwrite): void
    {
        $io->info('Copying DistrictUI assets into the application webroot...');

        $args = ['DistrictUI'];
        if ($overwrite) {
            $args[] = '--overwrite';
        }

        $result = $this->executeCommand(PluginAssetsCopyCommand::class, $args, $io);
        if ($result !== static::CODE_SUCCESS && $result !== null) {
            $io->error('Copying DistrictUI assets failed.');
            $this->abort($result);
        }
    }

    protected function ensureGitignoreEntry(ConsoleIo $io): void
    {
        $gitignorePath = ROOT . DIRECTORY_SEPARATOR . '.gitignore';
        $entry = '/webroot/district_u_i/';

        if (is_file($gitignorePath)) {
            $contents = file_get_contents($gitignorePath);
            if ($contents === false) {
                $io->warning(sprintf('Could not read %s to ensure generated asset ignore rule.', $gitignorePath));

                return;
            }
            if (preg_match('/^\/webroot\/district_u_i\/$/m', $contents)) {
                return;
            }

            $contents = rtrim($contents) . PHP_EOL . $entry . PHP_EOL;
        } else {
            $contents = $entry . PHP_EOL;
        }

        if (file_put_contents($gitignorePath, $contents) === false) {
            $io->warning(sprintf('Could not update %s to ignore generated DistrictUI assets.', $gitignorePath));

            return;
        }

        $io->success(sprintf('Ensured %s ignores generated DistrictUI assets.', $gitignorePath), 1, ConsoleIo::VERBOSE);
    }
}
