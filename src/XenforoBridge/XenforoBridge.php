<?php namespace XenforoBridge;

use XenforoBridge\Exceptions\XenforoAutloaderException;
use XenforoBridge\Contracts\VisitorInterface;
use XenforoBridge\Contracts\TemplateInterface;
use XenforoBridge\Template\Template;
use XenforoBridge\Visitor\Visitor;
use XenforoBridge\User\User;
use XenForo_Autoloader;
use XenForo_Application;
use XenForo_Session;
use Xenforo_Model_User;
use XenForo_DataWriter;
use XenForo_Phrase;
use XenForo_Authentication_Abstract;

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

	public function isAdmin()
	{
		return $this->visitor->isAdmin();
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

	public function getUserById($id)
	{
		return (new XenForo_Model_User)->getUserById($id)?:[];
	}

	/**
	 * Retrieve Xenforo User by Email
	 *
	 * If no user is found returns empty array
	 *
	 * @param $email
	 * @return array
	 */
	public function getUserByEmail($email)
	{
		return (new XenForo_Model_User)->getUserByEmail($email)?:[];
	}

	public function getUserByName($name)
	{
		return (new XenForo_Model_User)->getUserByName($name)?:[];
	}

	public function setPassword($password, $passwordConfirm = false, XenForo_Authentication_Abstract $auth = null, $requirePassword = false)
	{
		if ($requirePassword && $password === '')
		{
			return new XenForo_Phrase('please_enter_valid_password');
		}
		if ($passwordConfirm !== false && $password !== $passwordConfirm)
		{
			return new XenForo_Phrase('passwords_did_not_match');
		}
		if (!$auth)
		{
			$auth = XenForo_Authentication_Abstract::createDefault();
		}
		$authData = $auth->generate($password);
		if (!$authData)
		{
			return new XenForo_Phrase('please_enter_valid_password');
		}
		return array('scheme_class' => $auth->getClassName(), 'data' => $authData);
	}

	public function addUser($email,$username,$password,array $additional = [])
	{
		// Verify Password
		$userPassword = $this->setPassword($password);
		if(is_object($userPassword) && get_class($userPassword) == 'XenForo_Phrase') {
			return $userPassword;
		}

		$writer = XenForo_DataWriter::create('XenForo_DataWriter_User');

		$info = array_merge($additional, array(
			'username' => $username,
			'email' => $email,
			'user_group_id' => XenForo_Model_User::$defaultRegisteredGroupId,
			'language_id' => $this->getVisitor()->get('language_id'),
		));

		$writer->advanceRegistrationUserState();

		$writer->bulkSet($info);

		// Set user password
		$writer->set('scheme_class', $userPassword['scheme_class']);
		$writer->set('data', $userPassword['data'], 'xf_user_authenticate');

		// Save user
		$writer->save();
		$user = $writer->getMergedData();

		if(!$user['user_id']) {
			return new XenForo_Phrase('user_was_not_created');
		}
		// log the ip of the user registering
		XenForo_Model_Ip::log($user['user_id'], 'user', $user['user_id'], 'register');

		/*if ($user['user_state'] == 'email_confirm') {
			XenForo_Model::create('XenForo_Model_UserConfirmation')->sendEmailConfirmation($user);
		}*/
		return $user['user_id'];
	}
}
