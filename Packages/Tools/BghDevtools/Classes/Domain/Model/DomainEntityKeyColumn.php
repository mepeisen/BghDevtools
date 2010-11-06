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
 * Entity key column definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class DomainEntityKeyColumn extends \F3\BghDevtools\Domain\NamedElement
{
	
	/**
	 * @var array(string=>\F3\BghDevtools\Domain\Model\DomainEntityKeyColumnFilter)
	 */
	protected $filters = array();
	
	/**
	 * Returns the filter
	 * 
	 * @return array(string=>\F3\BghDevtools\Domain\Model\DomainEntityKeyColumnFilter)
	 */
	public function getFilters()
	{
		return $this->$filters;
	}
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Entity-Key-Column-Definition';
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
	
		if ($element->getName() == 'filter')
		{
			try
			{
				$filter = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainEntityKeyColumnFilter');
				$filter->parse($element);
				if (isset($this->filters[$filter->getName()]))
				{
					throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate entity key column filter for column '".$this->getName()."': '".$filter->getName()."'", 1286874500);
				}
				$this->filters[$filter->getName()] = $filter;
				return true;
			}
			catch (\F3\BghDevtools\Domain\Model\Exception $e)
			{
				throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity column '".$this->getName()."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286874501);
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
