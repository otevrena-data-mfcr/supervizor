<?php

/*
 * Copyright (C) 2013 Adam Schubert <adam.schubert@winternet.cz>.
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

/**
 * Description of SignPresenter
 *
 * @author Adam Schubert <adam.schubert@winternet.cz>
 */
use Nette\Application\UI\Form;
use Supervizor\Auth\Controls\SignInFormFactory;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends Presenter
{

    /** @persistent */
    public $backlink = '';

    /** @var SignInFormFactory @inject */
    public $signInFormFactory;

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm()
    {
        $signInControl = $this->signInFormFactory->create();
        $signInControl['form']->onSuccess[] = $this->signInFormSucceeded;

        return $signInControl;
    }

    /**
     * @param Form $form
     */
    public function signInFormSucceeded(Form $form)
    {
        $values = $form->getValues();
        if ($values->remember)
        {
            $this->getUser()->setExpiration('14 days', FALSE);
        }
        else
        {
            $this->getUser()->setExpiration('50 minutes', TRUE);
        }

        try
        {
            $this->getUser()->login($values->username, $values->password, true);

            $this->restoreRequest($this->backlink);
            $this->redirect('Homepage:');
        }
        catch (\Nette\Security\AuthenticationException $e)
        {
            $form->addError($e->getMessage());
        }
    }

    /**
     *
     */
    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('You has been logged out.', 'alert-success');
        $this->redirect('in');
    }

}
