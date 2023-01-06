<?php

namespace PHPUnuhi\Bundles\Exchange\HTML;

use PHPUnuhi\Bundles\Exchange\ExportInterface;
use PHPUnuhi\Models\Translation\TranslationSet;

class HTMLExporter implements ExportInterface
{

    /**
     * @param TranslationSet $set
     * @param string $outputDir
     * @return void
     */
    public function export(TranslationSet $set, string $outputDir): void
    {
        $html = "";

        $html .= "<html>";
        $html .= "<body>";

        $html .= "
            <style>
                table, th, td {
                  border: 1px solid black;
                  padding: 20px;
                }
            </style>
        ";

        $html .= '
            <script type ="text/javascript">
                function download() {
                
                    const setName = document.getElementById("set").value;
                    const translations = document.getElementsByClassName("translation");
                    
                    let output = "";
                    
                    Array.from(translations).forEach(element => {
                        output += element.id + "=" + element.value + "\n";
                    });

                    const link = document.createElement("a");
                    const file = new Blob([output], { type: "text/plain" });
                    
                    link.href = URL.createObjectURL(file);
                    link.download = "phpunuhi_" + setName + ".txt";
                    
                    link.click();
                    URL.revokeObjectURL(link.href);
                }
            </script>
        ';

        $html .= "<form name=\"gridForm\">";
        $html .= " <input type=\"hidden\" id=\"set\" value=\"" . $set->getName() . "\"></input>";
        $html .= "<table>";

        $html .= "<tr>";
        $html .= "<td>Key</td>";
        foreach ($set->getLocales() as $locale) {
            $html .= "<td>" . $locale->getLocale() . "</td>";
        }
        $html .= "</tr>";

        foreach ($set->getAllTranslationKeys() as $key) {

            $html .= "<tr>";

            $html .= "<td>" . $key . "</td>";

            foreach ($set->getLocales() as $locale) {
                foreach ($locale->getTranslations() as $translation) {

                    if ($translation->getKey() === $key) {
                        $html .= "<td>";
                        $html .= "<input id=\"" . $key . "--" . $translation->getLocale() . "\" class=\"translation\" type=\"text\" value=\"" . $translation->getValue() . "\"></input>";
                        $html .= "</td>";
                    }
                }
            }


            $html .= "</tr>";
        }

        $html .= "</table>";

        $html .= "<input type=\"button\" onclick=\"download();\" value=\"Save\"></input>";
        $html .= "</form>";

        $html .= "</body>";
        $html .= "</html>";


        file_put_contents($outputDir . '/index.html', $html);
    }

}