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
 * Aspect constraint definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class DomainAspectConstraint extends \F3\BghDevtools\Domain\NamedElement
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
	 * @var string
	 */
	protected $entity = false;
	
	/**
	 * Returns the entity
	 * @return string
	 */
	public function getEntity()
	{
		return $this->entity;
	}
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Aspect-Constraint-Definition';
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
		
		if ($key == 'entity')
		{
			$this->entity = $val;
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
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading aspect constraint '".$this->getName()."'. Missing type", 1286875531);
		}
	
		if ($this->entity === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading aspect constraint '".$this->getName()."'. Missing entity", 1286875532);
		}
	}
	
}
