<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\ViewHelpers;

/*                                                                        *
 * This script belongs to the FLOW3 package "BghDevtools".              *
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
 *                                                                        */

/**
 * view helper to normalize the type for phpdoc
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class NormalizeTypeViewHelper extends \F3\Fluid\Core\ViewHelper\AbstractViewHelper
{

	/**
	 * @param string $type
	 */
	public function render($type)
	{
		$packageKey = $this->templateVariableContainer['packageKey'];
		$subpackage = $this->templateVariableContainer['subpackage'];
		$namespace = $this->templateVariableContainer['namespace'];
		$packageKey = $this->templateVariableContainer['packageKey'];
		
		switch (strtolower($type))
		{
			case 'string':
			case 'id':
				return 'string';
			case 'bool':
			case 'boolean':
				return 'boolean';
			case 'int':
			case 'integer':
				return 'integer';
			case 'double':
			case 'float':
				return 'float';
			case 'array':
				return 'array';
			case 'mixed':
			case 'undefined':
				return 'mixed';
		}
		return self::normalizeEntityClassName($packageKey, $subpackage, $namespace, $type, '');
	}
	
	/**
	 * Normalizes the class name
	 * 
	 * @param string $packageKey
	 * @param string $subpackage
	 * @param string $namespace
	 * @param string $source
	 * @param string $default
	 * 
	 * @return string
	 */
	public static function normalizeEntityClassName($packageKey, $subpackage, $namespace, $source, $default = false)
	{
		if ($source === false) return $default;
		if (substr($source, 0, 1) != '$') return $source;
		$res = "\\F3\\$packageKey";
		if ($subpackage != '') $res .= "\\$subpackage";
		$res .= "\\Domain";
		if ($namespace != '') $res .= "\\$namespace";
		$res .= "\\Model\\Api\\".substr($source, 1)."Interface";
		return $res;
	}
	
	/**
	 * Normalizes the class name
	 * 
	 * @param string $packageKey
	 * @param string $subpackage
	 * @param string $namespace
	 * @param string $source
	 * @param string $thisPackage
	 * @param string $default
	 * 
	 * @return string
	 */
	public static function normalizeClassName($packageKey, $subpackage, $namespace, $source, $thisPackage, $default = false)
	{
		if ($source === false) return $default;
		
		if (substr($source, 0, 2) == '$$')
		{
			// external package declared
			$pos = strpos($source, ':');
			$type = substr($source, 2, $pos - 2);
			$explode = explode(';', substr($source, $pos + 1));
			$params = array();
			foreach ($explode as $e)
			{
				list ($key, $val) = explode('=', $e);
				$params[strtolower($key)] = $val;
			}
			switch (strtolower($type))
			{
				case 'entity':
					if (!isset($params['name'])) return '??MissingEntityName?';
					if (isset($params['package'])) $packageKey = $params['package'];
					if (isset($params['subpackage'])) $subpackage = $params['subpackage'];
					if (isset($params['namespace'])) $subpackage = $params['namespace'];
					return self::normalizeEntityClassName($packageKey, $subpackage, $namespace, '$'.$params['name']);
				case 'model':
					if (!isset($params['name'])) return '??MissingModelName?';
					if (isset($params['package'])) $packageKey = $params['package'];
					if (isset($params['subpackage'])) $subpackage = $params['subpackage'];
					if (isset($params['namespace'])) $subpackage = $params['namespace'];
					if (isset($params['thisPackage'])) $thisPackage = $params['thisPackage']; else $thisPackage = 'Domain';
					return self::normalizeClassName($packageKey, $subpackage, $namespace, '$'.$params['name'], $thisPackage);
			}
			return '??UnsupportedType?';
		}
		
		// a regular class name ?
		if (substr($source, 0, 1) != '$') return $source;
		
		// internal class declared
		$res = "\\F3\\$packageKey";
		if ($subpackage != '') $res .= "\\$subpackage";
		$res .= "\\$thisPackage";
		if ($namespace != '') $res .= "\\$namespace";
		$res .= "\\".substr($source, 1);
		return $res;
	}
	
}
