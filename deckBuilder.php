<?php 

/**
* deckBuilder is a class for generating a deck of cart
*
* @author   lukasz2049
* @version  Rev 2
*/
class deckBuilder {
	public $lang; // language of the cards
	public $type = 'default'; // card type
	public $fieldDefTpl = 'fieldDef'; // template of a default field
	public $cardTpl = 'card'; // template of a card
	public $tplDir = 'tpl/'; // location of template directory
	public $libDir = 'lib/'; // location of library directory
	public $renderType = 'html'; // for future use (pdf support is planned)
	
	private $cards; // list of cards
	private $card = ''; // current card
	
	/**
	 *  @brief Loads csv from local or remote location
	 *  
	 *  @param string $path path to the csv file
	 *  
	 *  @details You can use download link of a public Google Sheet document.
	 */
	public function loadSheet ($path) {
		// Load the file
		$file = @file_get_contents($path);
		
		if($file === FALSE) {
			return FALSE;
		}
		
		// Parse the CSV
		require_once $this->libDir.'parsecsv.lib.php';
		$csv = new parseCSV ();
		$csv->parse($file);
		$this->cards = $csv->data;
	}
	
	/**
	 *  @brief Returns path of a template file
	 *  
	 *  @param string $tplName name of the template (without the .tpl extension)
	 *  @return string path of the template file
	 *  
	 */
	private function tplPath ($tplName) {
		return ($this->tplDir.$tplName.'.tpl');
	}
	
	/**
	 *  @brief Transliterates non-ASCII characters
	 *  
	 *  @param string $string
	 *  @return string
	 */
	private function safeChars ($string) {
		$string = trim($string);
		
		// Transliterate non-ascii characters
		$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		return $string;
	}

	/**
	 *  @brief Transforms string into filename-safe form
	 *  
	 *  @param string $string
	 *  @return string
	 */	
	private function stringToFilename ($string) {	
		$string = $this->safeChars($string);
		
		// Replace spaces with underscores
		$string = str_replace(' ', '_', $string);

		// Allow only alphanumeric, dash, underscore and dot
		$string = preg_replace('#[^-\.\_\w]+#', '', $string);
		
		// Max 255 characters in the filename
		$string = mb_substr($string, 0, 255);
		return $string;
	}
	
	/**
	 *  @brief Removes characters that aren't allowed in a CSS selector
	 *  
	 *  @param string $string
	 *  @return string
	 */		
	private function stringToSelector ($string) {
		$string = $this->safeChars($string);

		// Allow only alphanumeric, dash, underscore, space and []
		$string = preg_replace('#[^-\_\ \[\]\w]+#', '', $string);
		return $string;
	}
	
	/**
	 *  @brief rendereds a field
	 *  
	 *  @param string $fieldName name of the field
	 *  @param string $fieldTpl Name of the field template
	 *  @return Return description
	 *  
	 *  @details More details
	 */
	private function field ($fieldName, $fieldTpl = FALSE) {
		// If there's no field with such name
		// try using language-specific name 
		// (e.g. "Description EN" instead of "Description")
		if(!isset($this->card[$fieldName])) {
			$fieldName = $fieldName.' '.$this->lang;
		}
		
		// If field is empty or not does not exist
		if(!isset($this->card[$fieldName]) ||
		   empty($this->card[$fieldName])) {
			return FALSE;
		}
		
		// Default template
		if($fieldTpl == FALSE) {
			$fieldTpl = $this->fieldDefTpl;
		}
		
		// CSS class of the field
		$fieldClass = $this->stringToSelector($fieldName);
		// Text in the field
		$fieldText = $this->card[$fieldName];
		
		// In case of HTML rendering
		if($this->renderType == 'html') {
			$fieldTextHTML = $fieldText;
			$fieldText = htmlspecialchars($fieldText);
			$fieldText = nl2br($fieldText, FALSE);
		}
		
		// Use template if given template file exists
		if(file_exists($this->tplPath($fieldTpl))) {
			return include($this->tplPath($fieldTpl));
		}
		// It there's no such template simply return field text
		return $fieldText;
	}

	/**
	 *  @brief Checks if the deck is ready to render
	 *  
	 *  @return bool
	 *  
	 */
	public function isReady () {
		// Check if properties of all objects are set
		foreach(get_object_vars($this) as $key => $obj) {
			if($obj === NULL) {
				if ($key === 'cards') { 
					throw new Exception ("The CSV file isn't loaded");
				} else {
					throw new Exception ("Param '$key' is not set");
				}
			}
		}
		// Check if card template file exist
		if(!file_exists($this->tplPath($this->cardTpl))) {
			throw new Exception ("Card Template file does not exists!");
		}
		// If no errors were thrown
		return TRUE;
	}
	
	/**
	 *  @brief Renders a deck
	 *  
	 *  @param bool $returnAsArray if true deck is returned as array of cards
	 *  @return string|array 
	 *  
	 */
	public function render ($returnAsArray = FALSE) {
		$caught = FALSE;
		$return = FALSE;
		
		// Check if deck is ready
		try { 
			$this->isReady();
		} catch (Exception $e) { 
			$caught = TRUE;
			if($this->renderType == 'html') {
				echo 'Error: ',  $e->getMessage(), "\n"; 
			} else {
				error_log($e->getMessage());
			}
		} 
		// Render the deck if no errors were caught
		if($caught === FALSE) {
			foreach($this->cards as $this->card) {
				// Returns array or string of cards
				if($returnAsArray) {
					$return[] = include($this->tplPath($this->cardTpl));
				} else {
					$return .= include($this->tplPath($this->cardTpl));
				}
			}
			return $return;
		}
	}
	
	/**
	 *  @brief Debug info
	 *  
	 *  @return array properties of the object
	 *  
	 */
	public function debug () {
		return get_object_vars($this);
	}
}