<?php

namespace Bitrix\Main\PhoneNumber;

class Formatter
{
	public static function format(PhoneNumber $number, $formatType, $forceNationalPrefix = false)
	{
		if(!$number->isValid())
			return $number->getRawNumber();

		if($formatType === Format::E164)
			return '+' . $number->getCountryCode() . $number->getNationalNumber();

		$countryMetadata = MetadataProvider::getInstance()->getCountryMetadata($number->getCountry());
		$format = static::selectFormatForNumber($number->getNationalNumber(), $formatType, $countryMetadata);

		if($format)
		{
			$formattedNationalNumber = static::formatNationalNumber(
				$number->getNationalNumber(),
				$formatType,
				$countryMetadata,
				$format,
				$forceNationalPrefix
			);
		}
		else
		{
			$formattedNationalNumber = $number->getNationalNumber();
		}

		if($formatType == Format::INTERNATIONAL)
		{
			return '+' . $number->getCountryCode() . ' ' . $formattedNationalNumber;
		}
		else if($formatType == Format::NATIONAL)
		{
			return $formattedNationalNumber;
		}

		return $number->getRawNumber();
	}

	protected static function selectFormatForNumber($nationalNumber, $formatType, $countryMetadata)
	{
		$isInternational = ($formatType === Format::INTERNATIONAL);
		$availableFormats = is_array($countryMetadata['availableFormats']) ? $countryMetadata['availableFormats'] : array();

		foreach ($availableFormats as $format)
		{
			if($isInternational && isset($format['intlFormat']) && $format['intlFormat'] === 'NA')
				continue;

			if(isset($format['leadingDigits']) && !static::matchLeadingDigits($nationalNumber, $format['leadingDigits']))
			{
				continue;
			}

			$formatPatternRegex = '/^' . $format['pattern'] . '$/';
			if(preg_match($formatPatternRegex, $nationalNumber))
			{
				return $format;
			}
		}
		return false;
	}

	/**
	 * Checks that number starts with specified leading digits regex. Return array of matches if matched or false otherwise
	 * @param string $phoneNumber Phone number.
	 * @param string|array $leadingDigits Leading digits to check (one pattern or array of patterns).
	 * @return array|false
	 */
	protected static function matchLeadingDigits($phoneNumber, $leadingDigits)
	{
		if(is_array($leadingDigits))
		{
			foreach ($leadingDigits as $leadingDigitsSample)
			{
				$re = '/^' . $leadingDigitsSample . '/';
				if(preg_match($re, $phoneNumber, $matches))
				{
					return $matches;
				}
			}
		}
		else
		{
			$re = '/^' . $leadingDigits . '/';
			if(preg_match($re, $phoneNumber, $matches))
			{
				return $matches;
			}
		}
		return false;
	}

	/**
	 * @param string $nationalNumber
	 * @param string $formatType
	 * @param array $countryMetadata
	 * @param mixed $format
	 * @param bool $forceNationalPrefix
	 * @return mixed
	 */
	protected static function formatNationalNumber($nationalNumber, $formatType, $countryMetadata, $format, $forceNationalPrefix)
	{
		$isInternational = ($formatType === Format::INTERNATIONAL);
		$replaceFormat = (isset($format['intlFormat']) && $isInternational) ? $format['intlFormat'] : $format['format'];
		$patternRegex = '/' . $format['pattern'] . '/';

		if(!$isInternational)
		{
			$nationalPrefixFormattingRule = static::getNationalPrefixFormattingRule($countryMetadata, $format);
			if($nationalPrefixFormattingRule != '')
			{
				$replaceFormat = preg_replace('/(\\$\\d)/', $nationalPrefixFormattingRule, $replaceFormat, 1);
			}
		}

		return preg_replace($patternRegex, $replaceFormat, $nationalNumber);
	}

	/**
	 * National prefix can be skipped if there's no national prefix formatting rule set for this country, or when this
	 * rule is set but national prefix is optional
	 * @param array $countryMetadata
	 * @param mixed $format
	 */
	protected static function canSkipPrefix($countryMetadata, $format)
	{
		$nationalPrefixFormattingRule = static::getNationalPrefixFormattingRule($countryMetadata, $format);
		$nationalPrefixOptional = static::getNationalPrefixOptional($countryMetadata, $format);

		return ($nationalPrefixOptional || $nationalPrefixFormattingRule == '');
	}

	protected static function getNationalPrefixFormattingRule($countryMetadata, $format)
	{
		if(is_array($format) && isset($format['nationalPrefixFormattingRule']))
			$result = $format['nationalPrefixFormattingRule'];
		else if(isset($countryMetadata['nationalPrefixFormattingRule']))
			$result = $countryMetadata['nationalPrefixFormattingRule'];
		else
			$result = '';

		return str_replace(array('$NP', '$FG'), array($countryMetadata['nationalPrefix'], '$1'), $result);
	}

	protected static function getNationalPrefixOptional($countryMetadata, $format)
	{
		if(is_array($format) && isset($format['nationalPrefixOptionalWhenFormatting']))
			return $format['nationalPrefixOptionalWhenFormatting'];
		else if(isset($countryMetadata['nationalPrefixOptionalWhenFormatting']))
			return $countryMetadata['nationalPrefixOptionalWhenFormatting'];
		else
			return false;
	}

}