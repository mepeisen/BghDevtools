<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\Domain;

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
 * Named element definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class NamedElement extends \F3\BghDevtools\Domain\DocumentedElement
{

	/**
	 * @var string
	 */
	protected $name = false;
	
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
		if ($key == 'name')
		{
			$this->name = $val;
			return true;
		}
		return parent::applyAttribute($key, $val);
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
		return parent::applyChild($element);
	}
	
	/**
	 * Validates the content
	 */
	protected function validate()
	{
		if ($this->name === false)
		{
			throw new \F3\BghDevtools\Domain\Exception("Error reading element '".$this->getElementName()."'. Missing name", 1286871538);
		}
	}
	
	/**
	 * Returns the name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
}
