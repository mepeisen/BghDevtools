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
 * Entity key column filter definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class DomainEntityKeyColumnFilter extends \F3\BghDevtools\Domain\NamedElement
{

    /**
     * The type
     * @var string
     */
    protected $type = false;
    
    /**
     * @var string
     */
    const TYPE_BYVALUE = 'byvalue';
    
    /**
     * @var string
     */
    const TYPE_USER = 'user';
    
    /**
     * @var string
     */
    protected $value = false;
    
    /**
     * Returns the type
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Returns the value
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Entity-Key-Column-Filter-Definition';
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
	
		if ($key == 'value')
		{
		    $this->value = $val;
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
		    throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity key filter. Missing type", 1286872284);
		}
		
		if ($this->type !== self::TYPE_BYVALUE && $this->type !== self::TYPE_USER)
		{
		    throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity key filter. Unsupported type ".$this->type, 1286872284);
		}
		
		if ($this->value === false && $this->type === self::TYPE_BYVALUE)
		{
		    throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity key filter. Value missing for byvalue filter", 1286872284);
		}
		
		if ($this->value !== false && $this->type === self::TYPE_USER)
		{
		    throw new \F3\BghDevtools\Domain\Model\Exception("Error reading entity key filter. Value cannot be used with user filter", 1286872284);
		}
	}
	
}
