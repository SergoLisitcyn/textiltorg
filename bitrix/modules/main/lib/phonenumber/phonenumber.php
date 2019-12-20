<?php

namespace Bitrix\Main\PhoneNumber;

class PhoneNumber
{
	protected $rawNumber;
	protected $country;

	protected $valid = false;
	protected $countryCode;
	protected $nationalNumber;
	protected $numberType;
	protected $extension;

	protected $international = false;
	protected $hadNationalPrefix = false;

	public function format($formatType = '', $forceNationalPrefix = false)
	{
		if(!$this->valid)
			return $this->rawNumber;

		if($formatType == '')
			$formatType = ($this->international ? Format::INTERNATIONAL : Format::NATIONAL);

		return Formatter::format($this, $formatType, ($forceNationalPrefix || $this->hadNationalPrefix()));
	}

	/**
	 * @return string
	 */
	public function getRawNumber()
	{
		return $this->rawNumber;
	}

	/**
	 * @param string $rawNumber
	 */
	public function setRawNumber($rawNumber)
	{
		$this->rawNumber = $rawNumber;
	}

	/**
	 * @return mixed
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * @param mixed $country
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}

	/**
	 * @return string
	 */
	public function getNationalNumber()
	{
		return $this->nationalNumber;
	}

	/**
	 * @param string $nationalNumber
	 */
	public function setNationalNumber($nationalNumber)
	{
		$this->nationalNumber = $nationalNumber;
	}

	/**
	 * @return mixed
	 */
	public function getNumberType()
	{
		return $this->numberType;
	}

	/**
	 * @param mixed $numberType
	 */
	public function setNumberType($numberType)
	{
		$this->numberType = $numberType;
	}

	/**
	 * @return bool
	 */
	public function isValid()
	{
		return $this->valid;
	}

	/**
	 * @param bool $valid
	 */
	public function setValid($valid)
	{
		$this->valid = $valid;
	}

	/**
	 * @param string $countryCode
	 */
	public function setCountryCode($countryCode)
	{
		$this->countryCode = $countryCode;
	}

	public function getCountryCode()
	{
		return $this->countryCode;
	}

	/**
	 * @return string
	 */
	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * @param string $extension
	 */
	public function setExtension($extension)
	{
		$this->extension = $extension;
	}

	/**
	 * @return bool
	 */
	public function isInternational()
	{
		return $this->international;
	}

	/**
	 * @param bool $international
	 */
	public function setInternational($international)
	{
		$this->international = $international;
	}

	/**
	 * @return bool
	 */
	public function hadNationalPrefix()
	{
		return $this->hadNationalPrefix;
	}

	/**
	 * @param bool $hadNationalPrefix
	 */
	public function setHadNationalPrefix($hadNationalPrefix)
	{
		$this->hadNationalPrefix = $hadNationalPrefix;
	}
}