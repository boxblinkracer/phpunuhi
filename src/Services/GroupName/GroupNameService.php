<?php

namespace PHPUnuhi\Services\GroupName;

use PHPUnuhi\Traits\StringTrait;

class GroupNameService
{

    use StringTrait;


    /**
     * @param string $translationId
     * @return string
     */
    public function getGroupID(string $translationId): string
    {
        $isGroup = $this->stringDoesStartsWith($translationId, 'group--');

        if (!$isGroup) {
            return '';
        }

        $group = explode('.', $translationId)[0];

        return (string)str_replace('group--', '', $group);
    }

    /**
     * @param string $translationId
     * @return string
     */
    public function getPropertyName(string $translationId): string
    {
        if (!$this->stringDoesStartsWith($translationId, 'group--')) {
            return $translationId;
        }

        $group = explode('.', $translationId)[0];
        $group = str_replace('group--', '', $group);

        return (string)str_replace('group--' . $group . '.', '', $translationId);
    }

}
