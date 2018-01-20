<?php

namespace App\Menu;

class MenuGroup
{
    private $name;

    /**
     * @var MenuItem[]
     */
    private $items;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function add(MenuItem $item)
    {
        $this->items[] = $item;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getItems()
    {
        return $this->items;
    }
}