<?php

namespace PHPUnuhi\Traits;

use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Services\OpenAI\OpenAIUsageTracker;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;

trait CommandOutputTrait
{

    /**
     * @param ValidationTest[] $rows
     * @param OutputInterface $output
     * @return void
     */
    public function showErrorTable(array $rows, OutputInterface $output): void
    {
        if ($rows === []) {
            return;
        }

        # return if we have no success false entries
        $foundError = false;
        foreach ($rows as $row) {
            if (!$row->isSuccess()) {
                $foundError = true;
                break;
            }
        }

        if (!$foundError) {
            return;
        }

        $table = new Table($output);
        $table->setStyle('default');

        $headers = [
            '#',
            'Type',
            'Locale',
            'Key',
            'Error',
        ];
        $table->setHeaders($headers);

        $rowData = [];

        $intRowIndex = 1;

        foreach ($rows as $row) {
            if ($row->isSuccess()) {
                continue;
            }

            $rowData[] = [
                $intRowIndex,
                ' ' . $row->getClassification() . ' ',
                '  ' . $row->getLocale() . ' ',
                ' ' . $row->getTranslationKey() . ' ',
                $row->getFailureMessage()
            ];


            if (!empty($row->getLineNumber())) {
                $lineError = '// Line ' . $row->getLineNumber() . ' in file: ' . $row->getFilename();

                $rowData[] = [
                    new TableCell(''),
                    new TableCell(''),
                    new TableCell(''),
                    new TableCell(''),
                    new TableCell(
                        $lineError,
                        [
                            'colspan' => count($headers) - 4
                        ]
                    )];
            }

            # don't add as last line
            if ($row !== end($rows)) {
                $rowData[] = new TableSeparator();
            }

            $intRowIndex++;
        }
        $table->setRows($rowData);

        $table->render();

        $output->writeln('');
    }

    protected function showOpenAIUsageData(OutputInterface $output): void
    {
        $tracker = OpenAIUsageTracker::getInstance();

        $output->writeln("\n=== OpenAI Usage Summary =====================");
        $output->writeln("Total Requests:    " . $tracker->getRequestCount());
        $output->writeln("Estimated Costs:   " . $tracker->getTotalCostsUSD() . " USD (approx.)");
        $output->writeln("==============================================\n");
    }
}
