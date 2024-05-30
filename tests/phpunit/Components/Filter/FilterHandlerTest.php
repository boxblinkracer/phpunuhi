<?php

namespace phpunit\Components\Filter;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Filter\FilterHandler;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class FilterHandlerTest extends TestCase
{

    /**
     * This test verifies that our filter handler correctly
     * removes keys depending on our filters.
     * We create a new translation-set with locales and translations.
     * Then we create an exclude-filter entry and make sure
     * those translations are removed.
     *
     * @return void
     */
    public function testApplyFilter(): void
    {
        $handler = new FilterHandler();

        $localesDE = new Locale('de-DE', false, '', '');
        $localesDE->addTranslation('btnCancel', 'Abbrechen', 'group1');
        $localesDE->addTranslation('btnOK', 'OK', 'group1');

        $localesEN = new Locale('en-GB', false, '', '');
        $localesEN->addTranslation('btnCancel', 'Cancel', 'group1');
        $localesEN->addTranslation('btnOK', 'OK', 'group1');
        $localesEN->addTranslation('title', 'title', 'group1');


        $filter = new Filter();
        $filter->addExcludeKey('btnOK');

        $set = new TranslationSet(
            '',
            'json',
            new Protection(),
            [$localesDE, $localesEN],
            $filter,
            [],
            new CaseStyleSetting([], []),
            []
        );


        $this->assertCount(3, $set->getAllTranslationIDs());

        $handler->applyFilter($set);

        $this->assertCount(2, $set->getAllTranslationIDs());
    }
}
