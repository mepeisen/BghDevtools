<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\Domain\Project;

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
 * Package module definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class PackageModule extends \F3\BghDevtools\Domain\NamedElement
{
	
	/**
	 * @var string
	 */
	protected $title = false;
	
	/**
	 * @var string
	 */
	protected $description = false;
		
	/**
	 * @var string
	 */
	protected $version = false;
	
	/**
	 * Returns the title
	 * 
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * Returns the description
	 * 
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
		
	/**
	 * Returns the version
	 * 
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Package-Module-Definition';
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
		if ($element->getName() == 'description')
		{
			$this->description = (string) $element;
			return true;
		}
		
		if (parent::applyChild($element)) return true;
		
		if ($element->getName() == 'title')
		{
			$this->title = (string) $element;
			return true;
		}
				
		if ($element->getName() == 'version')
		{
			$this->version = (string) $element;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Validates the content
	 */
	protected function validate()
	{
		parent::validate();
		
		if ($this->title === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading package module '".$this->getName()."'. Missing title", 1286872284);
		}
		
		if ($this->description === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading package module '".$this->getName()."'. Missing description", 1286872284);
		}
		
		if ($this->version === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading package module '".$this->getName()."'. Missing version", 1286872284);
		}
	}
	
}
