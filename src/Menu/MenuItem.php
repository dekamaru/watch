<?php

namespace App\Menu;

class MenuItem
{
    /**
     * @var MenuItem[]
     */
    private $childrens;
    private $icon;
    private $name;
    private $url;
    private $active = false;

    public function __construct($name, $url, $icon, $childrens = [])
    {
        $this->childrens = $childrens;
        $this->icon = $icon;
        $this->name = $name;
        $this->url = $url;
    }

    public function getChildrens()
    {
        return $this->childrens;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setActive()
    {
        $this->active = true;
    }

    public function isActive()
    {
        return $this->active;
    }
}