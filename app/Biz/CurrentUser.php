<?php

namespace app\Biz;

class CurrentUser
{
    protected $data;
    public function __construct($user = array())
    {
        $user = array_merge(array(
            'id' => 0,
            'currentIp' => '127.0.0.1',
            'nickname' => '游客',
            'email' => 'test.edusoho.com',
            'roles' => array(),
            'locked' => false,
            'password' => '',
        ), empty($user)?[]:$user);

        $this->data = $user;
    }

    public function serialize()
    {
        return serialize($this->data);
    }

    public function unserialize($serialized)
    {
        $this->data = unserialize($serialized);
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        throw new \RuntimeException("{$name} is not exist in CurrentUser.");
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->__set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->__unset($offset);
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getId()
    {
        return $this->id;
    }


    public function isLogin()
    {
        return empty($this->id) ? false : true;
    }

    public function isAdmin()
    {
        return false;
    }

    public function fromArray(array $user)
    {
        $this->data = $user;

        return $this;
    }

    public function toArray()
    {
        return $this->data;
    }

}
