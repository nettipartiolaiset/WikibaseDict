<?php

namespace MediaWiki\Extension\WikibaseDict;

use ExtensionRegistry;
use MediaWiki\Languages\LanguageNameUtils;
use MediaWiki\MediaWikiServices;
use Wikibase\Client\WikibaseClient;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Services\Lookup\TermLookupException;
use Wikibase\Lib\LanguageNameLookup;
use Wikibase\Repo\WikibaseRepo;

class WikibaseDictTag {
	
	function render($input, $argv) {
		if (!ExtensionRegistry::getInstance()->isLoaded('WikibaseClient')) {
		  return null;
		}

		// Get the current language for the header.
		$contLang = MediaWikiServices::getInstance()->getContentLanguage()->getCode();

		// Get a list of all the languages for the term filtering.
//		$langUtils = MediaWikiServices::getInstance()->getLanguageNameUtils();
		$languageNameLookup = new LanguageNameLookup($contLang);

		// Get the entity lookup service.
		$entityLookup = WikibaseClient::getEntityLookup();
		// Create the item filter.
		try {
			$itemId = new ItemId($input);
		} catch (InvalidArgumentException $exception) {
			return null;
		}
		
		try {
			$entity = $entityLookup->getEntity($itemId);

			$labelOutput = '';
			$defaultLabel = NULL;
			foreach ($entity->getLabels() as $label) {
				if ($defaultLabel === NULL || $label->getLanguageCode() == $contLang) {
					$defaultLabel = $label->getText();
				}
				// Skip the current language.
				if ($label->getLanguageCode() == $contLang) {
					continue;
				}
//				$langName = $langUtils->getLanguageName($label->getLanguageCode(), $contLang);
				$langName = $languageNameLookup->getName($label->getLanguageCode());
				$labelOutput .= '<tr><th>' . $langName . '</th><td>' . $label->getText() . '</td></tr>';
			}

			$output = '<table class="partiosanasto">
			<tr><th colspan="2" class="partiosanasto_h1">Sana: ' . $defaultLabel . ' <a href="https://dict.scoutwiki.org/">Partiosanastossa</a></th></tr>
			<tr class="partiosanasto_h2"><th>Kieli</th><th>Sana</th></tr>';
			$output .= $labelOutput;
			$output .= '</table>';
		} catch (TermLookupException $exception) {
			return null;
		}
		return $output;
	}

}
