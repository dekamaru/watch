<?php

namespace App\Menu;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class MenuBuilder
{

    private $authManager;
    private $roles;

    public function __construct(AuthorizationCheckerInterface $authManager, TokenStorageInterface $tokenStorage)
    {
        $this->authManager = $authManager;

        if ($tokenStorage->getToken() !== null && $tokenStorage->getToken()->getUser() !== 'anon.')
        {
            $this->roles = $tokenStorage->getToken()->getUser()->getRoles();
        }
    }

    public function generateAdminMenu(Menu &$menu)
    {
        // todo: ?
    }

    public function generateMainMenu(Menu &$menu)
    {

        $mainGroup = new MenuGroup();

        $mainGroup->add(new MenuItem('Профиль', '/', 'user'));
        $mainGroup->add(new MenuItem('Фермы', '/rig/list', 'cog'));

        $menu->add($mainGroup);
    }

    public function generate()
    {
        $menu = new Menu();

        if ($this->authManager->isGranted('ROLE_ADMIN'))
        {
            $this->generateAdminMenu($menu);
        }

        $this->generateMainMenu($menu);

        return $menu->generate();
    }
}