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
 * Entity key definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class DomainEntityKey extends \F3\BghDevtools\Domain\NamedElement
{
	
	/**
	 * @var string
	 */
	protected $type = false;
	
	/**
	 * @var string
	 */
	const TYPE_UNIQUE = 'unique';
	
	/**
	 * @var string
	 */
	const TYPE_USER = 'user';
	
	/**
	 * @var string
	 */
	const TYPE_NONUNIQUE = 'nonunique';
	
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
	 * @var array(string=>\F3\BghDevtools\Domain\Model\DomainEntityKeyColumn)
	 */
	protected $columns = array();
	
	/**
	 * Returns the columns
	 * 
	 * @return array(string=>\F3\BghDevtools\Domain\Model\DomainEntityKeyColumn)
	 */
	public function getColumns()
	{
		return $this->columns;
	}
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Entity-Key-Definition';
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
		
		if ($element->getName() == 'column')
		{
			try
			{
				$col = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainEntityKeyColumn');
				$col->parse($element);
				if (isset($this->columns[$col->getName()]))
				{
					throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate entity key column for key '".$this->getName()."': '".$col->getName()."'", 1286874500);
				}
				$this->columns[$col->getName()] = $col;
				return true;
			}
			catch (\F3\BghDevtools\Domain\Model\Exception $e)
			{
				throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity key '".$this->getName()."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286874501);
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
		    throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity key '".$this->getName()."'. Missing type", 1286872284);
		}
		
		if ($this->type !== self::TYPE_NONUNIQUE && $this->type !== self::TYPE_UNIQUE && $this->type !== self::TYPE_USER)
		{
		    throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity key '".$this->getName()."'. Unsupported type ".$this->type, 1286872284);
		}
	}
	
}
