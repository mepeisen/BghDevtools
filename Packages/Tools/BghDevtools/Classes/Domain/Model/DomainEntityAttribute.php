<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "BghDevtools"                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Entity attribute definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class DomainEntityAttribute extends \F3\BghDevtools\Domain\NamedElement
{
	
	/**
	 * @var string
	 */
	protected $type = false;
	
	/**
	 * Returns the argument type
	 * 
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * @var boolean
	 */
	protected $translate = false;
	
	/**
	 * Returns true if this argument is translated
	 * @return boolean
	 */
	public function isTranslate()
	{
		return $this->translate;
	}
	
	/**
	 * Returns true if this is an id type
	 * @return boolean
	 */
	public function isUuid()
	{
		return strtolower($this->type) == 'id';
	}
	
	/**
	 * Returns true if this is an id type
	 * @return boolean
	 */
	public function isNotUuid()
	{
		return strtolower($this->type) != 'id';
	}
	
	/**
	 * True if this is a write access attribute
	 * 
	 * @return boolean
	 */
	public function isWriteAccess()
	{
		return $this->access == self::ACCESS_WRITE;
	}
	
	/**
	 * @var boolean
	 */
	protected $validate = false;
	
	/**
	 * Returns true if this will be validated
	 * @return boolean
	 */
	public function isValidated()
	{
		return $this->validate;
	}
	
	/**
	 * @var boolean
	 */
	protected $postProcessing = false;
	
	/**
	 * Returns true if this will be post processed
	 * @return boolean
	 */
	public function isPostProcessing()
	{
		return $this->postProcessing;
	}
	
	/**
	 * @var string
	 */
	protected $access = false;
	
	/**
	 * Returns the access type; see the ACCESS constants in this class
	 * 
	 * @return string
	 */
	public function getAccess()
	{
		return $this->access;
	}
	
	/**
	 * readonly
	 * @var string
	 */
	const ACCESS_READONLY = 'readonly';
	
	/**
	 * read/write
	 * @var string
	 */
	const ACCESS_WRITE = 'write';
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Entity-Attribute-Definition';
	}
	
	/**
	 * Applies attributes
	 * 
	 * @param string $key
	 * @param string $val
	 * 
	 * @return boolean true if the attribute is ok; false if the attribute is invalid
	 */
	protected function applyAttribute($key, $val)
	{
		if (parent::applyAttribute($key, $val)) return true;
		
		if ($key == 'type')
		{
			$this->type = $val;
			return true;
		}
		
		if ($key == 'access')
		{
			$this->access = $val;
			return true;
		}
				
		if ($key == 'postProcessing')
		{
			$this->postProcessing = (boolean) $val;
			return true;
		}
				
		if ($key == 'translate')
		{
			$this->translate = (boolean) $val;
			return true;
		}
				
		if ($key == 'validate')
		{
			$this->validate = (boolean) $val;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Applies children
	 * 
	 * @param \SimpleXmlElement $element
	 * 
	 * @return boolean true if the child is ok; false if the child is invalid
	 */
	protected function applyChild(\SimpleXmlElement $element)
	{
		if (parent::applyChild($element)) return true;
		
		return false;
	}
	
	/**
	 * Validates the content
	 */
	protected function validate()
	{
		parent::validate();
		
		if ($this->type === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity attribute '".$this->getName()."'. Missing type", 1286875531);
		}
	
		if ($this->access === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity attribute '".$this->getName()."'. Missing access type", 1286875532);
		}
		
		if ($this->access != self::ACCESS_READONLY && $this->access != self::ACCESS_WRITE)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity attribute '".$this->getName()."'. Invalid access type '".$this->access."'", 1286875533);
		}
	
		if ($this->access == self::ACCESS_READONLY && ($this->validate || $this->postProcessing || $this->translate))
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity attribute '".$this->getName()."'. validate/translate/postProcessing cannot be used with read access", 1286875534);
		}
	}
	
}
