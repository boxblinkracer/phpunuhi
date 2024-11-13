<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Traits;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\PHPUnuhi;
use PHPUnuhi\Traits\CommandTrait;
use Symfony\Component\Console\Input\InputInterface;

class CommandTraitTest extends TestCase
{
    use CommandTrait;


    public function testShowHeader(): void
    {
        $this->expectOutputString("PHPUnuhi Framework, v" . PHPUnuhi::getVersion() . "\nCopyright (c) 2023 - " . date('Y') . ", Christian Dangl and contributors\n\n");

        $this->showHeader();
    }


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
     * @param mixed $option
     */
    public function testGetConfigFileWithoutValueGivesDefaultConfig($option): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')->willReturn($option);

        $config = $this->getConfigFile($input);

        $expected = getcwd() . '/phpunuhi.xml';

        $this->assertEquals($expected, $config);
    }

    /**
     * @testWith  [ "" , null ]
     *            [ "", "" ]
     *            [ " ", " " ]
     *            [ "", 4 ]
     *            [ "", 4.5 ]
     *
     * @param mixed $option
     */
    public function testGetConfigStringValue(string $expected, $option): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')->willReturn($option);

        $value = $this->getConfigStringValue('myKey', $input);

        $this->assertEquals($expected, $value);
    }

    /**
     * @testWith  [ false , null ]
     *            [ false, "" ]
     *            [ false, " " ]
     *            [ false, 4 ]
     *            [ false, 4.5 ]
     *            [ false, false ]
     *            [ false, "false" ]
     *            [ false, "true" ]
     *            [ true, true ]
     *
     * @param mixed $option
     */
    public function testGetConfigBoolValue(bool $expected, $option): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')->willReturn($option);

        $value = $this->getConfigBoolValue('myKey', $input);

        $this->assertEquals($expected, $value);
    }
}
