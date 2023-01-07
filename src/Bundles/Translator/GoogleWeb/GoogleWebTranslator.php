<?php

namespace PHPUnuhi\Bundles\Translation\GoogleWeb;

use PHPUnuhi\Bundles\Translation\TranslatorInterface;

class GoogleWebTranslator implements TranslatorInterface
{

    /**
     * @param string $text
     * @param string $sourceLanguage
     * @param string $targetLanguage
     * @return string
     */
    public function translate(string $text, string $sourceLanguage, string $targetLanguage): string
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://translate.googleapis.com/translate_a/single?client=gtx&sl=" . $sourceLanguage . "&tl=" . $targetLanguage . "&hl=en-US&dt=t&dt=bd&dj=1&source=icon&tk=310461.310461&q=" . urlencode($text),
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

        return (string)$json['sentences'][0]['trans'];
    }


}