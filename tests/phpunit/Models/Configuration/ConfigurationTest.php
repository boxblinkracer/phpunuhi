<?php

namespace phpunit\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Configuration\Coverage;
use PHPUnuhi\Models\Configuration\Coverage\TranslationSetCoverage;
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
    public function testCoverageCanBeConfigured(): void
    {
        $cov = new Coverage();
        $cov->setMinCoverage(36);

        $config = new Configuration([]);
        $config->setCoverage($cov);

        $this->assertSame($cov, $config->getCoverage());
    }

    /**
     * @return void
     */
    public function testHasCoverageWithGlobalMinCoverage(): void
    {
        $cov = new Coverage();
        $cov->setMinCoverage(36);

        $config = new Configuration([]);

        $this->assertFalse($config->hasCoverageSetting());

        $config->setCoverage($cov);

        $this->assertTrue($config->hasCoverageSetting());
    }

    /**
     * @return void
     */
    public function testHasCoverageWithGlobalLocaleCoverage(): void
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
    public function testHasCoverageWithTranslationSetMinCoverage(): void
    {
        $set = new TranslationSet('products', 'ini', new Protection(), [], new Filter(), [], [], []);

        $config = new Configuration([$set]);

        $this->assertFalse($config->hasCoverageSetting());

        $cov = new TranslationSetCoverage();
        $cov->setMinCoverage(25);

        $coverage = new Coverage();
        $coverage->addTranslationSetCoverage($set->getName(), $cov);

        $config->setCoverage($coverage);

        $this->assertTrue($config->hasCoverageSetting());
    }

    /**
     * @return void
     */
    public function testHasCoverageWithTranslationSetLocaleCoverage(): void
    {
        $set = new TranslationSet('products', 'ini', new Protection(), [], new Filter(), [], [], []);

        $config = new Configuration([$set]);

        $this->assertFalse($config->hasCoverageSetting());

        $cov = new TranslationSetCoverage();
        $cov->addLocaleCoverage('DE', 37);

        $coverage = new Coverage();
        $coverage->addTranslationSetCoverage($set->getName(), $cov);

        $config->setCoverage($coverage);

        $this->assertTrue($config->hasCoverageSetting());
    }

    /**
     * @return void
     */
    public function testHasCoverageIfOnlyOneTranslationSetHasCoverage(): void
    {
        $set1 = new TranslationSet('admin', 'ini', new Protection(), [], new Filter(), [], [], []);
        $set2 = new TranslationSet('storefront', 'ini', new Protection(), [], new Filter(), [], [], []);

        $config = new Configuration([$set1, $set2]);

        $this->assertFalse($config->hasCoverageSetting());

        $cov = new TranslationSetCoverage();
        $cov->setMinCoverage(25);

        $coverage = new Coverage();
        $coverage->addTranslationSetCoverage($set2->getName(), $cov);

        $config->setCoverage($coverage);

        $this->assertTrue($config->hasCoverageSetting());
    }
}
