<?php namespace Conner\Tagging;

use Conner\Tagging\Tag;

/**
 * Utility functions to help with various tagging functionality.
 *
 * @author Rob Conner <rtconner@gmail.com>
 */
class TaggingUtil {
	
	/**
	 * Converts input into array
	 *
	 * @param $tagName string or array
	 * @return array
	 */
	public static function makeTagArray($tagNames) {
		if(is_string($tagNames)) {
			$tagNames = explode(',', $tagNames);
		} elseif(!is_array($tagNames)) {
			$tagNames = array(null);
		}
		
		$tagNames = array_map('trim', $tagNames);

		return $tagNames;
	}
	
	/**
	 * Create a web friendly URL slug from a string.
	 *
	 * Although supported, transliteration is discouraged because
	 * 1) most web browsers support UTF-8 characters in URLs
	 * 2) transliteration causes a loss of information
	 *
	 * @author Sean Murphy <sean@iamseanmurphy.com>
	 *
	 * @param string $str
	 * @return string
	 */
	public static function slug($str) {
		
		// Make sure string is in UTF-8 and strip invalid UTF-8 characters
		$str = mb_convert_encoding((string)$str, 'UTF-8');

		$options = array(
			'delimiter' => '-',
			'limit' => 255,
			'lowercase' => true,
			'replacements' => array(),
			'transliterate' => true,
		);
		
		$char_map = array(
			// Latin
			'Ã€' => 'A', 'Ã�' => 'A', 'Ã‚' => 'A', 'Ãƒ' => 'A', 'Ã„' => 'A', 'Ã…' => 'A', 'Ã†' => 'AE', 'Ã‡' => 'C',
			'Ãˆ' => 'E', 'Ã‰' => 'E', 'ÃŠ' => 'E', 'Ã‹' => 'E', 'ÃŒ' => 'I', 'Ã�' => 'I', 'ÃŽ' => 'I', 'Ã�' => 'I',
			'Ã�' => 'D', 'Ã‘' => 'N', 'Ã’' => 'O', 'Ã“' => 'O', 'Ã”' => 'O', 'Ã•' => 'O', 'Ã–' => 'O', 'Å�' => 'O',
			'Ã˜' => 'O', 'Ã™' => 'U', 'Ãš' => 'U', 'Ã›' => 'U', 'Ãœ' => 'U', 'Å°' => 'U', 'Ã�' => 'Y', 'Ãž' => 'TH',
			'ÃŸ' => 'ss',
			'Ã ' => 'a', 'Ã¡' => 'a', 'Ã¢' => 'a', 'Ã£' => 'a', 'Ã¤' => 'a', 'Ã¥' => 'a', 'Ã¦' => 'ae', 'Ã§' => 'c',
			'Ã¨' => 'e', 'Ã©' => 'e', 'Ãª' => 'e', 'Ã«' => 'e', 'Ã¬' => 'i', 'Ã­' => 'i', 'Ã®' => 'i', 'Ã¯' => 'i',
			'Ã°' => 'd', 'Ã±' => 'n', 'Ã²' => 'o', 'Ã³' => 'o', 'Ã´' => 'o', 'Ãµ' => 'o', 'Ã¶' => 'o', 'Å‘' => 'o',
			'Ã¸' => 'o', 'Ã¹' => 'u', 'Ãº' => 'u', 'Ã»' => 'u', 'Ã¼' => 'u', 'Å±' => 'u', 'Ã½' => 'y', 'Ã¾' => 'th',
			'Ã¿' => 'y',
			 
			// Latin symbols
			'Â©' => '(c)',
			 
			// Greek
			'Î‘' => 'A', 'Î’' => 'B', 'Î“' => 'G', 'Î”' => 'D', 'Î•' => 'E', 'Î–' => 'Z', 'Î—' => 'H', 'Î˜' => '8',
			'Î™' => 'I', 'Îš' => 'K', 'Î›' => 'L', 'Îœ' => 'M', 'Î�' => 'N', 'Îž' => '3', 'ÎŸ' => 'O', 'Î ' => 'P',
			'Î¡' => 'R', 'Î£' => 'S', 'Î¤' => 'T', 'Î¥' => 'Y', 'Î¦' => 'F', 'Î§' => 'X', 'Î¨' => 'PS', 'Î©' => 'W',
			'Î†' => 'A', 'Îˆ' => 'E', 'ÎŠ' => 'I', 'ÎŒ' => 'O', 'ÎŽ' => 'Y', 'Î‰' => 'H', 'Î�' => 'W', 'Îª' => 'I',
			'Î«' => 'Y',
			'Î±' => 'a', 'Î²' => 'b', 'Î³' => 'g', 'Î´' => 'd', 'Îµ' => 'e', 'Î¶' => 'z', 'Î·' => 'h', 'Î¸' => '8',
			'Î¹' => 'i', 'Îº' => 'k', 'Î»' => 'l', 'Î¼' => 'm', 'Î½' => 'n', 'Î¾' => '3', 'Î¿' => 'o', 'Ï€' => 'p',
			'Ï�' => 'r', 'Ïƒ' => 's', 'Ï„' => 't', 'Ï…' => 'y', 'Ï†' => 'f', 'Ï‡' => 'x', 'Ïˆ' => 'ps', 'Ï‰' => 'w',
			'Î¬' => 'a', 'Î­' => 'e', 'Î¯' => 'i', 'ÏŒ' => 'o', 'Ï�' => 'y', 'Î®' => 'h', 'ÏŽ' => 'w', 'Ï‚' => 's',
			'ÏŠ' => 'i', 'Î°' => 'y', 'Ï‹' => 'y', 'Î�' => 'i',
			 
			// Turkish
			'Åž' => 'S', 'Ä°' => 'I', 'Ã‡' => 'C', 'Ãœ' => 'U', 'Ã–' => 'O', 'Äž' => 'G',
			'ÅŸ' => 's', 'Ä±' => 'i', 'Ã§' => 'c', 'Ã¼' => 'u', 'Ã¶' => 'o', 'ÄŸ' => 'g',
			 
			// Russian
			'Ð�' => 'A', 'Ð‘' => 'B', 'Ð’' => 'V', 'Ð“' => 'G', 'Ð”' => 'D', 'Ð•' => 'E', 'Ð�' => 'Yo', 'Ð–' => 'Zh',
			'Ð—' => 'Z', 'Ð˜' => 'I', 'Ð™' => 'J', 'Ðš' => 'K', 'Ð›' => 'L', 'Ðœ' => 'M', 'Ð�' => 'N', 'Ðž' => 'O',
			'ÐŸ' => 'P', 'Ð ' => 'R', 'Ð¡' => 'S', 'Ð¢' => 'T', 'Ð£' => 'U', 'Ð¤' => 'F', 'Ð¥' => 'H', 'Ð¦' => 'C',
			'Ð§' => 'Ch', 'Ð¨' => 'Sh', 'Ð©' => 'Sh', 'Ðª' => '', 'Ð«' => 'Y', 'Ð¬' => '', 'Ð­' => 'E', 'Ð®' => 'Yu',
			'Ð¯' => 'Ya',
			'Ð°' => 'a', 'Ð±' => 'b', 'Ð²' => 'v', 'Ð³' => 'g', 'Ð´' => 'd', 'Ðµ' => 'e', 'Ñ‘' => 'yo', 'Ð¶' => 'zh',
			'Ð·' => 'z', 'Ð¸' => 'i', 'Ð¹' => 'j', 'Ðº' => 'k', 'Ð»' => 'l', 'Ð¼' => 'm', 'Ð½' => 'n', 'Ð¾' => 'o',
			'Ð¿' => 'p', 'Ñ€' => 'r', 'Ñ�' => 's', 'Ñ‚' => 't', 'Ñƒ' => 'u', 'Ñ„' => 'f', 'Ñ…' => 'h', 'Ñ†' => 'c',
			'Ñ‡' => 'ch', 'Ñˆ' => 'sh', 'Ñ‰' => 'sh', 'ÑŠ' => '', 'Ñ‹' => 'y', 'ÑŒ' => '', 'Ñ�' => 'e', 'ÑŽ' => 'yu',
			'Ñ�' => 'ya',
			 
			// Ukrainian
			'Ð„' => 'Ye', 'Ð†' => 'I', 'Ð‡' => 'Yi', 'Ò�' => 'G',
			'Ñ”' => 'ye', 'Ñ–' => 'i', 'Ñ—' => 'yi', 'Ò‘' => 'g',
			 
			// Czech
			'ÄŒ' => 'C', 'ÄŽ' => 'D', 'Äš' => 'E', 'Å‡' => 'N', 'Å˜' => 'R', 'Å ' => 'S', 'Å¤' => 'T', 'Å®' => 'U',
			'Å½' => 'Z',
			'Ä�' => 'c', 'Ä�' => 'd', 'Ä›' => 'e', 'Åˆ' => 'n', 'Å™' => 'r', 'Å¡' => 's', 'Å¥' => 't', 'Å¯' => 'u',
			'Å¾' => 'z',
			 
			// Polish
			'Ä„' => 'A', 'Ä†' => 'C', 'Ä˜' => 'e', 'Å�' => 'L', 'Åƒ' => 'N', 'Ã“' => 'o', 'Åš' => 'S', 'Å¹' => 'Z',
			'Å»' => 'Z',
			'Ä…' => 'a', 'Ä‡' => 'c', 'Ä™' => 'e', 'Å‚' => 'l', 'Å„' => 'n', 'Ã³' => 'o', 'Å›' => 's', 'Åº' => 'z',
			'Å¼' => 'z',
			 
			// Latvian
			'Ä€' => 'A', 'ÄŒ' => 'C', 'Ä’' => 'E', 'Ä¢' => 'G', 'Äª' => 'i', 'Ä¶' => 'k', 'Ä»' => 'L', 'Å…' => 'N',
			'Å ' => 'S', 'Åª' => 'u', 'Å½' => 'Z',
			'Ä�' => 'a', 'Ä�' => 'c', 'Ä“' => 'e', 'Ä£' => 'g', 'Ä«' => 'i', 'Ä·' => 'k', 'Ä¼' => 'l', 'Å†' => 'n',
			'Å¡' => 's', 'Å«' => 'u', 'Å¾' => 'z'
		);
		
		// Make custom replacements
		$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
		
		// Transliterate characters to ASCII
		if ($options['transliterate']) {
			$str = str_replace(array_keys($char_map), $char_map, $str);
		}
		// Replace non-alphanumeric characters with our delimiter
		$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
		
		// Remove duplicate delimiters
		$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
		
		// Truncate slug to max. characters
		$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
		
		// Remove delimiter from ends
		$str = trim($str, $options['delimiter']);

		return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
	}

