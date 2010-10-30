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
 * Repository definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class DomainRepository extends \F3\BghDevtools\Domain\NamedElement
{
	
	/**
	 * @var boolean
	 */
	protected $generate = false;
	
	/**
	 * @var string
	 */
	protected $objectType = false;
	
	/**
	 * @var array(string=>\F3\BghDevtools\Domain\Model\DomainRepositoryMethod)
	 */
	protected $methods = array();
	
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
	 * Returns the object type
	 * 
	 * @return string
	 */
	public function getObjectType()
	{
		return $this->objectType;
	}
	
	/**
	 * Returns the methods
	 * 
	 * @return array(string=>\F3\BghDevtools\Domain\Model\DomainRepositoryMethod)
	 */
	public function getMethods()
	{
		return $this->methods;
	}
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Repository-Definition';
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
		
		if ($key == 'objectType')
		{
			$this->objectType = $val;
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
		
		if ($element->getName() == 'method')
		{
			try
			{
				$method = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainRepositoryMethod');
				$method->parse($element);
				if (isset($this->methods[$method->getName()]))
				{
					throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate repository method for repository '".$this->getName()."': '".$method->getName()."'", 1286872285);
				}
				$this->methods[$method->getName()] = $method;
				return true;
			}
			catch (\F3\BghDevtools\Domain\Model\Exception $e)
			{
				throw new \F3\BghDevtools\Domain\Model\Exception("Error reading repository '".$this->getName()."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286872288);
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
		
		if ($this->objectType === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading repository '".$this->getName()."'. Missing object type", 1286872284);
		}
	}
	
}
