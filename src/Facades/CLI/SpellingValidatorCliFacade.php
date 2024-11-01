<?php

namespace PHPUnuhi\Facades\CLI;

use PHPUnuhi\Bundles\Spelling\OpenAI\OpenAISpellChecker;
use PHPUnuhi\Bundles\Spelling\Result\SpellingValidationResult;
use PHPUnuhi\Bundles\Spelling\SpellCheckerInterface;
use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\ReportSetResult;
use PHPUnuhi\Components\Reporter\Service\ReportTestResultConverter;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Text\Text;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\CommandOutputTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

class SpellingValidatorCliFacade
{
    use CommandOutputTrait;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var SpellCheckerInterface
     */
    private $spellChecker;

    /**
     * @param SymfonyStyle $io
     */
    public function __construct(SymfonyStyle $io, SpellCheckerInterface $spellChecker)
    {
        $this->io = $io;
        $this->spellChecker = $spellChecker;
    }

    /**
     * @param TranslationSet[] $translationSets
     * @throws TranslationNotFoundException
     * @return ReportResult
     */
    public function execute(array $translationSets): ReportResult
    {
        $reportResult = new ReportResult();

        $allTests = [];

        $reportConverter = new ReportTestResultConverter();


        $availableDictionaries = $this->spellChecker->getAvailableDictionaries();

        $this->io->writeln('Spell Checker: ' . $this->spellChecker->getName());
        $this->io->writeln('Available dictionaries:');
        $this->io->writeln(implode(', ', $availableDictionaries));

        foreach ($translationSets as $set) {
            $this->io->section('Validate spelling Translation Set: ' . $set->getName());

            $setResult = new ReportSetResult($set->getName());

            foreach ($set->getLocales() as $locale) {
                foreach ($locale->getValidTranslations() as $translation) {

                    # in case of placeholders, get the encoded text
                    $encodedText = $set->getEncodedValue($translation->getValue());

                    $spellingResult = $this->spellChecker->validate(
                        new Text($translation->getValue(), $encodedText),
                        $locale->getName()
                    );

                    $valTest = $this->buildTestValidation(
                        $translation->getID(),
                        $translation->getValue(),
                        $spellingResult,
                        $set
                    );

                    $reportResultTest = $reportConverter->toTestResult($valTest);

                    $setResult->addTestResult($reportResultTest);

                    $allTests[] = $valTest;
                }
            }

            $reportResult->addTranslationSet($setResult);
        }

        $this->showErrorTable($allTests, $this->io);

        if ($this->spellChecker instanceof OpenAISpellChecker) {
            $this->showOpenAIUsageData($this->io);
        }

        return $reportResult;
    }

    /**
     * @param string $translationID
     * @param string $originalText
     * @param SpellingValidationResult $spellingResult
     * @return ValidationTest
     */
    private function buildTestValidation(string $translationID, string $originalText, SpellingValidationResult $spellingResult, TranslationSet $set): ValidationTest
    {
        $failureMessage = 'Translation spelled incorrectly: ' . $originalText;

        if ($spellingResult->getMisspelledWords() !== []) {
            $failureMessage .= ' - Misspelled words: ';

            foreach ($spellingResult->getMisspelledWords() as $misspelledWord) {
                $failureMessage .= $misspelledWord->getWord();

                if ($misspelledWord->getSuggestions() !== []) {
                    $failureMessage .= ' (Suggestions: ' . implode(', ', $misspelledWord->getSuggestions()) . '), ';
                } else {
                    $failureMessage .= ', ';
                }
            }
        }

        if ($spellingResult->getSuggestedText() !== '') {

            # we probably used the encoded text with placeholders
            # for a better experience, try to decode it
            $decodedSuggestedText = $set->getDecodedText($originalText, $spellingResult->getSuggestedText());

            $failureMessage .= ' - Suggested text: ' . $decodedSuggestedText;
        }

        return new ValidationTest(
            $translationID,
            $spellingResult->getLocale(),
            'Test if translation for key ' . $translationID . ' is spelled correctly',
            '',
            0,
            'SPELLING',
            $failureMessage,
            $spellingResult->isValid()
        );
    }
}
