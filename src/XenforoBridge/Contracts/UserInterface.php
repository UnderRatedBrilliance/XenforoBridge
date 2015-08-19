<?php namespace XenforoBridge\Contracts;

interface UserInterface
{

    public function getUserById($id);

    public function getUserByUsername($name);

    public function getUserByEmail($email);
}