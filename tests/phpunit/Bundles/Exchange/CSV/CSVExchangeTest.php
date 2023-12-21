<?php

namespace phpunit\Bundles\Exchange\CSV;

use PHPUnit\Framework\TestCase;
use phpunit\Utils\Fakes\FakeCSVWriter;
use PHPUnuhi\Bundles\Exchange\CSV\CSVExchange;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class CSVExchangeTest extends TestCase
{

    /**
     * This test verifies that we can correctly export a CSV.
     * The export does not consider groups
     * @return void
     */
    public function testExportWithoutGroups(): void
    {
        $fakeWriter = new FakeCSVWriter();

        $csv = new CSVExchange($fakeWriter);

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
            [],
            []
        );

        $csv->export($set, '', false);

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

        $this->assertEquals($expected, $fakeWriter->getWrittenLines());
    }

    /**
     * This test verifies that we can correctly export a CSV.
     * The export considers groups
     * @return void
     */
    public function testExportWithGroups(): void
    {
        $fakeWriter = new FakeCSVWriter();

        $csv = new CSVExchange($fakeWriter);

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
            [],
            []
        );

        $csv->export($set, '', false);

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

        $this->assertEquals($expected, $fakeWriter->getWrittenLines());
    }

}
