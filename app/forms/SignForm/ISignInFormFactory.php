<?php

declare(strict_types=1);

namespace App\Forms;

interface ISignInFormFactory
{
    /** @return SignInForm */
    function create();
}
