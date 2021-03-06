<?php

declare(strict_types=1);

namespace Orchid\Bulldozer\Http\Composers;

use Orchid\Platform\ItemMenu;
use Orchid\Platform\Dashboard;

/**
 * Class SystemMenuComposer.
 */
class SystemMenuComposer
{
    /**
     * @var Dashboard
     */
    private $dashboard;

    /**
     * MenuComposer constructor.
     *
     * @param Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * Registering the main menu items.
     */
    public function compose()
    {
        $this->dashboard->menu
            ->add('Tools',
                ItemMenu::setLabel(__('Model builder'))
                    ->setIcon('icon-database')
                    ->setRoute(route('platform.bulldozer.index'))
                    ->setPermission('platform.bulldozer')
                    ->setActive('platform.bulldozer.*')
                    ->setGroupName(__('Add your models, customize your columns, and even set up relationships.'))
            );
    }
}
