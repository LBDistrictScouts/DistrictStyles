<?php
declare(strict_types=1);

namespace DistrictUI\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\CommandCollection;
use Cake\Console\CommandCollectionAwareInterface;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Text;

class DistrictUiCommand extends Command implements CommandCollectionAwareInterface
{
    protected CommandCollection $commands;

    public static function defaultName(): string
    {
        return 'district_ui';
    }

    public static function getDescription(): string
    {
        return 'DistrictUI commands entry point.';
    }

    public function setCommandCollection(CommandCollection $commands): void
    {
        $this->commands = $commands;
    }

    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $io->warning('No command provided. Run `district_ui --help` to get a list of commands.');

        return static::CODE_ERROR;
    }

    protected function displayHelp(ConsoleOptionParser $parser, Arguments $args, ConsoleIo $io): void
    {
        $io->out(Text::wrap($parser->getDescription(), 72), 2);
        $io->info('Available Commands:', 2);

        foreach ($this->commands as $command => $class) {
            if (substr($command, 0, 11) === 'district_ui') {
                $io->out("- $command");
            }
        }

        $io->out();
        $io->out('To run a command, type <info>`district_ui command_name [args|options]`</info>');
        $io->out('To get help on a specific command, type <info>`district_ui command_name --help`</info>', 2);
    }

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->setDescription(
            'The DistrictUI console provides commands for building theme assets ' .
            'and publishing the plugin assets into your application webroot.',
        );
    }
}
