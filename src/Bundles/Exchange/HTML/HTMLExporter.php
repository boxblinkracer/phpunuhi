<?php

namespace PHPUnuhi\Bundles\Exchange\HTML;

use PHPUnuhi\Bundles\Exchange\ExportInterface;
use PHPUnuhi\Models\Translation\Locale;
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

        $html .= "<head>";

        $html .= "<title>PHPUnuhi - " . $set->getName() . "</title>";


        $html .= "<style>";
        $html .= $this->getCSS();
        $html .= "</style>";

        $html .= $this->getJavascript();

        $html .= "</head>";


        $html .= '
        <body>
            <div class="table-wrapper">
        ';

        $html .= '
                <h1>PHPUnuhi</h1>
                <h2>The easy framework to validate and manage translation files!</h2>
                <div class="alert">
                     Please go through the list of translations and adjust the values for all locales.<br />
                     Afterwards click on "Save Translations".<br />
                     This will download a file that you can then import again into your software by using the PHPUnuhi import command.<br />
                     <br />
                     You can read more about the framework at <a href="https://github.com/boxblinkracer/phpunuhi" target="_blank" style="color: white;">https://github.com/boxblinkracer/phpunuhi</a>
                </div>
        ';


        $html .= "<form name=\"gridForm\">";
        $html .= "<input type=\"button\" class=\"btn-save\" onclick=\"download();\" value=\"Save Translations\"/>";

        $html .= " <input type=\"hidden\" id=\"set\" value=\"" . $set->getName() . "\"></input>";

        $html .= "<h3>Translation Set: " . $set->getName() . "</h3>";


        $html .= "<table>";

        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>Keys (" . count($set->getAllTranslationKeys()) . ")</th>";
        foreach ($set->getLocales() as $locale) {
            $html .= "<th>";
            $html .= $locale->getName();
            $html .= "</th>";
        }
        $html .= "</tr>";
        $html .= "</thead>";

        $html .= "<tbody>";
        foreach ($set->getAllTranslationKeys() as $key) {

            $html .= "<tr>";

            $html .= "<td>" . $key . "</td>";

            foreach ($set->getLocales() as $locale) {

                $value = $this->getTranslationValue($locale, $key);

                $value = htmlentities($value);

                $html .= '
                        <td>
                            <input 
                                id="' . $key . '--' . $locale->getExchangeIdentifier() . '" 
                                class="translation textfield" 
                                type="text" 
                                value="' . $value . '"/>
                        </td>
                        ';
            }


            $html .= "</tr>";
        }
        $html .= "</tbody>";

        $html .= "</table>";

        $html .= "<input type=\"button\" class=\"btn-save\" onclick=\"download();\" value=\"Save Translations\"/>";


        $html .= "</form>";

        $html .= "</div>";


        $html .= "</body>";
        $html .= "</html>";

        if (!file_exists($outputDir)) {
            mkdir($outputDir);
        }

        file_put_contents($outputDir . '/index.html', $html);
    }

    /**
     * @param Locale $locale
     * @param string $key
     * @return string
     */
    private function getTranslationValue(Locale $locale, string $key): string
    {
        foreach ($locale->getTranslations() as $translation) {

            if ($translation->getKey() === $key) {

                return $translation->getValue();
            }
        }

        return "";
    }

    private function getCSS(): string
    {
        return '
html,body {
  margin: 20px;
  padding: 0;
  height: 100%;
}

body {
  display: flex;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
  font-size: 90%;
  color: #333;
  justify-content: center;
}

.table-wrapper {
  max-width: 100%;
  overflow: scroll;
}

table {
  position: relative;
  border: 1px solid #ddd;
  border-collapse: collapse;
}

td, th {
  white-space: nowrap;
  border: 1px solid #ddd;
  padding: 10px;
  text-align: center;
}

th {
  background-color: #eee;
  position: sticky;
  top: -1px;
  z-index: 2;
  
  &:first-of-type {
    left: 0;
    z-index: 3;
  }
}

tbody tr td:first-of-type {
  background-color: #eee;

  left: -1px;
  text-align: left;
}

.textfield {
  height: 40px;
  min-width: 400px;
}

.alert {
  padding: 20px;
  background-color: #5b5b5b; 
  color: white;
  margin-top: 25px;
  margin-bottom: 25px;
}

.btn-save {
  background-color: #04AA6D;
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  cursor: pointer;
  
  margin-top:30px;
  margin-bottom:20px;
  float: right;
}
        ';
    }

    private function getJavascript(): string
    {
        return '
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
                    link.download = "phpunuhi_" + setName + ".html.txt";
                    
                    link.click();
                    URL.revokeObjectURL(link.href);
                }
            </script>
        ';
    }
}