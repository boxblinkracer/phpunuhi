<?php

namespace PHPUnuhi\Models\Configuration;

class Marker
{

    /**
     * @var string
     */
    private $start;

    /**
     * @var string
     */
    private $end;


    /**
     * @param string $start
     * @param string $end
     */
    public function __construct(string $start, string $end)
    {
        $this->start = $start;
        $this->end = $end;
    }


    /**
     * @return string
     */
    public function getStart(): string
    {
        return $this->start;
    }

    /**
     * @return string
     */
    public function getEnd(): string
    {
        return $this->end;
    }
}
