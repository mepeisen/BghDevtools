<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\Controller;

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
 * Controller for the project generator
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CliProjectController extends \F3\FLOW3\MVC\Controller\ActionController
{

	/**
	 * @var array
	 */
	protected $supportedRequestTypes = array('F3\FLOW3\MVC\CLI\Request');
	
	/**
	 * Index action - displays a help message.
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$this->helpAction();
	}

	/**
	 * Help action - displays a help message
	 *
	 * @return void
	 */
	public function helpAction()
	{
		$this->response->appendContent(
			'Bghosting developer tools: Project generator' . PHP_EOL .
			'Usage:' . PHP_EOL .
			' php Tooling.php project <action> [<args>]' . PHP_EOL .
			PHP_EOL .
			' available actions' . PHP_EOL .
			'   generate       generates a project from project xml file' . PHP_EOL .
			'                  arguments: --xml <xml-project-file>' . PHP_EOL .
			'                  example: php Tooling.php project generate --xml my-xml-file.xml' . PHP_EOL . 
			'                  note: the xml must be located at a special location.' . PHP_EOL
		);
	}
	
	/**
	 * Array of already found and checked projects
	 * @var array(string=>bool)
	 */
	protected $projectSummary = array();
	
	/**
	 * Simulation flag
	 * @var boolean
	 */
	protected $simulation = false;
	
	/**
	 * Generate the whole project using a project xml file.
	 *
	 * @param string $xml The name of the xml file
	 * @param boolean $simulation Simulation flag
	 * 
	 * @return void
	 */
	public function generateAction($xml, $simulation = false)
	{
	    $this->simulation = $simulation;
		$basePath = realpath(dirname($xml));
		if (!is_file($xml) || !is_readable($xml))
		{
			$this->response->appendContent('ERROR: Xml file "'.$xml.'" not valid or not readable!' . PHP_EOL);
			return;
		}
		
		$file = $this->objectManager->create('F3\BghDevtools\Domain\Project\ProjectFile');
		$file->loadXml($xml);
		
		$wsroot = FLOW3_WORKSPACE_ROOT;
		$this->response->appendContent('looking up dependencies...' . PHP_EOL);
		$this->checkoutDependencies($wsroot, $file);
		
		$this->response->appendContent('checking packages...' . PHP_EOL);
		$this->checkPackages($file, $basePath);
		
		// check eclipse configuration
		$this->response->appendContent('  checking eclipse project configuration...' . PHP_EOL);
		$this->mkdir("$basePath/.settings");
		
		// 1) dependencies in .buildpath
		$this->checkBuildpath($file, $basePath);
		
		// 2) Existance of .settings/.jsdtscope
		$this->createFile("$basePath/.settings/jsdtscope",
		    "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
		    "<classpath>\n".
		    "	<classpathentry kind=\"src\" path=\"\"/>\n".
		    "	<classpathentry kind=\"con\" path=\"org.eclipse.wst.jsdt.launching.JRE_CONTAINER\"/>\n".
		    "	<classpathentry kind=\"con\" path=\"org.eclipse.wst.jsdt.launching.WebProject\">\n".
		    "		<attributes>\n".
		    "			<attribute name=\"hide\" value=\"true\"/>\n".
		    "		</attributes>\n".
		    "	</classpathentry>\n".
		    "	<classpathentry kind=\"con\" path=\"org.eclipse.wst.jsdt.launching.baseBrowserLibrary\"/>\n".
		    "	<classpathentry kind=\"output\" path=\"\"/>\n".
		    "</classpath>");
		
		// 3) Existance of .settings/org.eclipse.core.resource.prefs
		$this->createFile("$basePath/.settings/org.eclipse.core.resource.prefs", "eclipse.preferences.version=1\nencoding/<project>=UTF-8");
		
		// 4) Existance of .settings/org.eclipse.core.runtime.prefs
		$this->createFile("$basePath/.settings/org.eclipse.core.runtime.prefs", "eclipse.preferences.version=1\nline.separator=\\n");
		
		// 5) Existance of and dependencies in .settings/org.eclipse.php.core.prefs
		$this->checkPhpCorePrefs($file, $basePath);
		
		// 6) Existance of org.eclipse.wst.jsdt.ui.superType.container
		$this->createFile("$basePath/.settings/org.eclipse.wst.jsdt.ui.superType.container", "org.eclipse.wst.jsdt.launching.baseBrowserLibrary");
		
		// 7) Existance of org.eclipse.wst.jsdt.ui.superType.name
		$this->createFile("$basePath/.settings/org.eclipse.wst.jsdt.ui.superType.name", "Window");
		
		// 8) Script Build/create_project.ant.xml
		$this->mkdir("$basePath/Build");
		$this->createFile("$basePath/Build/create_project.ant.xml",
		    "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
		    "<project name=\"CreateProject\" default=\"build\" basedir=\"./\">\n".
		    "	\n".
		    "	<dirname property=\"antfile.dir\" file=\"\${ant.file}\" />\n".
		    "	<dirname property=\"project.dir\" file=\"\${antfile.dir}\" />\n".
		    "	\n".
		    "	<target name=\"build\">\n".
		    "		<exec executable=\"php\">\n".
		    "			<arg value=\"\${project.dir}/../com_bghosting_flow3_devtools/Tooling.php\" />\n".
		    "			<arg value=\"project\" />\n".
		    "			<arg value=\"generate\" />\n".
		    "			<arg value=\"--xml\" />\n".
		    "			<arg value=\"\${project.dir}/project.bgh.xml\" />\n".
		    "		</exec>\n".
		    "	</target>\n".
		    "</project>");
		
		// 9) Script Build/create_model.ant.xml
		$contents =
			"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
		    "<project name=\"CreateModel\" default=\"build\" basedir=\"./\">\n".
		    "	\n".
		    "	<dirname property=\"antfile.dir\" file=\"\${ant.file}\" />\n".
		    "	<dirname property=\"project.dir\" file=\"\${antfile.dir}\" />\n".
		    "	\n".
		    "	<target name=\"build\">\n";
		foreach ($file->getPackages() as $pkg)
		{
			/* @var $pkg \F3\BghDevtools\Domain\Project\Package */
		    $name = $pkg->getName();
		    foreach ($pkg->getModules() as $mod)
	        {
	        	/* @var $mod \F3\BghDevtools\Domain\Project\PackageModule */
	            $modname = $mod->getName();
	            $contents .=
	            	"		<exec executable=\"php\">\n".
        		    "			<arg value=\"\${project.dir}/../com_bghosting_flow3_devtools/Tooling.php\" />\n".
        		    "			<arg value=\"model\" />\n".
        		    "			<arg value=\"generate\" />\n".
        		    "			<arg value=\"--xml\" />\n".
        		    "			<arg value=\"\${project.dir}/Packages/$name/$modname/Model/model.xml\" />\n".
        		    "		</exec>\n";
	        }
		}
		$contents .=
			"	</target>\n".
		    "</project>";
		$this->createFile("$basePath/Build/create_model.ant.xml", $contents, true);
		
		// 10) Script Build/create_services.ant.xml
		$contents =
			"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
		    "<project name=\"CreateServices\" default=\"build\" basedir=\"./\">\n".
		    "	\n".
		    "	<dirname property=\"antfile.dir\" file=\"\${ant.file}\" />\n".
		    "	<dirname property=\"project.dir\" file=\"\${antfile.dir}\" />\n".
		    "	\n".
		    "	<target name=\"build\">\n";
		foreach ($file->getPackages() as $pkg)
		{
			/* @var $pkg \F3\BghDevtools\Domain\Project\Package */
		    $name = $pkg->getName();
		    foreach ($pkg->getModules() as $mod)
	        {
	        	/* @var $mod \F3\BghDevtools\Domain\Project\PackageModule */
	            $modname = $mod->getName();
	            $contents .=
	            	"		<exec executable=\"php\">\n".
        		    "			<arg value=\"\${project.dir}/../com_bghosting_flow3_devtools/Tooling.php\" />\n".
        		    "			<arg value=\"service\" />\n".
        		    "			<arg value=\"generate\" />\n".
        		    "			<arg value=\"--xml\" />\n".
        		    "			<arg value=\"\${project.dir}/Packages/$name/$modname/Model/service.xml\" />\n".
        		    "		</exec>\n";
	        }
		}
		$contents .=
			"	</target>\n".
		    "</project>";
		$this->createFile("$basePath/Build/create_service.ant.xml", $contents, true);
		
		// 11) Ant builders
		// TODO
		
		// 12) .project
		// TODO
		
		// check eclipse project configuration
		$this->response->appendContent('  checking directory layout...' . PHP_EOL);
		
		// 1) Configuration/Development/Settings.yaml
		$this->mkdir("$basePath/Configuration");
		$this->mkdir("$basePath/Configuration/Development");
		$this->createFile("$basePath/Configuration/Development/Settings.yaml",
		    "#                                                                        #\n".
		    "# Configuration for TYPO3 in development context.                        #\n".
		    "#                                                                        #\n".
		    "# This file contains additions to the base configuration for the FLOW3   #\n".
		    "# Framework. Just add your own modifications as necessary.               #\n".
		    "#                                                                        #\n".
		    "# Please refer to the default configuration file or the FLOW3 manual for #\n".
		    "# possible configuration options.                                        #\n".
		    "#                                                                        #\n".
		    "\n".
		    "FLOW3:\n".
		    "  error:\n".
		    "    exceptionHandler:\n".
		    "      className: 'F3\FLOW3\Error\DebugExceptionHandler'\n".
		    "    errorHandler:\n".
		    "      exceptionalErrors: [%E_USER_ERROR%, %E_RECOVERABLE_ERROR%, %E_WARNING%, %E_NOTICE%, %E_USER_WARNING%, %E_USER_NOTICE%, %E_STRICT%]\n".
		    "\n".
		    "  monitor:\n".
		    "    detectClassChanges: y\n".
		    "\n".
		    "  resource:\n".
		    "    publishing:\n".
		    "      detectPackageResourceChanges: y\n".
		    "\n".
		    "  log:\n".
		    "    systemLogger:\n".
		    "      backendOptions: { logFileURL: %FLOW3_PATH_DATA%Logs/%FLOW3_SAPITYPE%/FLOW3_Development.log, createParentDirectories: y, severityThreshold: %LOG_DEBUG% }\n".
		    "    \n".
		    "    security:\n".
		    "      enable: yes\n".
		    "      ");
		
		// 2) Configuration/Production/Settings.yaml
		$this->mkdir("$basePath/Configuration/Production");
		$this->createFile("$basePath/Configuration/Production/Settings.yaml",
		    "#                                                                        #\n".
		    "# Configuration for TYPO3 in development context.                        #\n".
		    "#                                                                        #\n".
		    "# This file contains additions to the base configuration for the FLOW3   #\n".
		    "# Framework. Just add your own modifications as necessary.               #\n".
		    "#                                                                        #\n".
		    "# Please refer to the default configuration file or the FLOW3 manual for #\n".
		    "# possible configuration options.                                        #\n".
		    "#                                                                        #\n".
		    "\n".
		    "FLOW3:\n".
		    "  configuration:\n".
		    "    compileConfigurationFiles: y\n".
		    "\n".
		    "  log:\n".
		    "    systemLogger:\n".
		    "      backendOptions: { logFileURL: %FLOW3_PATH_DATA%Logs/%FLOW3_SAPITYPE%/FLOW3_Production.log, createParentDirectories: y, severityThreshold: %LOG_WARNING% }\n".
		    "    \n".
		    "    security:\n".
		    "      enable: yes\n".
		    "      ");
				
		// 3) Configuration/Testing/Settings.yaml
		$this->mkdir("$basePath/Configuration/Testing");
		$this->createFile("$basePath/Configuration/Testing/Settings.yaml",
		    "#                                                                        #\n".
		    "# Configuration for TYPO3 in development context.                        #\n".
		    "#                                                                        #\n".
		    "# This file contains additions to the base configuration for the FLOW3   #\n".
		    "# Framework. Just add your own modifications as necessary.               #\n".
		    "#                                                                        #\n".
		    "# Please refer to the default configuration file or the FLOW3 manual for #\n".
		    "# possible configuration options.                                        #\n".
		    "#                                                                        #\n".
		    "\n".
		    "\n".
		    "FLOW3:\n".
		    "  error:\n".
		    "    ##\n".
		    "    # Use the more meaningful debug exception handler.\n".
		    "    exceptionHandler:\n".
		    "      className: 'F3\FLOW3\Error\DebugExceptionHandler'\n".
		    "\n".
		    "    ##\n".
		    "    # All errors should result in exceptions.\n".
		    "    errorHandler:\n".
		    "      exceptionalErrors: [%E_USER_ERROR%, %E_RECOVERABLE_ERROR%, %E_WARNING%, %E_NOTICE%, %E_USER_WARNING%, %E_USER_NOTICE%, %E_STRICT%]\n".
		    "    \n".
		    "    security:\n".
		    "      enable: yes\n".
		    "		    ");
		
		// 4) Data/Logs
		$this->mkdir("$basePath/Data");
		$this->mkdir("$basePath/Data/Logs");
		
		// 5) Data/Persistent
		$this->mkdir("$basePath/Data/Persistent");
		
		// 6) Data/Resources/Private
		$this->mkdir("$basePath/Data/Resources");
		$this->mkdir("$basePath/Data/Resources/Private");
		
		// 7) Data/Resources/Public
		$this->mkdir("$basePath/Data/Resources/Public");
		
		// 8) Data/Temporary
		$this->mkdir("$basePath/Data/Temporary");
		
		// 9) .htaccess
		$this->createFile("$basePath/.htaccess",
		    "SetEnv FLOW3_CONTEXT Development\n".
		    "SetEnv FLOW3_ROOTPATH ../../com_bghosting_flow3_devfwkflow3\n".
		    "SetEnv FLOW3_WORKSPACE_ROOT ../..\n".
		    "SetEnv FLOW3_PROJECT_NAME ".basename($basePath)."\n".
		    "SetEnv FLOW3_PROJECT_DEPENDENCIES ".implode(";", array_keys($this->projectSummary))."\n", true);
		
		// 10) Web/.htaccess
		$this->mkdir("$basePath/Web");
		$this->createFile("$basePath/Web/.htaccess",
		    "#\n".
		    "# FLOW3 context setting\n".
		    "#\n".
		    "\n".
		    "# You can specify a default context by activating this option:\n".
		    "# SetEnv FLOW3_CONTEXT Development\n".
		    "\n".
		    "# If the root path is not the parent of the Web directory, FLOW3's root path must be\n".
		    "# specified manually:\n".
		    "# SetEnv FLOW3_ROOTPATH /var/www/myapp/\n".
		    "\n".
		    "#\n".
		    "# mod_rewrite configuration\n".
		    "#\n".
		    "<IfModule mod_rewrite.c>\n".
		    "\n".
		    "	# Enable URL rewriting\n".
		    "	RewriteEngine On\n".
		    "\n".
		    "	# Set flag so we know URL rewriting is available\n".
		    "	SetEnv FLOW3_REWRITEURLS 1\n".
		    "\n".
		    "	# You will have to enable the following option and change the path if you\n".
		    "	# experience problems while your installation is located in a subdirectory\n".
		    "	# of the website root.\n".
		    "	#	RewriteBase /\n".
		    "\n".
		    "	# Stop rewrite processing no matter if a package resource, robots.txt etc. exists or not\n".
		    "	RewriteRule ^(_Resources/Packages/|robots\.txt|favicon\.ico) - [L]\n".
		    "\n".
		    "	# Stop rewrite process if the path points to a static file anyway\n".
		    "	RewriteCond %{REQUEST_FILENAME} -f [OR]\n".
		    "	RewriteCond %{REQUEST_FILENAME} -l [OR]\n".
		    "	RewriteCond %{REQUEST_FILENAME} -d\n".
		    "\n".
		    "	RewriteRule .* - [L]\n".
		    "\n".
		    "	# Perform rewriting of persitent resource files\n".
		    "	RewriteRule ^(_Resources/Persistent/.{40})/.+(\..+) $1$2 [L]\n".
		    "\n".
		    "	# Make sure that not existing resources don't execute FLOW3\n".
		    "	RewriteRule ^_Resources/.* - [L]\n".
		    "\n".
		    "	# Continue only if the file/symlink/directory does not exist\n".
		    "	RewriteRule (.*) index.php\n".
		    "\n".
		    "</IfModule>\n".
		    "\n".
		    "ErrorDocument 500 \"<h1>Application Error</h1><p>The FLOW3 application could not be launched.</p>\"\n".
		    "");
		
		// 11) Web/index.php
		$this->createFile("$basePath/Web/index.php",
		    "<?php\n".
		    "declare(ENCODING = 'utf-8');\n".
		    "\n".
		    "/*                                                                        *\n".
		    " * This script belongs to the FLOW3 framework.                            *\n".
		    " *                                                                        *\n".
		    " * It is free software; you can redistribute it and/or modify it under    *\n".
		    " * the terms of the GNU Lesser General Public License as published by the *\n".
		    " * Free Software Foundation, either version 3 of the License, or (at your *\n".
		    " * option) any later version.                                             *\n".
		    " *                                                                        *\n".
		    " * This script is distributed in the hope that it will be useful, but     *\n".
		    " * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *\n".
		    " * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *\n".
		    " * General Public License for more details.                               *\n".
		    " *                                                                        *\n".
		    " * You should have received a copy of the GNU Lesser General Public       *\n".
		    " * License along with the script.                                         *\n".
		    " * If not, see http://www.gnu.org/licenses/lgpl.html                      *\n".
		    " *                                                                        *\n".
		    " * The TYPO3 project - inspiring people to share!                         *\n".
		    " *                                                                        */\n".
		    "\n".
		    "\$rootPath = isset(\$_SERVER['FLOW3_ROOTPATH']) ? \$_SERVER['FLOW3_ROOTPATH'] : FALSE;\n".
		    "if (\$rootPath === FALSE && isset(\$_SERVER['REDIRECT_FLOW3_ROOTPATH'])) {\n".
		    "	\$rootPath = \$_SERVER['REDIRECT_FLOW3_ROOTPATH'];\n".
		    "}\n".
		    "if (\$rootPath === FALSE) {\n".
		    " \$rootPath = dirname(__FILE__) . '/../';\n".
		    "} elseif (substr(\$rootPath, -1) !== '/') {\n".
		    "	\$rootPath .= '/';\n".
		    "}\n".
		    "\n".
		    "require(\$rootPath.'BghFwk/utility.php');\n".
		    "\n".
		    "require(\$rootPath . 'Packages/Framework/FLOW3/Scripts/FLOW3.php');\n".
		    "\n".
		    "?>");
		
		// check git
		$this->response->appendContent('  checking git...' . PHP_EOL);
		
		// 1) Configuration/.gitignore
		$this->createFile("$basePath/Configuration/.gitignore", "/PackageStates.yaml\n");
		
		// 2) Data/Logs/.do_not_delete_this
		$this->createFile("$basePath/Data/Logs/.do_not_delete_this", "");
		
		// 3) Data/Persistent/.do_not_delete_this
		$this->createFile("$basePath/Data/Persistent/.do_not_delete_this", "");
			
		// 4) Data/Resources/Private/.do_not_delete_this
		$this->createFile("$basePath/Data/Resources/Private/.do_not_delete_this", "");
			
		// 5) Data/Resources/Public/.do_not_delete_this
		$this->createFile("$basePath/Data/Resources/Public/.do_not_delete_this", "");
		
		// 6) Data/Temporary/.do_not_delete_this
		$this->createFile("$basePath/Data/Temporary/.do_not_delete_this", "");
		
		// 7) Data/.gitignore
		$this->createFile("$basePath/Configuration/.gitignore",
		    "/Logs/**\n".
		    "!/Logs/.do_not_delete_this\n".
		    "/Persistent/EncryptionKey\n".
		    "/Persistent/Objects.db\n".
		    "/Temporary/**\n".
		    "!/Temporary/.do_not_delete_this\n".
		    "");
		
		// 8) Web/.gitignore
		$this->createFile("$basePath/Web/.gitignore", "/_Resources\n");
		
		// 9) git init
		if (!file_exists("$basePath/.git/index"))
		{
		    $command = 'git init';
		    $this->response->appendContent('  '.$command . PHP_EOL);
		    if (!$this->simulation)
		    {
		        chdir($basePath);
		        $pipes = array();
                $proc = proc_open($command, array(array("pipe","r"), array("pipe","w"), array("pipe","w")), $pipes);
                if (is_resource($proc))
                {
                    $this->response->appendContent(stream_get_contents($pipes[1]));
                    $this->response->appendContent(PHP_EOL);
                }
                else
                {
                    throw new \Exception('ERROR: Could not invoke '.$command);
                }
            }
		}
	}
	
	/**
	 * Checks/creates the file .settings/org.eclipse.php.core.prefs
	 * 
	 * @param \F3\BghDevtools\Domain\Project\ProjectFile $file
	 * @param string $basePath
	 */
	protected function checkPhpCorePrefs(\F3\BghDevtools\Domain\Project\ProjectFile $file, $basePath)
	{
	    $projectname = basename($basePath);
	    if (!file_exists("$basePath/.settings/org.eclipse.php.core.prefs"))
		{
		    $contentPackages = "";
		    foreach ($file->getPackages() as $pkg)
		    {
		    	/* @var $pkg \F3\BghDevtools\Domain\Project\Package */
		        $name = $pkg->getName();
		        foreach ($pkg->getModules() as $mod)
	            {
	            	/* @var $mod \F3\BghDevtools\Domain\Project\PackageModule */
	                $modname = $mod->getName();
	                $contentPackages .= "\\u00050;/$projectname/Packages/$name/$modname/Classes";
	                $contentPackages .= "\\u00050;/$projectname/Packages/$name/$modname/Tests";
	            }
		    }
		    $contentDependencies = "";
		    foreach ($this->projectSummary as $name => $v)
		    {
		        $contentDependencies .= "\\u00052;/$name";
		    }
		    $content =
		        "eclipse.preferences.version=1\n".
		        "include_path=2;/com_bghosting_flow3_devfwkflow3$contentPackages$contentDependencies\n".
		        "phpVersion=php5.3\n".
		        "use_asp_tags_as_php=false\n";
		    $this->createFile("$basePath/.buildpath", $content);
		}
		else
		{
		    $this->response->appendContent('  checking dependencies in existing .settings/org.eclipse.php.core.prefs...' . PHP_EOL);
		    $content = file_get_contents("$basePath/.settings/org.eclipse.php.core.prefs");
		    $modulesClasses = array();
            $modulesTests = array();
            foreach ($file->getPackages() as $pkg)
		    {
		    	/* @var $pkg \F3\BghDevtools\Domain\Project\Package */
		        $name = $pkg->getName();
		        foreach ($pkg->getModules() as $mod)
	            {
	            	/* @var $mod \F3\BghDevtools\Domain\Project\PackageModule */
	                $modname = $mod->getName();
	                $modulesClasses[$name][$modname] = true;
	                $modulesTests[$name][$modname] = true;
	            }
		    }
		    $projects = $this->projectSummary;
		    $changed = false;
		    $includes = "2;/com_bghosting_flow3_devfwkflow3";
		    $woincludes = $content;
		    $incpos = strpos('include_path=', $content);
		    if ($incpos !== false)
		    {
		        $elpos = strpos("\n", $content, $incpos);
		        if ($elpos !== false)
		        {
		            $includes = substr($content, $incpos + 13, $elpos);
		            $woincludes = substr($content, 0, $incpos) . substr($content, $elpos);
		        }
		        else
		        {
		            $includes = substr($content, $incpos + 13);
		            $woincludes = substr($content, 0, $incpos)."\n";
		        }
		        $exploded = explode("\\u0005", trim($includes));
		        foreach ($exploded as $exp)
		        {
		            $parts = explode(";", $exp);
		            switch ($parts[0])
		            {
		                case '0':
		                    $path = $parts[1];
		                    $result = array();
		                    if (preg_match("/^\/$prjname\/Packages\/(?P<Pkg>[^\/]+)\/(?P<Mod>[^\/]+)\/Classes$/", $path, $result))
		                    {
		                        if (isset($modulesClasses[$result['Pkg']][$result['Mod']])) unset($modulesClasses[$result['Pkg']][$result['Mod']]);
		                    }
		                    elseif (preg_match("/^\/$prjname\/Packages\/(?P<Pkg>[^\/]+)\/(?P<Mod>[^\/]+)\/Tests$/", $path, $result))
		                    {
		                        if (isset($modulesTests[$result['Pkg']][$result['Mod']])) unset($modulesTests[$result['Pkg']][$result['Mod']]);
		                    }
		                    break;
		                case '2':
		                    $prjname = substr($parts[1], 1);
		                    if (isset($projects[$prjname])) unset($projects[$prjname]);
		                    break;
		            }
		        }
		    }
		    
		    foreach ($projects as $prj => $v)
		    {
		        $changed = true;
		        $includes .= "\u00052;/$prj";
		    }
		    foreach ($modulesClasses as $pkg => $mods)
		    {
		        foreach ($mods as $mod => $v)
		        {
		            $changed = true;
    		        $includes .= "\u00050;/$projectname/Packages/$pkg/$mod/Classes";
		        }
		    }
		    foreach ($modulesTests as $pkg => $mods)
		    {
		        foreach ($mods as $mod => $v)
		        {
		            $changed = true;
    		        $includes .= "\u00050;/$projectname/Packages/$pkg/$mod/Tests";
		        }
		    }
		    
		    if ($changed)
		    {
		        $this->createFile("$basePath/.settings/org.eclipse.php.core.prefs", $woincludes."include_path=$includes\n");
		    }
		}
	}
	
	/**
	 * Checks/creates the file .buildpath
	 * 
	 * @param \F3\BghDevtools\Domain\Project\ProjectFile $file
	 * @param string $basePath
	 */
	protected function checkBuildpath(\F3\BghDevtools\Domain\Project\ProjectFile $file, $basePath)
	{
	    if (!file_exists("$basePath/.buildpath"))
		{
		    $contentPackages = "";
		    foreach ($file->getPackages() as $pkg)
		    {
		    	/* @var $pkg \F3\BghDevtools\Domain\Project\Package */
		        $name = $pkg->getName();
		        foreach ($pkg->getModules() as $mod)
	            {
	            	/* @var $mod \F3\BghDevtools\Domain\Project\PackageModule */
	                $modname = $mod->getName();
	                $contentPackages .= "	<buildpathentry kind=\"src\" path=\"Packages/$name/$modname/Classes\"/>\n";
	                $contentPackages .= "	<buildpathentry kind=\"src\" path=\"Packages/$name/$modname/Tests\"/>\n";
	            }
		    }
		    $contentDependencies = "";
		    foreach ($this->projectSummary as $name => $v)
		    {
		        $contentDependencies .= "	<buildpathentry combineaccessrules=\"false\" kind=\"prj\" path=\"/$name\"/>\n";
		    }
		    $content =
		        "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
		        "<buildpath>\n".
		        $contentPackages.
		        "	<buildpathentry kind=\"con\" path=\"org.eclipse.php.core.LANGUAGE\"/>\n".
		        "	<buildpathentry combineaccessrules=\"false\" kind=\"prj\" path=\"/com_bghosting_flow3_devfwkflow3\"/>\n".
		        $contentDependencies.
		        "</buildpath>";
		    $this->createFile("$basePath/.buildpath", $content);
		}
		else
		{
		    $this->response->appendContent('  checking dependencies in existing .buildpath...' . PHP_EOL);
		    /* @var $prjxml \SimpleXmlElement */
		    $prjxml = simplexml_load_file("$basePath/.buildpath");
            $modulesClasses = array();
            $modulesTests = array();
            foreach ($file->getPackages() as $pkg)
		    {
		    	/* @var $pkg \F3\BghDevtools\Domain\Project\Package */
		        $name = $pkg->getName();
		        foreach ($pkg->getModules() as $mod)
	            {
	            	/* @var $mod \F3\BghDevtools\Domain\Project\PackageModule */
	                $modname = $mod->getName();
	                $modulesClasses[$name][$modname] = true;
	                $modulesTests[$name][$modname] = true;
	            }
		    }
		    $projects = $this->projectSummary;
		    $changed = false;
		    foreach ($prjxml->children() as $child)
		    {
		        if ($child->getName() == 'buildpathentry')
		        {
		            switch ((string)$child['kind'])
		            {
		                case 'prj':
		                    $prjname = substr((string)$child['path'], 1);
		                    if (isset($projects[$prjname])) unset($projects[$prjname]);
		                    break;
		                case 'src':
		                    $path = (string) $child['path'];
		                    $result = array();
		                    if (preg_match("/^Packages\/(?P<Pkg>[^\/]+)\/(?P<Mod>[^\/]+)\/Classes$/", $path, $result))
		                    {
		                        if (isset($modulesClasses[$result['Pkg']][$result['Mod']])) unset($modulesClasses[$result['Pkg']][$result['Mod']]);
		                    }
		                    elseif (preg_match("/^Packages\/(?P<Pkg>[^\/]+)\/(?P<Mod>[^\/]+)\/Tests$/", $path, $result))
		                    {
		                        if (isset($modulesTests[$result['Pkg']][$result['Mod']])) unset($modulesTests[$result['Pkg']][$result['Mod']]);
		                    }
		                    break;
		            }
		        }
		    }
		    foreach ($projects as $prj => $v)
		    {
		        $changed = true;
		        $child = $prjxml->addChild("buildpathentry");
		        $child->addAttribute('combineaccessrules', 'false');
		        $child->addAttribute('kind', 'prj');
		        $child->addAttribute('path', "/$prj");
		    }
		    foreach ($modulesClasses as $pkg => $mods)
		    {
		        foreach ($mods as $mod => $v)
		        {
		            $changed = true;
    		        $child = $prjxml->addChild("buildpathentry");
    		        $child->addAttribute('kind', 'src');
    		        $child->addAttribute('path', "Packages/$pkg/$mod/Classes");
		        }
		    }
		    foreach ($modulesTests as $pkg => $mods)
		    {
		        foreach ($mods as $mod => $v)
		        {
		            $changed = true;
    		        $child = $prjxml->addChild("buildpathentry");
    		        $child->addAttribute('kind', 'src');
    		        $child->addAttribute('path', "Packages/$pkg/$mod/Tests");
		        }
		    }
		    if ($changed)
		    {
		        $this->createFile("$basePath/.buildpath", $prjxml->asXML());
		    }
		}
	}
	
	/**
	 * Checks packages
	 * 
	 * @param \F3\BghDevtools\Domain\Project\ProjectFile $file
	 * @param string $basePath
	 */
	protected function checkPackages(\F3\BghDevtools\Domain\Project\ProjectFile $file, $basePath)
	{
	    $this->mkdir("$basePath/Packages");
   	    foreach ($file->getPackages() as $pkg)
		{
		    /* @var $pkg \F3\BghDevtools\Domain\Project\Package */
		    $name = $pkg->getName();
		    if (file_exists("$basePath/Packages/$name"))
		    {
		        $this->response->appendContent('  package '.$name.' already exists in project... checking modules...' . PHP_EOL);
		    }
		    else
		    {
		        $this->response->appendContent('  creating package '.$name.'...' . PHP_EOL);
		        $this->mkdir("$basePath/Packages/$name");
		    }
		    
	        foreach ($pkg->getModules() as $mod)
	        {
	            /* @var $mod \F3\BghDevtools\Domain\Project\PackageModule */
	            $modname = $mod->getName();
	            $modpath = "$basePath/Packages/$name/$modname";
	            if (file_exists($modpath))
	            {
	                $this->response->appendContent('  module '.$modname.' already exists... checking...' . PHP_EOL);
	                // TODO Check Package.xml
	                // TODO Check model.xml
	                // TODO Check service.xml
	                $this->createFile("$modpath/Meta/Package.xml", $this->getContentOfPackageXml($mod));
                    $this->createFile("$modpath/Model/model.xml", $this->getContentOfModelXml($mod));
                    $this->createFile("$modpath/Model/service.xml", $this->getContentOfServiceXml($mod));
	            }
	            else
	            {
	                $this->mkdir($modpath);
                    $this->mkdir("$modpath/Classes");
                    $this->mkdir("$modpath/Configuration");
                    $this->mkdir("$modpath/Documentation");
                    $this->mkdir("$modpath/Documentation/Manual");
                    $this->mkdir("$modpath/Documentation/Manual/DocBook");
                    $this->mkdir("$modpath/Documentation/Manual/DocBook/en");
                    $this->mkdir("$modpath/Meta");
                    $this->mkdir("$modpath/Model");
                    $this->mkdir("$modpath/Resources");
                    $this->mkdir("$modpath/Resources/Private");
                    $this->mkdir("$modpath/Resources/Public");
                    $this->mkdir("$modpath/Tests");
                    $this->createFile("$modpath/Meta/Package.xml", $this->getContentOfPackageXml($mod));
                    $this->createFile("$modpath/Model/model.xml", $this->getContentOfModelXml($mod));
                    $this->createFile("$modpath/Model/service.xml", $this->getContentOfServiceXml($mod));
	            }
	        }
		}
	}
	
	/**
	 * Returns the content of package xml file
	 * 
	 * @param \F3\BghDevtools\Domain\Project\PackageModule $mod
	 * 
	 * @return string
	 */
	protected function getContentOfPackageXml(\F3\BghDevtools\Domain\Project\PackageModule $mod)
	{
	    return "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n".
               "<package xmlns=\"http://typo3.org/ns/2008/flow3/package\">\n".
               "    <key>".$mod->getName()."</key>\n".
               "    <title>".$mod->getTitle()."</title>\n".
               "    <description>".$mod->getDescription()."</description>\n".
               "    <version>".$mod->getVersion()."</version>\n".
               "</package>";
	}
	
	/**
	 * Returns the content of model xml file
	 * 
	 * @param \F3\BghDevtools\Domain\Project\PackageModule $mod
	 * 
	 * @return string
	 */
	protected function getContentOfModelXml(\F3\BghDevtools\Domain\Project\PackageModule $mod)
	{
	    return "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n".
               "<model package=\"".$mod->getName()."\">\n".
               "    <domain namespace=\"\">\n".
               "    </domain>\n".
               "</package>";
	}
	
	/**
	 * Returns the content of service xml file
	 * 
	 * @param \F3\BghDevtools\Domain\Project\PackageModule $mod
	 * 
	 * @return string
	 */
	protected function getContentOfServiceXml(\F3\BghDevtools\Domain\Project\PackageModule $mod)
	{
	    return "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n".
               "<service package=\"".$mod->getName()."\">\n".
               "    <domain namespace=\"\">\n".
               "    </domain>\n".
               "</service>";
	}
	
	/**
	 * Creates a directory
	 * 
	 * @param string $path
	 */
	protected function mkdir($path)
	{
	    if (file_exists($path)) return;
	    $this->response->appendContent('      mkdir '.$path.'...' . PHP_EOL);
	    if (!$this->simulation)
	    {
	        mkdir($path);
	    }
	}
	
	/**
	 * Creates a file and puts the contents
	 * 
	 * @param string $path
	 * @param string $content
	 * @param boolean $overwriteOnExists
	 */
	protected function createFile($path, $content, $overwriteOnExists = false)
	{
	    if (!$overwriteOnExists && file_exists($path))
	    {
	        $this->response->appendContent("      file $path already exists... skipping..." . PHP_EOL);
	        return;
	    }
	    $this->response->appendContent("      create file $path..." . PHP_EOL);
	    if (!$this->simulation)
	    {
	        file_put_contents($path, $content);
	    }
	}
	
	/**
	 * Checks for dependencies
	 * 
	 * @param string $wsroot
	 * @param \F3\BghDevtools\Domain\Project\ProjectFile $file
	 */
	protected function checkoutDependencies($wsroot, \F3\BghDevtools\Domain\Project\ProjectFile $file)
	{
	    foreach ($file->getDependencies() as $dep)
		{
		    /* @var $dep \F3\BghDevtools\Domain\Project\Dependency */
		    $project = $dep->getProject();
		    if (isset($this->projectSummary[$project])) continue;
		    $this->projectSummary[$project] = true;
		    if (file_exists("$wsroot/$project"))
		    {
		        $this->response->appendContent('  project '.$project.' already exists in workspace...' . PHP_EOL);
		        $file2 = $this->objectManager->create('F3\BghDevtools\Domain\Project\ProjectFile');
		        $file2->loadXml("$wsroot/$project/project.bgh.xml");
		        $this->checkoutDependencies($wsroot, $file2);
		    }
		    else
		    {
		        $success = false;
		        foreach ($dep->getSources() as $src)
		        {
		            switch ($src->getSourceType())
		            {
		                case \F3\BghDevtools\Domain\Project\DependencySourceInterface::SOURCETYPE_SCM:
		                    switch ($src->getType())
		                    {
		                        case \F3\BghDevtools\Domain\Project\DependencySourceScm::TYPE_GIT:
		                            $command = 'git clone '.$src->getUrl().' '.$project;
		                            $this->response->appendContent('  '.$command . PHP_EOL);
		                            if (!$this->simulation)
		                            {
    		                            chdir($wsroot);
    		                            $pipes = array();
    		                            $proc = proc_open($command, array(array("pipe","r"), array("pipe","w"), array("pipe","w")), $pipes);
    		                            if (is_resource($proc))
    		                            {
    		                                $this->response->appendContent(stream_get_contents($pipes[1]));
    		                                $this->response->appendContent(PHP_EOL);
    		                            }
    		                            else
    		                            {
    		                                throw new \Exception('ERROR: Could not invoke '.$command);
    		                            }
		                            }
		                            $success = true;
		                            break;
		                    }
		                    break;
		            }
		            
		            if ($success)
		            {
		                break;
		            }
		        }
		        
		        if (!$success)
		        {
		            throw new \Exception('ERROR: Invalid source specified (internal error)');
		        }
		        
		        $file2 = $this->objectManager->create('F3\BghDevtools\Domain\Project\ProjectFile');
		        $file2->loadXml("$wsroot/$project/project.bgh.xml");
		        $this->checkoutDependencies($wsroot, $file2);
		    }
		}
	}
	
}
