<?php

namespace MediaWiki\Extension\WikibaseDict;

use MediaWiki\MediaWikiServices;
use Wikibase\Client\WikibaseClient;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Services\Lookup\TermLookupException;
use ExtensionRegistry;

class WikibaseDictTag {
	
	function render($input, $argv) {
		// TODO: Should fetch all the langs instead of hardcoding them.
		$langs = ['en', 'fi', 'nl'];
		
		if (!ExtensionRegistry::getInstance()->isLoaded('WikibaseClient')) {
		  return null;
		}
		
		$contLang = MediaWikiServices::getInstance()->getContentLanguage();
		$termLookup = WikibaseClient::getTermLookup();
		try {
		  $itemId = new ItemId( $input );
		} catch ( InvalidArgumentException $exception ) {
		  return null;
		}
		
		try {
		  $labels = $termLookup->getLabels($itemId, $langs);
		  $output = '<table class="partiosanasto">
		  <tr><th colspan="2" class="partiosanasto_h1">Sana: ' . $labels[$contLang->getCode()] . ' <a href="https://dict.scoutwiki.org/">Partiosanastossa</a></th></tr>
		  <tr class="partiosanasto_h2"><th>Kieli</th><th>Sana</th></tr>';
		  foreach ($labels as $lang => $label) {
		    $output.= '<tr><th>' . $lang . '</th><td>' . $label . '</td></tr>';
		  }
		  $output .= '</table>';
		} catch ( TermLookupException $exception ) {
		  return null;
		}
		return $output;
	}

}
