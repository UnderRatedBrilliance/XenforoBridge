<?php
/**
 * Copyright (C) Stellaron, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by George Barba <george@agenaastro.com>, September 2017
 */

namespace Urb\XenforoBridge;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;


class XenforoGuard implements Guard
{
    protected $user;
    protected $xenforo;


    public function __construct(XenforoBridge $xenforo)
    {
        $this->xenforo  = $xenforo;
    }

    public function check()
    {
        return ! is_null($this->user());
    }

    public function guest()
    {
        return ! $this->check();
    }

    public function user()
    {
        if(! is_null($this->user))
        {
            return $this->user;
        }
        $user = null;

        if($this->xenforo->isLoggedIn())
        {
            $user = $this->xenforo->getVisitorObject();
        }
        return $this->user = $user; /** @todo Implement Authenticable */
    }

    public function id()
    {
        if($this->user())
        {
            return $this->user()->getUserId();
        }
    }

    public function validate(array $credentials = [])
    {
        // TODO: Implement validate() method.
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;

        return $this;
    }


}