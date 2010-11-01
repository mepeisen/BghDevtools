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
 * Dependency source scm definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class DependencySourceScm extends \F3\BghDevtools\Domain\DocumentedElement implements \F3\BghDevtools\Domain\Project\DependencySourceInterface
{
    
    /**
     * Returns the source type
     * 
     * @return string
     */
	public function getSourceType()
	{
	    return self::SOURCETYPE_SCM;
	}
	
	/**
	 * GIT
	 */
	const TYPE_GIT = 'git';
	
	/**
	 * @var string
	 */
	protected $type = false;
	
	/**
	 * @var string
	 */
	protected $url = false;
	
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
	 * Returns the url
	 * 
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Dependency-Source-Scm-Definition';
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
		
		if ($key == 'url')
		{
			$this->url = $val;
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
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading dependency source scm. Missing type", 1286872284);
		}
		
		if ($this->type != self::TYPE_GIT)
		{
		    throw new \F3\BghDevtools\Domain\Model\Exception("Error reading dependency source scm. Wrong type ".$this->type, 1286872285);
		}
		
		if ($this->url === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading dependency source scm. Missing url", 1286872284);
		}
	}
	
}
