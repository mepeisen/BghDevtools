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
 * Repository method argument definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class DomainRepositoryMethodArg extends \F3\BghDevtools\Domain\NamedElement
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
	public function isTranslated()
	{
		return $this->translate;
	}
	
	/**
	 * @var boolean
	 */
	protected $shadowed = false;
	
	/**
	 * Returns true if this is a shadowed argument
	 * @return boolean
	 */
	public function isShadowed()
	{
		return $this->shadowed;
	}
	
	/**
	 * Returns true if this is a visible argument
	 * @return boolean
	 */
	public function isVisible()
	{
		return !$this->shadowed;
	}
	
	/**
	 * @var string
	 */
	protected $value = false;
	
	/**
	 * Returns the shadowed value
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Repository-Method-Argument-Definition';
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
		
		if ($key == 'translate')
		{
			$this->translate = (boolean) $val;
			return true;
		}
				
		if ($key == 'shadowed')
		{
			$this->shadowed = (boolean) $val;
			return true;
		}
				
		if ($key == 'value')
		{
			$this->value = $val;
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
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading repository method argument '".$this->getName()."'. Missing type", 1286874209);
		}
	
		if ($this->translate && $this->shadowed)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading repository method argument '".$this->getName()."'. Translate and shadowed cannot be used together", 1286874210);
		}
	
		if ($this->shadowed && $this->value === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading repository method argument '".$this->getName()."'. Missing value for shadowed argument", 1286874211);
		}
		
		if (!$this->shadowed && $this->value !== false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading repository method argument '".$this->getName()."'. Value on regular argument", 1286874212);
		}
	}
	
}
