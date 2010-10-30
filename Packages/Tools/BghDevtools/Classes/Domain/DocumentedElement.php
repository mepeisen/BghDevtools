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
 * Documented element definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class DocumentedElement
{

	/**
	 * @var \F3\FLOW3\Object\ObjectManagerInterface
	 */
	protected $objectManager;
	
	/**
	 * @var string
	 */
	protected $documentation = '';
	
	/**
	 * Constructor
	 * 
	 * @param \F3\FLOW3\Object\ObjectManagerInterface $objectManager
	 */
	public function __construct(\F3\FLOW3\Object\ObjectManagerInterface $objectManager)
	{
	    $this->objectManager = $objectManager;
	}
	
	/**
	 * parse it
	 * 
	 * @param \SimpleXmlElement $node
	 * 
	 * @throws \F3\BghDevtools\Domain\Exception
	 */
	public function parse(\SimpleXmlElement $node)
	{
		$attrs = $node->attributes();
		try
		{
			foreach ($attrs as $key => $val)
			{
				if (!$this->applyAttribute($key, (string) $val))
				{
					throw new \F3\BghDevtools\Domain\Exception("Invalid argument reading ".$this->getElementName()." element: '$key'", 1286871535);
				}
			}
			foreach ($node->children() as $child)
			{
				/* @var $child \SimpleXmlElement */
				if (!$this->applyChild($child))
				{
					throw new \F3\BghDevtools\Domain\Exception("Invalid child reading ".$this->getElementName()." element: '".$child->getName()."'", 1286871536);
				}
			}
		}
		catch (\F3\BghDevtools\Domain\Model\Exception $e)
		{
			throw new \F3\BghDevtools\Domain\Exception("Error reading element '".$this->getElementName()."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286871537);
		}
		
		$this->validate();
	}
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected abstract function getElementName();
	
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
		if ($element->getName() == 'description')
		{
			$this->documentation = (string) $element;
			return true;
		}
		return false;
	}
	
	/**
	 * Validates the content
	 */
	protected function validate()
	{
		// empty
	}
	
	/**
	 * Returns the documentation
	 * 
	 * @return string
	 */
	public function getDocumentation()
	{
		return $this->documentation;
	}
	
}
