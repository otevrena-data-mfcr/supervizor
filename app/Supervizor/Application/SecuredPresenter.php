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

namespace Supervizor\Application;

use Exception;
use Kdyby\Doctrine\EntityManager;
use Supervizor\Security\User;
use thomaswelton\GravatarLib\Gravatar;

/**
 * Description of SecuredPresenter
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
abstract class SecuredPresenter extends Presenter
{

    /** @var EntityManager @inject */
    public $entityManager;

    /** @var bool */
    private $assigned = false;

    /**
     * Checks authorization.
     * @return void
     */
    public function checkRequirements($element)
    {
        parent::checkRequirements($element);

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:In', array('backlink' => $this->storeRequest()));
        } elseif ($this->getUser()->isLoggedIn()) {
            $this->assignUserInfo();
        }
    }

    /**
     * @throws Exception
     */
    private function assignUserInfo()
    {
        if ($this->assigned) {
            return;
        }

        /** @var User $user */
        $user = $this->getUser()->getIdentity();
        $this->entityManager->flush();

        $this->template->userInfo = $user;

        $gravatar = new Gravatar;
        $gravatar->setDefaultImage('retro')
            ->setAvatarSize(64)
            ->setMaxRating('pg')
            ->enableSecureImages();

        $this->template->avatar = $gravatar->buildGravatarURL($user->getEmail());

        $this->assigned = true;
    }

}
