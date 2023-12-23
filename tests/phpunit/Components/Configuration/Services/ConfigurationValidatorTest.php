<?php

namespace phpunit\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Configuration\Services\ConfigurationValidator;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\Configuration;

class ConfigurationValidatorTest extends TestCase
{

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testEmptySetsThrowException(): void
    {
        $this->expectException(ConfigurationException::class);

        $configuration = new Configuration([]);

        $validator = new ConfigurationValidator();
        $validator->validateConfig($configuration);
    }
}
