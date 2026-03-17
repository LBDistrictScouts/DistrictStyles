<?php
declare(strict_types=1);

namespace DistrictUI\Test\TestCase\Command;

use Cake\TestSuite\TestCase;
use DistrictUI\Command\DistrictUiCommand;
use DistrictUI\Command\InstallCommand;

class InstallCommandTest extends TestCase
{
    public function testInstallCommandMetadata(): void
    {
        $this->assertSame('district_ui install', InstallCommand::defaultName());
        $this->assertSame(
            'Build DistrictUI assets and copy them into the application webroot.',
            InstallCommand::getDescription(),
        );
    }

    public function testDistrictUiCommandMetadata(): void
    {
        $this->assertSame('district_ui', DistrictUiCommand::defaultName());
        $this->assertSame('DistrictUI commands entry point.', DistrictUiCommand::getDescription());
    }
}
