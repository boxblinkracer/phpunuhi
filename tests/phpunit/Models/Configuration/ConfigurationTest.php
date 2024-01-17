<?php

namespace phpunit\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Configuration\Coverage\Coverage;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\TranslationSet;

class ConfigurationTest extends TestCase
{

    /**
     * @return void
     */
    public function testTranslationSets(): void
    {
        $sets = [];
        $sets[] = new TranslationSet('products', 'ini', new Protection(), [], new Filter(), [], [], []);

        $config = new Configuration($sets);

        $this->assertCount(1, $config->getTranslationSets());
    }

    /**
     * @return void
     */
    public function testGetGlobalCoverage(): void
    {
        $cov = new Coverage();
        $cov->setTotalMinCoverage(36);

        $config = new Configuration([]);
        $config->setCoverage($cov);

        $this->assertSame($cov, $config->getCoverage());
    }

    /**
     * @return void
     */
    public function testHasCoverageWithTotalCoverage(): void
    {
        $cov = new Coverage();
        $cov->setTotalMinCoverage(36);

        $config = new Configuration([]);

        $this->assertFalse($config->hasCoverageSetting());

        $config->setCoverage($cov);

        $this->assertTrue($config->hasCoverageSetting());
    }

    /**
     * @return void
     */
    public function testHasCoverageWithTotalLocaleCoverage(): void
    {
        $cov = new Coverage();
        $cov->addLocaleCoverage('DE', 37);

        $config = new Configuration([]);

        $this->assertFalse($config->hasCoverageSetting());

        $config->setCoverage($cov);

        $this->assertTrue($config->hasCoverageSetting());
    }

    /**
     * @return void
     */
    public function testHasCoverageWithTotalSetCoverage(): void
    {
        $set = new TranslationSet('products', 'ini', new Protection(), [], new Filter(), [], [], []);

        $config = new Configuration([$set]);

        $this->assertFalse($config->hasCoverageSetting());

        $cov = new Coverage();
        $cov->setTotalMinCoverage(25);

        $set->setCoverage($cov);

        $this->assertTrue($config->hasCoverageSetting());
    }

    /**
     * @return void
     */
    public function testHasCoverageWithTotalSetLocaleCoverage(): void
    {
        $set = new TranslationSet('products', 'ini', new Protection(), [], new Filter(), [], [], []);

        $config = new Configuration([$set]);

        $this->assertFalse($config->hasCoverageSetting());

        $cov = new Coverage();
        $cov->addLocaleCoverage('DE', 37);

        $set->setCoverage($cov);

        $this->assertTrue($config->hasCoverageSetting());
    }
}
