<?php

namespace phpunit\Services\GroupName;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\GroupName\GroupNameService;

class GroupNameServiceTest extends TestCase
{

    /**
     * @return void
     */
    public function testGetGroupId()
    {
        $service = new GroupNameService();

        $groupId = $service->getGroupID('group--product_7d1abedd2d22436385580e2ff42431b9.pack_unit_plural');

        $this->assertEquals('product_7d1abedd2d22436385580e2ff42431b9', $groupId);
    }

    /**
     * @return void
     */
    public function testPropertyName()
    {
        $service = new GroupNameService();

        $propertyKey = $service->getPropertyName('group--product_7d1abedd2d22436385580e2ff42431b9.pack_unit_plural');

        $this->assertEquals('pack_unit_plural', $propertyKey);
    }

}