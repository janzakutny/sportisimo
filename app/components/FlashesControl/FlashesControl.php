<?php

declare(strict_types=1);

namespace App;

use Nette\Application\UI\Control;

/**
 * @property-read Control $parent
 */
class FlashesControl extends Control
{
    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/flashesControl.latte');
        $template->flashes = $this->parent->template->flashes;
        $template->render();
    }
}
