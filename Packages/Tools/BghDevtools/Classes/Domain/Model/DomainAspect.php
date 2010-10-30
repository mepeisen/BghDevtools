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
 * Aspect definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class DomainAspect extends \F3\BghDevtools\Domain\NamedElement
{
	
	/**
	 * @var boolean
	 */
	protected $generate = false;
		
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
	 * @var array(string=>\F3\BghDevtools\Domain\Model\DomainAspectConstraint)
	 */
	protected $constraints = array();
	
	/**
	 * Returns the attributes
	 * 
	 * @return array(string=>\F3\BghDevtools\Domain\Model\DomainAspectConstraint)
	 */
	public function getConstraints()
	{
		return $this->constraints;
	}
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Aspect-Definition';
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
		
		if ($element->getName() == 'constraint')
		{
			try
			{
				$constraint = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainAspectConstraint');
				$constraint->parse($element);
				if (isset($this->constraints[$constraint->getName()]))
				{
					throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate aspect constraint for entity '".$this->getName()."': '".$constraint->getName()."'", 1286874500);
				}
				$this->constraints[$constraint->getName()] = $constraint;
				return true;
			}
			catch (\F3\BghDevtools\Domain\Model\Exception $e)
			{
				throw new \F3\BghDevtools\Domain\Model\Exception("Error reading aspect '".$this->getName()."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286874501);
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
	}
	
}
