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
 * Service for the model generator
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ModelGeneratorService
{

	/**
	 * @var \F3\FLOW3\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var \F3\Fluid\Core\Parser\TemplateParser
	 * @inject
	 */
	protected $templateParser;

	/**
	 * @var \F3\BghDevtools\Service\ClassReaderService
	 * @inject
	 */
	protected $classReaderService;
	
	/**
	 * @var array
	 */
	protected $generatedFiles = array();

	/**
	 * Generates an exception class file
	 * 
	 * @param string $packageKey
	 * @param string $subpackage
	 * @param string $namespace
	 * @param string $basePath
	 * @param \F3\BghDevtools\Domain\Model\DomainException $exception
	 */
	public function generateExceptionClass($packageKey, $subpackage, $namespace, $basePath, \F3\BghDevtools\Domain\Model\DomainException $exception)
	{
		$templatePathAndFilename = 'resource://BghDevtools/Private/Generator/Model/ExceptionTemplate.php.tmpl';
		
		$contextVariables = array();
		$contextVariables['packageKey'] = $packageKey;
		$contextVariables['subpackage'] = $subpackage;
		$contextVariables['isInSubpackage'] = ($subpackage != '');
		$contextVariables['namespace'] = $namespace;
		$contextVariables['isInNamespace'] = ($namespace != '');
		$contextVariables['exceptionClassName'] = $exception->getName();
		$contextVariables['exceptionName'] = $exception->getName();
		$contextVariables['exceptionDocumentation'] = $exception->getDocumentation();
		$contextVariables['exceptionParentClass'] = $this->normalizeClassName($packageKey, $subpackage, $namespace, $exception->getParent(), '\\F3\\FLOW3\\Exception');
		
		$targetFile = $basePath.'/Classes/Domain';
		if ($namespace != '') $targetFile .= '/'.str_replace('\\', '/', $namespace);
		$targetFile .= '/'.$exception->getName().'.php';
		
		$this->classReaderService->readFile($targetFile);
		
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables);
		
		$this->generateFile($targetFile, $fileContent);
	}

	/**
	 * Generates an entity class file
	 * 
	 * @param string $packageKey
	 * @param string $subpackage
	 * @param string $namespace
	 * @param string $basePath
	 * @param \F3\BghDevtools\Domain\Model\DomainEntity $entity
	 */
	public function generateEntityClass($packageKey, $subpackage, $namespace, $basePath, \F3\BghDevtools\Domain\Model\DomainEntity $entity)
	{
		$contextVariables = array();
		$contextVariables['packageKey'] = $packageKey;
		$contextVariables['subpackage'] = $subpackage;
		$contextVariables['isInSubpackage'] = ($subpackage != '');
		$contextVariables['namespace'] = $namespace;
		$contextVariables['isInNamespace'] = ($namespace != '');
		$contextVariables['entityClassName'] = $entity->getName();
		$contextVariables['entityName'] = $entity->getName();
		$contextVariables['entityDocumentation'] = $entity->getDocumentation();
		$contextVariables['entityAttributes'] = $entity->getAttributes();
		
		// API
		$templatePathAndFilename = 'resource://BghDevtools/Private/Generator/Model/EntityApiTemplate.php.tmpl';
		
		$targetFile = $basePath.'/Classes/Domain';
		if ($namespace != '') $targetFile .= '/'.str_replace('\\', '/', $namespace);
		$targetFile .= '/Model/'.$entity->getName().'Interface.php';
		
		$this->classReaderService->readFile($targetFile);
		
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables);
		
		$this->generateFile($targetFile, $fileContent);
		
		// IMPL
		$templatePathAndFilename = 'resource://BghDevtools/Private/Generator/Model/EntityImplTemplate.php.tmpl';
		
		$targetFile = $basePath.'/Classes/Domain';
		if ($namespace != '') $targetFile .= '/'.str_replace('\\', '/', $namespace);
		$targetFile .= '/Model/'.$entity->getName().'.php';
		
		$this->classReaderService->readFile($targetFile);
		
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables);
		
		$this->generateFile($targetFile, $fileContent);
	}

	/**
	 * Generates a repository class file
	 * 
	 * @param string $packageKey
	 * @param string $subpackage
	 * @param string $namespace
	 * @param string $basePath
	 * @param \F3\BghDevtools\Domain\Model\DomainRepository $repository
	 */
	public function generateRepositoryClass($packageKey, $subpackage, $namespace, $basePath, \F3\BghDevtools\Domain\Model\DomainRepository $repository)
	{
		$contextVariables = array();
		$contextVariables['packageKey'] = $packageKey;
		$contextVariables['subpackage'] = $subpackage;
		$contextVariables['isInSubpackage'] = ($subpackage != '');
		$contextVariables['namespace'] = $namespace;
		$contextVariables['isInNamespace'] = ($namespace != '');
		$contextVariables['repositoryClassName'] = $repository->getName();
		$contextVariables['repositoryName'] = $repository->getName();
		$contextVariables['repositoryDocumentation'] = $repository->getDocumentation();
		$contextVariables['repositoryMethods'] = $repository->getMethods();
		
		// API
		$templatePathAndFilename = 'resource://BghDevtools/Private/Generator/Model/EntityRepositoryApiTemplate.php.tmpl';
		
		$targetFile = $basePath.'/Classes/Domain';
		if ($namespace != '') $targetFile .= '/'.str_replace('\\', '/', $namespace);
		$targetFile .= '/Repository/'.$repository->getName().'Interface.php';
		
		$this->classReaderService->readFile($targetFile);
		
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables);
		
		$this->generateFile($targetFile, $fileContent);
		
		// IMPL
		$templatePathAndFilename = 'resource://BghDevtools/Private/Generator/Model/EntityRepositoryImplTemplate.php.tmpl';
		
		$targetFile = $basePath.'/Classes/Domain';
		if ($namespace != '') $targetFile .= '/'.str_replace('\\', '/', $namespace);
		$targetFile .= '/Repository/'.$repository->getName().'.php';
		
		$this->classReaderService->readFile($targetFile);
		
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables);
		
		$this->generateFile($targetFile, $fileContent);
	}

	/**
	 * Generates a factory class file
	 * 
	 * @param string $packageKey
	 * @param string $subpackage
	 * @param string $namespace
	 * @param string $basePath
	 * @param \F3\BghDevtools\Domain\Model\DomainFactory $factory
	 */
	public function generateFactoryClass($packageKey, $subpackage, $namespace, $basePath, \F3\BghDevtools\Domain\Model\DomainFactory $factory)
	{
		$contextVariables = array();
		$contextVariables['packageKey'] = $packageKey;
		$contextVariables['subpackage'] = $subpackage;
		$contextVariables['isInSubpackage'] = ($subpackage != '');
		$contextVariables['namespace'] = $namespace;
		$contextVariables['isInNamespace'] = ($namespace != '');
		$contextVariables['factoryClassName'] = $factory->getName();
		$contextVariables['factoryName'] = $factory->getName();
		$contextVariables['factoryDocumentation'] = $factory->getDocumentation();
		$contextVariables['factoryMethods'] = $factory->getMethods();
		
		// API
		$templatePathAndFilename = 'resource://BghDevtools/Private/Generator/Model/FactoryApiTemplate.php.tmpl';
		
		$targetFile = $basePath.'/Classes/Domain';
		if ($namespace != '') $targetFile .= '/'.str_replace('\\', '/', $namespace);
		$targetFile .= '/Factory/'.$factory->getName().'Interface.php';
		
		$this->classReaderService->readFile($targetFile);
		
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables);
		
		$this->generateFile($targetFile, $fileContent);
		
		// IMPL
		$templatePathAndFilename = 'resource://BghDevtools/Private/Generator/Model/FactoryImplTemplate.php.tmpl';
		
		$targetFile = $basePath.'/Classes/Domain';
		if ($namespace != '') $targetFile .= '/'.str_replace('\\', '/', $namespace);
		$targetFile .= '/Factory/'.$factory->getName().'.php';
		
		$this->classReaderService->readFile($targetFile);
		
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables);
		
		$this->generateFile($targetFile, $fileContent);
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
	private function normalizeClassName($packageKey, $subpackage, $namespace, $source, $default = false)
	{
		return \F3\BghDevtools\ViewHelpers\NormalizeTypeViewHelper::normalizeClassName($packageKey, $subpackage, $namespace, $source, 'Domain', $default);
	}

	/**
	 * Render the given template file with the given variables
	 * 
	 * notice: taken from KickStart
	 *
	 * @param string $templatePathAndFilename
	 * @param array $contextVariables
	 * @return string
	 * @author Christopher Hlubek <hlubek@networkteam.com>
	 */
	protected function renderTemplate($templatePathAndFilename, array $contextVariables) {
		$contextVariables['sections'] = $this->classReaderService;
		$contextVariables['generatedInfo'] = 'GENERATED by BghDevtools V0.0.1';
		 
		$templateSource = \F3\FLOW3\Utility\Files::getFileContents($templatePathAndFilename, FILE_TEXT);
		if ($templateSource === FALSE) {
			throw new \F3\Fluid\Core\Exception('The template file "' . $templatePathAndFilename . '" could not be loaded.', 1225709595);
		}
		$parsedTemplate = $this->templateParser->parse($templateSource);

		$renderingContext = $this->buildRenderingContext($contextVariables);

		return $parsedTemplate->render($renderingContext);
	}

	/**
	 * Build the rendering context
	 * 
	 * notice: taken from KickStart
	 *
	 * @param array $contextVariables
	 */
	protected function buildRenderingContext(array $contextVariables) {
		$renderingContext = $this->objectManager->create('F3\Fluid\Core\Rendering\RenderingContextInterface');

		$renderingContext->injectTemplateVariableContainer($this->objectManager->create('F3\Fluid\Core\ViewHelper\TemplateVariableContainer', $contextVariables));
		$renderingContext->injectViewHelperVariableContainer($this->objectManager->create('F3\Fluid\Core\ViewHelper\ViewHelperVariableContainer'));

		return $renderingContext;
	}

	/**
	 * Generate a file with the given content and add it to the
	 * generated files
	 *
	 * notice: taken from KickStart
	 * 
	 * @param string $targetPathAndFilename
	 * @param string $fileContent
	 * @return void
	 * @author Christopher Hlubek <hlubek@networkteam.com>
	 */
	protected function generateFile($targetPathAndFilename, $fileContent)
	{
		if (!is_dir(dirname($targetPathAndFilename)))
		{
			\F3\FLOW3\Utility\Files::createDirectoryRecursively(dirname($targetPathAndFilename));
		}
		file_put_contents($targetPathAndFilename, $fileContent);
	}
}
