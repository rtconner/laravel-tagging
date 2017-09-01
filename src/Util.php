<?php namespace Conner\Tagging;

use Conner\Tagging\Contracts\TaggingUtility;

/**
 * Utility functions to help with various tagging functionality.
 *
 * @author Rob Conner <rtconner@gmail.com>
 *
 * Copyright (C) 2014 Robert Conner
 */
class Util implements TaggingUtility
{
	/**
	 * Converts input into array
	 *
	 * @param $tagName string or array
	 * @return array
	 */
	public function makeTagArray($tagNames)
	{
		if(is_array($tagNames) && count($tagNames) == 1) {
			$tagNames = reset($tagNames);
		}
		
		if(is_string($tagNames)) {
			$tagNames = explode(',', $tagNames);
		} elseif(!is_array($tagNames)) {
			$tagNames = array(null);
		}
		
		$tagNames = array_map('trim', $tagNames);

		return array_values($tagNames);
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
	public static function slug($str)
	{
		// Make sure string is in UTF-8 and strip invalid UTF-8 characters
		$str = mb_convert_encoding((string)$str, 'UTF-8');
	
		$options = array(
			'delimiter' => config('taggable.delimiter', '-'),
			'limit' => '255',
			'lowercase' => true,
			'replacements' => array(),
			'transliterate' => true,
		);
	
		$char_map = array(
				// Latin
				'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
				'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
				'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
				'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
				'ß' => 'ss',
				'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
				'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
				'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
				'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
				'ÿ' => 'y',
	
				// Latin symbols
				'©' => '(c)',
	
				// Greek
				'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
				'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
				'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
				'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
				'Ϋ' => 'Y',
				'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
				'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
				'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
				'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
				'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
	
				// Turkish
				'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
				'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
	
				// Russian
				'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
				'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
				'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
				'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
				'Я' => 'Ya',
				'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
				'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
				'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
				'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
				'я' => 'ya',
	
				// Ukrainian
				'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
				'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
	
				// Czech
				'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
				'Ž' => 'Z',
				'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
				'ž' => 'z',
	
				// Polish
				'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
				'Ż' => 'Z',
				'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
				'ż' => 'z',
	
				// Latvian
				'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
				'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
				'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
				'š' => 's', 'ū' => 'u', 'ž' => 'z',
					
				//Romanian
				'Ă' => 'A', 'ă' => 'a', 'Ș' => 'S', 'ș' => 's', 'Ț' => 'T', 'ț' => 't',

				//Vietnamese
				'ả' => 'a', 'Ả' => 'A','ạ' => 'a', 'Ạ' => 'A', 'ắ' => 'a', 'Ắ' => 'A', 'ằ' => 'a', 'Ằ' => 'A',
				'ẳ' => 'a', 'Ẳ' => 'A', 'ẵ' => 'a', 'Ẵ' => 'A', 'ặ' => 'a', 'Ặ' => 'A', 'ẩ' => 'a', 'Ẩ' => 'A',
				'Ấ' => 'A', 'ấ' => 'a', 'Ầ' => 'A', 'ầ' => 'a', 'Ơ' => 'O', 'ơ' => 'o', 'Đ' => 'D', 'đ' => 'd',
				'ẫ' => 'a', 'Ẫ' => 'A', 'ậ' => 'a', 'Ậ' => 'A', 'ẻ' => 'e', 'Ẻ' => 'E', 'ẽ' => 'e', 'Ẽ' => 'E',
				'ẹ' => 'e', 'Ẹ' => 'E', 'ế' => 'e', 'Ế' => 'E', 'ề' => 'e', 'Ề' => 'E',  'ể' => 'e', 'Ể' => 'E',
				'ễ' => 'e', 'Ễ' => 'E', 'ệ' => 'e', 'Ệ' => 'E', 'ỉ' => 'i', 'Ỉ' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I',
				'ị' => 'i', 'Ị' => 'I', 'ỏ' => 'o', 'Ỏ' => 'O', 'ọ' => 'o', 'Ọ' => 'O', 'ố' => 'o', 'Ố' => 'O',
				'ồ' => 'o', 'Ồ' => 'O', 'ổ' => 'o', 'Ổ' => 'O', 'ỗ' => 'o', 'Ỗ' => 'O', 'ộ' => 'o', 'Ộ' => 'O',
				'ớ' => 'o', 'Ớ' => 'O', 'ờ' => 'o', 'Ờ' => 'O', 'ở' => 'o', 'Ở' => 'O', 'ỡ' => 'o', 'Ỡ' => 'O',
				'ợ' => 'o', 'Ợ' => 'O', 'ủ' => 'u', 'Ủ' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ụ' => 'u', 'Ụ' => 'U',
				'ư' => 'u', 'Ư' => 'U', 'ứ' => 'u', 'Ứ' => 'U', 'ừ' => 'u', 'Ừ' => 'U', 'ử' => 'u', 'Ử' => 'U',
				'ữ' => 'u', 'Ữ' => 'U', 'ự' => 'u', 'Ự' => 'U', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ỷ' => 'y', 'Ỷ' => 'Y',
				'ỹ' => 'y', 'Ỹ' => 'Y', 'ỵ' => 'y', 'Ỵ' => 'Y'
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
	public function incrementCount($tagString, $tagSlug, $count)
	{
		if($count <= 0) { return; }
		$model = $this->tagModelString();
		
		$tag = $model::where('slug', '=', $tagSlug)->first();

		if(!$tag) {
			$tag = new $model;
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
	public function decrementCount($tagString, $tagSlug, $count)
	{
		if($count <= 0) { return; }
		$model = $this->tagModelString();
		
		$tag = $model::where('slug', '=', $tagSlug)->first();
	
		if($tag) {
			$tag->count = $tag->count - $count;
			if($tag->count < 0) {
				$tag->count = 0;
				\Log::warning("The '.$model.' count for `$tag->name` was a negative number. This probably means your data got corrupted. Please assess your code and report an issue if you find one.");
			}
			$tag->save();
		}
	}
	
	/**
	 * Look at the tags table and delete any tags that are no londer in use by any taggable database rows.
	 * Does not delete tags where 'suggest' is true
	 *
	 * @return int
	 */
	public function deleteUnusedTags()
	{
		$model = $this->tagModelString();
		return $model::deleteUnused();
	}

	/**
	 * @return string
	 */
	public function tagModelString()
	{
		return config('tagging.tag_model', '\Conner\Tagging\Model\Tag');
	}
}
