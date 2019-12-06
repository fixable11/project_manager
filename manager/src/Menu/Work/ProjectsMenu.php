<?php

declare(strict_types=1);

namespace App\Menu\Work;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ProjectsMenu.
 */
class ProjectsMenu
{
    /**
     * @var FactoryInterface $factory Form factory.
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface $auth Auth checker.
     */
    private $auth;

    /**
     * ProjectsMenu constructor.
     *
     * @param FactoryInterface              $factory Factory.
     * @param AuthorizationCheckerInterface $auth    Auth checker.
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $auth)
    {
        $this->factory = $factory;
        $this->auth = $auth;
    }

    /**
     * @return ItemInterface
     */
    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes(['class' => 'nav nav-tabs mb-4']);

        $menu
            ->addChild('Projects', ['route' => 'work.projects'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link');

        if ($this->auth->isGranted('ROLE_WORK_MANAGE_PROJECTS')) {
            $menu
                ->addChild('Roles', ['route' => 'work.projects.roles'])
                ->setExtra('routes', [
                    ['route' => 'work.projects.roles'],
                    ['pattern' => '/^work.projects.roles\..+/']
                ])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');
        }

        return $menu;
    }
}