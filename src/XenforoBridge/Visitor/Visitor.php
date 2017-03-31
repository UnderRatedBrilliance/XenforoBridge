<?php namespace Urb\XenforoBridge\Visitor;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Urb\XenforoBridge\Contracts\VisitorInterface;
use XenForo_Visitor;


class Visitor implements VisitorInterface,Authenticatable
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

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getUserId();
    }

    public function getAuthPassword()
    {
        throw new AuthenticationException('get Auth Password not implemented by '.get_class($this));
    }

    public function getRememberToken()
    {
        throw new AuthenticationException('Remember Tokens not implemented by '.get_class($this));
    }

    public function setRememberToken($value)
    {
        throw new AuthenticationException('Remember Tokens not implemented by '.get_class($this));
    }

    public function getRememberTokenName()
    {
        throw new AuthenticationException('Remember Tokens not implemented by '.get_class($this));
    }

    public function getName()
    {
        $user = $this->getCurrentVisitor()->toArray();
        if(isset($user['username']))
        {
            return $user['username'];
        }
        return null;
    }

    public function __get($key)
    {
        if(!$key)
        {
            return;
        }

        if(method_exists(self::class, 'get'.\Illuminate\Support\Str::studly($key)))
        {
            return $this->{'get'.\Illuminate\Support\Str::studly($key)}();
        }

        $user = $this->getCurrentVisitor()->toArray();

        if(isset($user[$key]))
        {
            return $user[$key];
        }
    }

}
