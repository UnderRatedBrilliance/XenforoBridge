<?php namespace XenforoBridge\User;

use XenforoBridge\Contracts\VisitorInterface;
use XenForo_Model_User as XenUser;


class User implements VisitorInterface
{

    public function getCurrentVisitor()
    {
        return XenForo_Visitor::getInstance();
    }

    public function isBanned()
    {
        return $this->getCurrentVisitor()->toArray()['is_banned'];
    }

    public function isSuperAdmin()
    {
        return $this->getCurrentVisitor()->isSuperAdmin();
    }

    public function isLoggedIn()
    {
        return $this->getCurrentVisitor()->getUserId();
    }
}