	/**
	 * Private! Please do not call this function directly, just let the Tag library use it.
	 * Increment count of tag by one. This function will create tag record if it does not exist.
	 *
	 * @param string $tagString
	 */
	public static function incrementCount($tagString, $tagSlug, $count) {
		if($count <= 0) { return; }
		
		$tag = Tag::where('slug', '=', $tagSlug)->first();

		if(!$tag) {
			$tag = new Tag;
			$tag->name = $tagString;
			$tag->slug = $tagSlug;
			$tag->suggest = false;
			$tag->save();
		}
		
		$tag->count = $tag->count + $count;
		$tag->save();
	}
	
	/**
	 * Private! Please do not call this function directly, let the Tag library use it.
	 * Decrement count of tag by one. This function will create tag record if it does not exist.
	 *
	 * @param string $tagString
	 */
	public static function decrementCount($tagString, $tagSlug, $count) {
		if($count <= 0) { return; }
		
		$tag = Tag::where('slug', '=', $tagSlug)->first();
	
		if($tag) {
			$tag->count = $tag->count - $count;
			if($tag->count < 0) {
				$tag->count = 0;
				\Log::warning("The \Conner\Tagging\Tag count for `$tag->name` was a negative number. This probably means your data got corrupted. Please assess your code and report an issue if you find one.");
			}
			$tag->save();
		}
	}
	
}