<?php
declare(strict_types=1);

namespace DistrictUI\Service;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use RuntimeException;

class AssetInstaller
{
    protected const ICON_SIZES_CSS = <<<'CSS'
.bi-2xs {
    font-size: .625rem;
    line-height: .1em;
    vertical-align: .225em;
}
.bi-xs {
    font-size: .75rem;
    line-height: .08333em;
    vertical-align: .125em;
}
.bi-sm {
    font-size: .875rem;
    line-height: .07143em;
    vertical-align: .05357em;
}
.bi-lg {
    font-size: 1.25rem;
    line-height: .05em;
    vertical-align: -.075em;
}
.bi-xl {
    font-size: 1.5rem;
    line-height: .04167em;
    vertical-align: -.125em;
}
.bi-2xl {
    font-size: 2rem;
    line-height: 1;
    vertical-align: -.12em;
}

.bi-1x {
    font-size: 1rem;
}
.bi-2x {
    font-size: 2rem;
}
.bi-3x {
    font-size: 3rem;
}
.bi-4x {
    font-size: 4rem;
}
.bi-5x {
    font-size: 5rem;
}
.bi-6x {
    font-size: 6rem;
}
.bi-7x {
    font-size: 7rem;
}
.bi-8x {
    font-size: 8rem;
}
.bi-9x {
    font-size: 9rem;
}
.bi-10x {
    font-size: 10rem;
}
CSS;

    public function install(ConsoleIo $io): void
    {
        $pluginPath = rtrim(Plugin::path('DistrictUI'), DIRECTORY_SEPARATOR);
        $config = (array)Configure::read('DistrictUI.build', []);

        $this->ensureYarnAvailable();
        $this->installNodeDependencies($pluginPath, $io);
        $this->buildThemeAssets($pluginPath, $io);
        $this->prepareTargetDirectories($pluginPath, $config);
        $this->installThemeAssets($pluginPath, $config, $io);
        $this->copyFile(
            $pluginPath . DIRECTORY_SEPARATOR . $config['bootstrapJs'],
            $pluginPath . DIRECTORY_SEPARATOR . $config['webrootJsDir'] . DIRECTORY_SEPARATOR . 'bootstrap.bundle.min.js',
        );
        $this->copyFile(
            $pluginPath . DIRECTORY_SEPARATOR . $config['bootstrapIconsCss'],
            $pluginPath . DIRECTORY_SEPARATOR . $config['webrootFontDir'] . DIRECTORY_SEPARATOR . 'bootstrap-icons.css',
        );
        $this->writeFile(
            $pluginPath . DIRECTORY_SEPARATOR . $config['webrootFontDir'] . DIRECTORY_SEPARATOR . 'bootstrap-icon-sizes.css',
            self::ICON_SIZES_CSS . PHP_EOL,
        );
        $this->copyDirectoryContents(
            $pluginPath . DIRECTORY_SEPARATOR . $config['bootstrapIconsFontsDir'],
            $pluginPath . DIRECTORY_SEPARATOR . $config['webrootFontDir'] . DIRECTORY_SEPARATOR . 'fonts',
        );

        $io->success('DistrictUI assets built and synced.');
    }

    protected function ensureYarnAvailable(): void
    {
        $command = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where yarn' : 'which yarn';
        $output = shell_exec($command);
        if (!$output) {
            throw new RuntimeException('Yarn is required to build DistrictUI assets.');
        }
    }

    protected function installNodeDependencies(string $pluginPath, ConsoleIo $io): void
    {
        $io->info('Installing DistrictUI frontend build dependencies...');
        $this->runCommand('yarn install --immutable', $pluginPath, $io);
    }

    protected function buildThemeAssets(string $pluginPath, ConsoleIo $io): void
    {
        $io->info('Building DistrictUI frontend assets from local source...');
        $this->runCommand('yarn build', $pluginPath, $io);
    }

