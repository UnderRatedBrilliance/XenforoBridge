<?php namespace XenforoBridge\User;

use XenForo_Authentication_Abstract;
use XenForo_DataWriter;
use XenForo_Model_Ip;
use XenForo_Phrase;
use XenforoBridge\Contracts\UserInterface;
use XenForo_Model_User as XenforoUser;
use XenforoBridge\XenforoBridge;


class User implements UserInterface
{
    /**
     * Stores Xenforo User Model
     *
     * @var XenforoUser | \XenForo_Model_User
     */
    protected $user;

    /**
     * Construct XenforoBridge User Class
     */
    public function __construct()
    {
        $this->setUser(new XenforoUser);
    }

    /**
     * Set User Model
     *
     * @param XenforoUser $user
     */
    public function setUser(XenforoUser $user)
    {
        $this->user = $user;
    }

    /**
     * Get User Model
     *
     * @return void|XenforoUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get User by Id - returns empty array if user does not exist
     *
     * @param $id
     * @return array
     */
    public function getUserById($id)
    {
        return $this->user->getUserById($id)?:[];
    }

    /**
     * Get User by Email - returns empty array if user does not exist
     *
     * @param $email
     * @return array
     */
    public function getUserByEmail($email)
    {
        return $this->user->getUserByEmail($email)?: [];
    }

    /**
     * Get User by Username - returns empty array if user does not exist
     *
     * @param $name
     * @return array
     */
    public function getUserByUsername($name)
    {
        return $this->user->getUserByName($name)?:[];
    }

    /**
     * Validates Passwords and Returns Hashed value
     *
     * @todo refactor this method clean up and break down into smaller methods also rename to better illustrate it's purpose
     * @param $password
     * @param bool|false $passwordConfirm
     * @param XenForo_Authentication_Abstract|null $auth
     * @param bool|false $requirePassword
     * @return array|XenForo_Phrase
     */
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

    /**
     * Add User to Xenforo
     *
     * @todo refactor this method clean up and break down into smaller methods
     * @param $email
     * @param $username
     * @param $password
     * @param array $additional
     * @param int $languageId
     * @return array|XenForo_Phrase
     * @throws \Exception
     * @throws \XenForo_Exception
     */
    public function addUser($email,$username,$password,array $additional = [], $languageId = XenforoBridge::XENFOROBRIDGE_DEFAULT_LANGUAGE_ID)
    {
        // Verify Password
        $userPassword = $this->setPassword($password);
        if(is_object($userPassword) && get_class($userPassword) == 'XenForo_Phrase') {
            return $userPassword;
        }

        /**
         * @var $writer \XenForo_DataWriter_User
         */
        $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');

        $info = array_merge($additional, array(
            'username' => $username,
            'email' => $email,
            'user_group_id' => XenforoUser::$defaultRegisteredGroupId,
            'language_id' => $languageId,
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
