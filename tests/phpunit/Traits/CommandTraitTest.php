<?php

namespace phpunit\Traits;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\PHPUnuhi;
use PHPUnuhi\Traits\CommandTrait;
use Symfony\Component\Console\Input\InputInterface;

class CommandTraitTest extends TestCase
{
    use CommandTrait;


    /**
     * @return void
     */
    public function testShowHeader(): void
    {
        $this->expectOutputString("PHPUnuhi Framework, v" . PHPUnuhi::getVersion() . "\nCopyright (c) 2023 Christian Dangl\n\n");

        $this->showHeader();
    }

    /**
     * @return void
     */
    public function testGetConfigFileWithCustomValue(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')->willReturn('test.xml');

        $config = $this->getConfigFile($input);

        $expected = getcwd() . '/test.xml';

        $this->assertEquals($expected, $config);
    }

    /**
     * @testWith [ null ]
     *            [ "" ]
     *            [ " " ]
     *
     * @param $option
     * @return void
     */
    public function testGetConfigFileWithoutValueGivesDefaultConfig($option): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')->willReturn($option);

        $config = $this->getConfigFile($input);

        $expected = getcwd() . '/phpunuhi.xml';

        $this->assertEquals($expected, $config);
    }
}