    protected function prepareTargetDirectories(string $pluginPath, array $config): void
    {
        foreach ([$config['webrootCssDir'], $config['webrootJsDir'], $config['webrootFontDir'], $config['webrootAssetsDir']] as $dir) {
            $fullPath = $pluginPath . DIRECTORY_SEPARATOR . $dir;
            if (!is_dir($fullPath) && !mkdir($fullPath, 0777, true) && !is_dir($fullPath)) {
                throw new RuntimeException(sprintf('Failed to create directory: %s', $fullPath));
            }
        }

        $fontsDir = $pluginPath . DIRECTORY_SEPARATOR . $config['webrootFontDir'] . DIRECTORY_SEPARATOR . 'fonts';
        if (!is_dir($fontsDir) && !mkdir($fontsDir, 0777, true) && !is_dir($fontsDir)) {
            throw new RuntimeException(sprintf('Failed to create directory: %s', $fontsDir));
        }
    }

    protected function installThemeAssets(string $pluginPath, array $config, ConsoleIo $io): void
    {
        $distCss = $pluginPath . DIRECTORY_SEPARATOR . $config['distCss'];
        $distMinCss = $pluginPath . DIRECTORY_SEPARATOR . $config['distMinCss'];
        $distAssetsDir = $pluginPath . DIRECTORY_SEPARATOR . $config['distAssetsDir'];
        $targetCss = $pluginPath . DIRECTORY_SEPARATOR . $config['webrootCssDir'] . DIRECTORY_SEPARATOR . 'district-ui.css';
        $targetMinCss = $pluginPath . DIRECTORY_SEPARATOR . $config['webrootCssDir'] . DIRECTORY_SEPARATOR . 'district-ui.min.css';
        $targetAssetsDir = $pluginPath . DIRECTORY_SEPARATOR . $config['webrootAssetsDir'];

        if (!is_file($distCss)) {
            throw new RuntimeException(sprintf('Built CSS asset not found: %s', $distCss));
        }
        if (!is_file($distMinCss)) {
            throw new RuntimeException(sprintf('Built minified CSS asset not found: %s', $distMinCss));
        }
        if (!is_dir($distAssetsDir)) {
            throw new RuntimeException(sprintf('Built asset directory not found: %s', $distAssetsDir));
        }

        $this->copyFile($distCss, $targetCss);
        $this->copyFile($distMinCss, $targetMinCss);
        $this->copyDirectoryContents($distAssetsDir, $targetAssetsDir);

        $io->out('Theme assets copied from local build output.');
    }

    protected function runCommand(string $command, string $workingDirectory, ConsoleIo $io): void
    {
        $fullCommand = sprintf('cd %s && %s 2>&1', escapeshellarg($workingDirectory), $command);
        exec($fullCommand, $output, $return);
        foreach ($output as $line) {
            $io->out($line);
        }

        if ($return !== 0) {
            throw new RuntimeException(sprintf('Command failed: %s', $command));
        }
    }

    protected function copyFile(string $source, string $target): void
    {
        if (!is_file($source)) {
            throw new RuntimeException(sprintf('Asset source not found: %s', $source));
        }
        if (!copy($source, $target)) {
            throw new RuntimeException(sprintf('Failed to copy %s to %s', $source, $target));
        }
    }

    protected function writeFile(string $target, string $contents): void
    {
        if (file_put_contents($target, $contents) === false) {
            throw new RuntimeException(sprintf('Failed to write file: %s', $target));
        }
    }

    protected function copyDirectoryContents(string $sourceDir, string $targetDir): void
    {
        if (!is_dir($sourceDir)) {
            throw new RuntimeException(sprintf('Asset source directory not found: %s', $sourceDir));
        }

        $entries = scandir($sourceDir);
        if ($entries === false) {
            throw new RuntimeException(sprintf('Failed to read directory: %s', $sourceDir));
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $source = $sourceDir . DIRECTORY_SEPARATOR . $entry;
            $target = $targetDir . DIRECTORY_SEPARATOR . $entry;
            if (is_file($source)) {
                $this->copyFile($source, $target);
            }
        }
    }
}
