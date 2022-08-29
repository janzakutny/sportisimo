<?php

declare(strict_types=1);

namespace App\Model\Utils;

use Nette;
use Nette\Forms;
use Nette\Forms\Control;


class FormValidators
{
    use Nette\SmartObject;

    /** @var Validators */
    private $validators;

    /** @var int */
    private $exceptUserId;


    public function __construct(Validators $validators)
    {
        $this->validators = $validators;
    }


    /**
     * Set user id which will be excluded
     * @param int $id
     */
    public function setExceptUserId($id)
    {
        $this->exceptUserId = $id;
    }


    /**
     * @param Control $control
     * @return bool
     */
    public function isUserEmailUnique(Forms\Control $control)
    {
        return $this->validators->isUserEmailUnique('email', $control->getValue(), $this->exceptUserId);
    }


    /**
     * @param Control $control
     * @return bool
     */
    public function isLiftIdUnique(Forms\Control $control)
    {
        return $this->validators->isLiftIdUnique('id', $control->getValue(), $this->exceptLiftId);
    }
}
