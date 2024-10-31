<?php

namespace PHPUnuhi\Services\Process;

use Symfony\Component\Process\Process;

class OSProcess
{

    /**
     * @param string $command
     * @return ProcessResult
     */
    public function execute(string $command): ProcessResult
    {
        $process = Process::fromShellCommandline($command);

        $process->run();

        $errorOutput = $process->getErrorOutput();
        $output = $process->getOutput();

        return new ProcessResult($output, $errorOutput);
    }
}
