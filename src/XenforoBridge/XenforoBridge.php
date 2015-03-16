<?php namespace XenforoBridge;

use XenforoBridge\Exceptions\XenforoAutloaderException;
use XenforoBridge\Contracts\VisitorInterface;
use XenforoBridge\Contracts\TemplateInterface;
use XenforoBridge\Template\Template;
use XenforoBridge\Visitor\Visitor;
use XenForo_Autoloader;
use XenForo_Application;
use XenForo_Session;
use Xenforo_Model_User;

class XenforoBridge
{
	protected $xenforoDir;
	protected $visitor;
	protected $template;

	public function __construct($xenforoDir, $xenforoBaseUrl)
	{
		//Inject Dependencies
		$this->xenforoDir = $xenforoDir;

		//Load Xenforo Autoloader
		$this->loadXenAutoloader($xenforoDir);

		//Intialize Xenforo Application
		XenForo_Autoloader::getInstance()->setupAutoloader($xenforoDir .'/library');
		XenForo_Application::initialize($xenforoDir . '/library', $xenforoDir);
		//XenForo_Application::set('page_start_time', $startTime);
		XenForo_Session::startPublicSession();

		//Load XenforoBridge Modules 
		$this->setVisitor(new Visitor);
		$this->setTemplate(new Template($xenforoBaseUrl));
	}

	public function setVisitor(VisitorInterface $visitor)
	{
		$this->visitor = $visitor;
	}

	public function setTemplate(TemplateInterface $template)
	{
		$this->template = $template;
	}

	/**
	 * Attempts to load Xenforo_Autloader.php throws exception if
	 * unable to find or load.
	 *
	 * @param string $xenforoDir - Full path to Xenforo Directory
	 * @return boolean
	 */
	protected function loadXenAutoloader($xenforoDir)
	{
		$path = $xenforoDir. '/library/XenForo/Autoloader.php';

		$autoloader = include($path);

		if(!$autoloader)
		{
			throw new XenforoAutloaderException('Could not load XenForo_Autoloader.php check path');
		}
	}

	public function getVisitor()
	{
		return $this->visitor->getCurrentVisitor();
	}

	public function isBanned()
	{
		return $this->visitor->isBanned();
	}

	public function isSuperAdmin()
	{
		return $this->visitor->isSuperAdmin();
	}

	public function isLoggedIn()
	{
		return $this->visitor->isLoggedIn();
	}

	public function renderTemplate( $name    = 'PAGE_CONTAINER',
									$content = '',
									$params  = array())
	{
		return $this->template->renderTemplate($name,$content,$params);
	}

	/**
	 * Retrieve Xenforo User by Id
	 *
	 * If no user is found returns empty array
	 *
	 * @param $id
	 * @return array
	 */
	public function getUserById($id)
	{
		return (new Xenforo_Model_User)->getUserById($id)?:[];
	}
}
