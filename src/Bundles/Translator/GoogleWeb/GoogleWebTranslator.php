<?php

namespace PHPUnuhi\Bundles\Translation\GoogleWeb;

use PHPUnuhi\Bundles\Translation\TranslatorInterface;

class GoogleWebTranslator implements TranslatorInterface
{

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

        return $result;
    }


}