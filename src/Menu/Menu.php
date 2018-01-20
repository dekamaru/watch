<?php

namespace App\Menu;

class Menu
{
    /**
     * @var MenuGroup[]
     */
    private $menu;

    const HEADER_FORMAT = '';
    const ITEM_FORMAT = '<li class="nav-item %s" data-toggle="tooltip" data-placement="right" title="%s"><a class="nav-link" href="%s"><i class="fa fa-fw fa-%s"></i> <span class="nav-link-text">%s</span></a></li>';
    const ITEM_MULTILEVEL_START_FORMAT = '<li class="treeview %s"><a href="javascript:;"><i class="fa fa-%s"></i><span>%s</span><span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span></a><ul class="treeview-menu">';
    const ITEM_MULTILEVEL_END_FORMAT = '</ul>';

    public function add(MenuGroup $group)
    {
        $this->menu[] = $group;
        return $this;
    }

    public function generate()
    {
        $result = null;
        $this->_setActiveItem();

        foreach($this->menu as $group)
        {
            $result .= sprintf(Menu::HEADER_FORMAT, strtoupper($group->getName()));
            foreach($group->getItems() as $menuItem)
            {
                $result .= $this->_generateMenuItem($menuItem);
            }
        }

        return $result;
    }

    private function _generateMenuItem(MenuItem $item, $content = null)
    {
        if (count($item->getChildrens()) == 0)
        {
            $content .= sprintf(
                Menu::ITEM_FORMAT,
                ($item->isActive()) ? 'active' : '',
                $item->getName(),
                $item->getUrl(),
                $item->getIcon(),
                $item->getName()
            );
        }
        else
        {
            $content .= sprintf(
                Menu::ITEM_MULTILEVEL_START_FORMAT,
                ($item->isActive()) ? 'active' : '',
                $item->getIcon(),
                $item->getName()
            );

            foreach($item->getChildrens() as $children)
            {
                $content .= $this->_generateMenuItem($children);
            }
            $content .= Menu::ITEM_MULTILEVEL_END_FORMAT;
        }

        return $content;
    }

    private function _setActiveItem()
    {
        // find active menu item
        foreach($this->menu as $group)
        {
            foreach($group->getItems() as $item)
            {
                if (count($item->getChildrens()) == 0)
                {
                    if ($this->_getRequestUri() == $item->getUrl())
                    {
                        $item->setActive();
                        return;
                    }
                }
                else
                {
                    $activeParents = $this->_findMultilevelActiveItem($item);

                    foreach($activeParents as $parent)
                    {
                        $parent->setActive();
                    }

                    if (count($activeParents) > 0)
                    {
                        $item->setActive();
                        return;
                    }

                }
            }
        }
    }

    /**
     * @param MenuItem $item
     * @param array $parents
     * @return MenuItem[] Active parents
     */
    private function _findMultilevelActiveItem(MenuItem $item, $parents = [])
    {
        foreach($item->getChildrens() as $children)
        {
            if (count($children->getChildrens()) > 0)
            {
                return $this->_findMultilevelActiveItem($children, $parents);
            }
            else
            {
                if ($this->_getRequestUri() == $children->getUrl())
                {
                    $children->setActive();
                    $parents[] = $item;
                }
            }
        }

        return $parents;
    }

    private function _getRequestUri()
    {
        $requestUri = explode('?', $_SERVER['REQUEST_URI'])[0];
        return urldecode($requestUri);
    }
}