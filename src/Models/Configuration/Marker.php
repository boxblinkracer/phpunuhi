<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Configuration;

class Marker
{
    private string $start;

    private string $end;



    public function __construct(string $start, string $end)
    {
        $this->start = $start;
        $this->end = $end;
    }



    public function getStart(): string
    {
        return $this->start;
    }


    public function getEnd(): string
    {
        return $this->end;
    }
}
