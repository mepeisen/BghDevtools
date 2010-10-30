<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\Domain\Service;

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
 * Service method try catch definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class DomainServiceMethodTryCatch extends \F3\BghDevtools\Domain\DocumentedElement
{

	/**
	 * @var string
	 */
	protected $type = false;
	
	/**
	 * @var string
	 */
	protected $from = false;
	
	/**
	 * @var string
	 */
	protected $to = false;
	
	/**
	 * @var string
	 */
	protected $class = false;
	
	/**
	 * @var string
	 */
	const TYPE_USER = 'user';
	
	/**
	 * @var string
	 */
	const TYPE_MAP = 'map';
	
	/**
	 * @return boolean
	 */
	public function isUserCode()
	{
		return $this->type == self::TYPE_USER;
	}
	
	/**
	 * @return boolean
	 */
	public function isMap()
	{
		return $this->type == self::TYPE_MAP;
	}
	
	/**
	 * @return string
	 */
	public function getFrom()
	{
		return $this->from;
	}
	
	/**
	 * @return string
	 */
	public function getTo()
	{
		return $this->to;
	}
	
	/**
	 * @return string
	 */
	public function getClass()
	{
		return $this->class;
	}
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Service-Method-TryCatch-Definition';
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
		
		if ($key == 'class')
		{
			$this->class = $val;
			return true;
		}
		
		if ($key == 'from')
		{
			$this->from = $val;
			return true;
		}
		
		if ($key == 'to')
		{
			$this->to = $val;
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
			throw new \F3\BghDevtools\Domain\Service\Exception("Error reading service try catch. Missing type", 1286873572);
		}
	
		if ($this->type != self::TYPE_MAP && $this->type != self::TYPE_USER)
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("Error reading service try catch. Wrong type: ".$this->type, 1286873572);
		}
		
		if ($this->type == self::TYPE_USER && $this->class === false)
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("Error reading service try catch. Missing class", 1286873572);
		}
		
		if ($this->type == self::TYPE_MAP && $this->from === false)
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("Error reading service try catch. Missing from", 1286873572);
		}
			
		if ($this->type == self::TYPE_MAP && $this->to === false)
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("Error reading service try catch. Missing to", 1286873572);
		}
		
		if ($this->type == self::TYPE_USER && $this->from !== false)
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("Error reading service try catch. Invalid from", 1286873572);
		}
		
		if ($this->type == self::TYPE_USER && $this->to !== false)
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("Error reading service try catch. Invalid to", 1286873572);
		}
			
		if ($this->type == self::TYPE_MAP && $this->class !== false)
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("Error reading service try catch. Invalid class", 1286873572);
		}
	}
	
	/**
	 * Returns the object type
	 * 
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
	
}
