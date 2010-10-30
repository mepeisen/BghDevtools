<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\Service;

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
 * class reader service to read class files and report any user code section
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ClassReaderService
{

	/**
	 * User code sections
	 * @var array(string=>string)
	 */
	protected $userCodeSections = array();
	
	/**
	 * @var string
	 */
	const USER_CODE_BEGIN = '/*{USER_CODE_BEGIN:';
		
	/**
	 * @var string
	 */
	const USER_CODE_END = '/*{USER_CODE_END:';
	
	/**
	 * @var string
	 */
	const USER_CODE_SUFFIX = '}*/';
	
	/**
	 * Reads a class file and detects user code sections
	 * 
	 * @param string $file
	 */
	public function readFile($file)
	{
		$this->userCodeSections = array();
		if (is_readable($file))
		{
			$contents = \F3\FLOW3\Utility\Files::getFileContents($file);
			$cur = 0;
			$len = strlen($contents);
			while (($pos1 = strpos($contents, self::USER_CODE_BEGIN, $cur)) !== false)
			{
				$pos1 += strlen(self::USER_CODE_BEGIN);
				$pos2 = strpos($contents, self::USER_CODE_SUFFIX, $pos1);
				if ($pos2 === false) return; // TODO Should we report an exception ?
				$key1 = substr($contents, $pos1, $pos2 - $pos1);
				
				$codeBegin = $pos2 + strlen(self::USER_CODE_SUFFIX);
				
				$pos1 = strpos($contents, self::USER_CODE_END, $codeBegin);
				$codeEnd = $pos1;
				if ($pos1 === false) return; // TODO Should we report an exception ?
				$pos1 += strlen(self::USER_CODE_END);
				$pos2 = strpos($contents, self::USER_CODE_SUFFIX, $pos1);
				if ($pos2 === false) return; // TODO Should we report an exception ?
				$key2 = substr($contents, $pos1, $pos2 - $pos1);
				
				if ($key1 != $key2) return; // TODO Should we report an exception ?
				
				$this->userCodeSections[$key1] = substr($contents, $codeBegin, $codeEnd - $codeBegin);
				$cur = $pos2;
			}
		}
	}
	
	/**
	 * Returns true if a user code section was found
	 * 
	 * @param string $key
	 * 
	 * @return boolean
	 */
	public function hasSection($key)
	{
		return isset($this->userCodeSections[$key]);
	}
	
	/**
	 * Returns the contents of a user code section
	 * 
	 * @param string $key
	 */
	public function getSection($key)
	{
		return isset($this->userCodeSections[$key]) ? $this->userCodeSections[$key] : '';
	}
	
	/**
	 * Returns a string to start a section
	 * 
	 * @param string $key
	 * 
	 * @return string
	 */
	public function startSection($key)
	{
		return self::USER_CODE_BEGIN.$key.self::USER_CODE_SUFFIX;
	}
	
	/**
	 * Returns a string to end a section
	 * 
	 * @param string $key
	 * 
	 * @return string
	 */
	public function endSection($key)
	{
		return self::USER_CODE_END.$key.self::USER_CODE_SUFFIX;
	}
	
}
