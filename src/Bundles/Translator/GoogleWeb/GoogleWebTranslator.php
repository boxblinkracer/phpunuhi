<?php

namespace PHPUnuhi\Bundles\Translator\GoogleWeb;

use PHPUnuhi\Bundles\Translator\TranslatorInterface;
use PHPUnuhi\Models\Command\CommandOption;

class GoogleWebTranslator implements TranslatorInterface
{


    /**
     * @return string
     */
    public function getName(): string
    {
        return 'googleweb';
    }

    /**
     * @return CommandOption[]
     */
    public function getOptions(): array
    {
        return [];
    }

    /**
     * @param array<mixed> $options
     * @return void
     */
    public function setOptionValues(array $options): void
    {
    }

    /**
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @return string
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale): string
    {
        $curl = curl_init();

        # dots are not working in urls with encode,
        # so we replace it temporarily :)
        $text = str_replace('.', "[[dot]]", $text);

        $encodedText = urlencode($text);

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://translate.googleapis.com/translate_a/single?client=gtx&sl=" . $sourceLocale . "&tl=" . $targetLocale . "&hl=en-US&dt=t&dt=bd&dj=1&source=icon&tk=310461.310461&q=" . $encodedText,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $json = json_decode((string)$response, true);

        if (!is_array($json)) {
            return "";
        }

        $result = (string)$json['sentences'][0]['trans'];

        $result = str_replace("[[dot]]", '.', $result);
        $result = str_replace("[[punt]]", '.', $result);

        return $result;
    }


}