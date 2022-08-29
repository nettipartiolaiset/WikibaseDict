<?php

namespace MediaWiki\Extension\WikibaseDict;

use ExtensionRegistry;
use MediaWiki\Languages\LanguageNameUtils;
use MediaWiki\MediaWikiServices;
use Wikibase\Client\WikibaseClient;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Services\Lookup\TermLookupException;
use Wikibase\Repo\WikibaseRepo;

class WikibaseDictTag {
	
	function render($input, $argv) {
		if (!ExtensionRegistry::getInstance()->isLoaded('WikibaseClient')) {
		  return null;
		}

		// Get the current language for the header.
		$contLang = MediaWikiServices::getInstance()->getContentLanguage();


		// Get a list of all the languages for the term filtering.
		$langUtils = MediaWikiServices::getInstance()->getLanguageNameUtils();
		// FIXME: This doesn't work either.
//		$langNames = $langUtils->getLanguageNames($contLang, LanguageNameUtils::ALL);

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
			$output = '<table class="partiosanasto">
			<tr><th colspan="2" class="partiosanasto_h1">Sana: ' . $labels[$contLang->getCode()] . ' <a href="https://dict.scoutwiki.org/">Partiosanastossa</a></th></tr>
			<tr class="partiosanasto_h2"><th>Kieli</th><th>Sana</th></tr>';
			foreach ($entity->getLabels() as $label) {
				// FIXME: Why doesn't this work?
//				$langName = $langUtils->getLanguageName($label->getLanguageCode(), $contLang);
				$langName = $langUtils->getLanguageName($label->getLanguageCode());
				$output.= '<tr><th>' . $langName . '</th><td>' . $label->getText() . '</td></tr>';
			}
			$output .= '</table>';
		} catch (TermLookupException $exception) {
			return null;
		}
		return $output;
	}

}
