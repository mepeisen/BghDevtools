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
 * Entity definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class DomainEntity extends \F3\BghDevtools\Domain\NamedElement
{
	
    /**
     * @var array(string=>\F3\BghDevtools\Domain\Model\DomainEntityKey)
     */
    protected $keys = array();
    
    /**
     * Returns the declared keys
     * @return array(string=>\F3\BghDevtools\Domain\Model\DomainEntityKey)
     */
    protected function getKeys()
    {
        return $this->keys;
    }
    
	/**
	 * @var boolean
	 */
	protected $generate = false;

	/**
	 * @var string
	 */
	protected $type = false;
	
	/**
	 * @var string
	 */
	const TYPE_TX = 'tx';
	
	/**
	 * @var string
	 */
	const TYPE_SIMPLE = 'simple';
	
	/**
	 * @var string
	 */
	const TYPE_STANDARD = 'standard';
	
	/**
	 * Returns the type
	 * 
	 * @return string
	 */
	public function getType()
	{
	    return $this->type;
	}
	
	/**
	 * Returns true if this is generated
	 * 
	 * @return boolean
	 */
	public function isGenerated()
	{
		return $this->generate;
	}
	
	/**
	 * @var array(string=>\F3\BghDevtools\Domain\Model\DomainEntityAttribute)
	 */
	protected $attributes = array();
	
	/**
	 * Returns the attributes
	 * 
	 * @return array(string=>\F3\BghDevtools\Domain\Model\DomainEntityAttribute)
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Entity-Definition';
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
		
		if ($key == 'generate')
		{
			$this->generate = (boolean)$val;
			return true;
		}
		
		if ($key == 'type')
		{
		    $this->type = $val;
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
	
		if ($element->getName() == 'attribute')
		{
			try
			{
				$attribute = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainEntityAttribute');
				$attribute->parse($element);
				if (isset($this->attributes[$attribute->getName()]))
				{
					throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate entity attribute for entity '".$this->getName()."': '".$attribute->getName()."'", 1286874500);
				}
				$this->attributes[$attribute->getName()] = $attribute;
				return true;
			}
			catch (\F3\BghDevtools\Domain\Model\Exception $e)
			{
				throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity '".$this->getName()."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286874501);
			}
		}
		
		if ($element->getName() == 'key')
		{
			try
			{
				$key = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainEntityKey');
				$key->parse($element);
				if (isset($this->keys[$key->getName()]))
				{
					throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate entity key for entity '".$this->getName()."': '".$key->getName()."'", 1286874500);
				}
				$this->keys[$key->getName()] = $key;
				return true;
			}
			catch (\F3\BghDevtools\Domain\Model\Exception $e)
			{
				throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity '".$this->getName()."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286874501);
			}
		}

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
		    throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity '".$this->getName()."'. Missing type", 1286872284);
		}
		
		if ($this->type !== self::TYPE_SIMPLE && $this->type !== self::TYPE_STANDARD && $this->type !== self::TYPE_TX)
		{
		    throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity '".$this->getName()."'. Unsupported type ".$this->type, 1286872284);
		}
	}
	
}
