<?php

/*
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace App\Components;

use App\Components\BaseFormFactory;
use Nette;

class SignInForm extends Nette\Application\UI\Control
{

    /** @var BaseFormFactory */
    private $baseFormFactory;

    /**
     * SignInForm constructor.
     * @param BaseFormFactory $baseFormFactory
     */
    public function __construct(BaseFormFactory $baseFormFactory)
    {
        parent::__construct();
        $this->baseFormFactory = $baseFormFactory;
    }

    public function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        $form->addText('username')
                ->setType('email')
                ->setRequired('Please enter email.')
                ->addRule(Nette\Application\UI\Form::EMAIL, 'Please enter a vaild email');

        $form->addPassword('password')
                ->setRequired('Please enter password.');

        $form->addCheckbox('remember');

        $form->addSubmit('sign');

        return $form;
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/SignInForm.latte');
        $template->render();
    }

}
