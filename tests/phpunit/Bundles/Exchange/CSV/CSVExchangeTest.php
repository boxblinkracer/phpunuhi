<?php

namespace phpunit\Bundles\Exchange\CSV;

use Exception;
use PHPUnit\Framework\TestCase;
use phpunit\Utils\Fakes\FakeCSVWriter;
use PHPUnuhi\Bundles\Exchange\CSV\CSVExchange;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class CSVExchangeTest extends TestCase
{

    /**
     * @var FakeCSVWriter
     */
    private $fakeWriter;

    /**
     * @var CSVExchange
     */
    private $csv;


    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->fakeWriter = new FakeCSVWriter();

        $this->csv = new CSVExchange($this->fakeWriter);
    }


    /**
     * @return void
     */
    public function testGetName(): void
    {
        $this->assertEquals('csv', $this->csv->getName());
    }

    /**
     * @return void
     */
    public function testPossibleOptions(): void
    {
        $expected = [
            new CommandOption('csv-delimiter', true),
        ];

        $this->assertEquals($expected, $this->csv->getOptions());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testSetOptionsWithMissingDelimiterUsesDefaultDelimiter(): void
    {
        $options = [
            'csv-delimiter' => ' '
        ];

        $this->csv->setOptionValues($options);

        $this->assertEquals(',', $this->csv->getCsvDelimiter());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testSetOptions(): void
    {
        $options = [
            'csv-delimiter' => 'A'
        ];

        $this->csv->setOptionValues($options);

        $this->assertEquals('A', $this->csv->getCsvDelimiter());
    }


    /**
     * This test verifies that we can correctly export a CSV.
     * The export does not consider groups
     *
     * @throws TranslationNotFoundException
     * @return void
     */
    public function testExportWithoutGroups(): void
    {
        $localesDE = new Locale('de-DE', '', '');
        $localesDE->addTranslation('btnCancel', 'Abbrechen', '');
        $localesDE->addTranslation('btnOK', 'OK', '');

        $localesEN = new Locale('en-GB', '', '');
        $localesEN->addTranslation('btnCancel', 'Cancel', '');


        $set = new TranslationSet(
            '',
            'json',
            new Protection(),
            [$localesDE, $localesEN],
            new Filter(),
            [],
            new CaseStyleSetting([], []),
            []
        );

        $this->csv->export($set, '', false);

        $expected = [
            [
                'Key',
                'de-DE',
                'en-GB',
            ],
            [
                'btnCancel',
                'Abbrechen',
                'Cancel',
            ],
            [
                'btnOK',
                'OK',
                '',
            ]
        ];

        $this->assertEquals($expected, $this->fakeWriter->getWrittenLines());
    }

    /**
     * This test verifies that we can correctly export a CSV.
     * The export considers groups
     *
     * @throws TranslationNotFoundException
     * @return void
     */
    public function testExportWithGroups(): void
    {
        $localesDE = new Locale('de-DE', '', '');
        $localesDE->addTranslation('title', 'T-Shirt', 'ProductA');
        $localesDE->addTranslation('size', 'Mittel', 'ProductA');
        $localesDE->addTranslation('title', 'Hose', 'ProductB');

        $localesEN = new Locale('en-GB', '', '');
        $localesEN->addTranslation('size', 'Medium', 'ProductA');


        $set = new TranslationSet(
            '',
            'json',
            new Protection(),
            [$localesDE, $localesEN],
            new Filter(),
            [],
            new CaseStyleSetting([], []),
            []
        );

        $this->csv->export($set, '', false);

        $expected = [
            [
                'Group',
                'Key',
                'de-DE',
                'en-GB',
            ],
            [
                'ProductA',
                'title',
                'T-Shirt',
                '',
            ],
            [
                'ProductA',
                'size',
                'Mittel',
                'Medium',
            ],
            [
                'ProductB',
                'title',
                'Hose',
                '',
            ]
        ];

        $this->assertEquals($expected, $this->fakeWriter->getWrittenLines());
    }


    /**
     * This test verifies that we can correctly export a CSV.
     * In this case we only export empty translations
     *
     * @throws TranslationNotFoundException
     * @return void
     */
    public function testExportOnlyEmpty(): void
    {
        $de = new Locale('de-DE', '', '');
        $en = new Locale('en-GB', '', '');

        # must not be exported
        $de->addTranslation('title', 'Titel', '');
        $en->addTranslation('title', 'Title', '');

        # should be exported because 1 translation is missing
        $de->addTranslation('size', '', '');
        $en->addTranslation('size', 'Medium', '');

        # should be exported because 1 translation is missing (vice-versa)
        $de->addTranslation('subtitle', 'Untertitel', '');
        $en->addTranslation('subtitle', '', '');


        $set = new TranslationSet(
            '',
            'json',
            new Protection(),
            [$de, $en],
            new Filter(),
            [],
            new CaseStyleSetting([], []),
            []
        );

        $this->csv->export($set, '', true);

        $expected = [
            [
                'Key',
                'de-DE',
                'en-GB',
            ],
            [
                'size',
                '',
                'Medium',
            ],
            [
                'subtitle',
                'Untertitel',
                '',
            ],
        ];

        $this->assertEquals($expected, $this->fakeWriter->getWrittenLines());
    }
}
