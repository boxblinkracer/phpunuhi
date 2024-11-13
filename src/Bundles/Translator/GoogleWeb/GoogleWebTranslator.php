<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Translator\GoogleWeb;

use Exception;
use PHPUnuhi\Bundles\Translator\TranslatorInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Services\Placeholder\Placeholder;
use PHPUnuhi\Services\Placeholder\PlaceholderEncoder;

class GoogleWebTranslator implements TranslatorInterface
{
    private PlaceholderEncoder $placeholderEncoder;


    public function __construct()
    {
        $this->placeholderEncoder = new PlaceholderEncoder();
    }


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
     */
    public function setOptionValues(array $options): void
    {
    }

    /**
     * @param Placeholder[] $foundPlaceholders
     * @throws Exception
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale, array $foundPlaceholders): string
    {
        $text = $this->placeholderEncoder->encode($text, $foundPlaceholders);

        $curl = curl_init();

        # dots are not working in urls with encode,
        # so we replace it temporarily :)
        $text = str_replace('.', "[[dot]]", $text);
        $text = str_replace('.', "[[Punkt]]", $text);

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
        $result = str_replace("[[Punkt]]", '.', $result);

        if ($foundPlaceholders !== []) {
            # decode our string so that we have the original placeholder values again (%productName%)
            return $this->placeholderEncoder->decode($result, $foundPlaceholders);
        }

        return $result;
    }
}
