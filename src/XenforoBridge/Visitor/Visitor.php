<?php namespace XenforoBridge\Visitor;

use XenforoBridge\Contracts\VisitorInterface;
use XenForo_Visitor;


class Visitor implements VisitorInterface
{
	
	public function getCurrentVisitor()
	{
		return XenForo_Visitor::getInstance();
	}

	public function isBanned()
	{
		return (bool)$this->getCurrentVisitor()->toArray()['is_banned'];
	}

	public function isAdmin()
	{
		return (bool)$this->getCurrentVisitor()->toArray()['is_admin'];
	}
	
	public function isSuperAdmin()
	{
		return (bool)$this->getCurrentVisitor()->isSuperAdmin();
	}

	public function isLoggedIn()
	{
		return (bool)$this->getCurrentVisitor()->getUserId();
	}

	public function hasPermission($group,$permission)
	{
		return $this->getCurrentVisitor()->hasPermission($group,$permission);
	}

	public function getUserId()
	{
		return (int)$this->getCurrentVisitor()->toArray()['user_id'];
	}
}
