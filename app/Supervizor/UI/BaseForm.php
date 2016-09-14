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

namespace Supervizor\UI;

use Minetro\Forms\reCAPTCHA\IReCaptchaValidatorFactory;
use Minetro\Forms\reCAPTCHA\ReCaptchaField;
use Minetro\Forms\reCAPTCHA\ReCaptchaHolder;
use Nette\Application\UI\Form;
use Nette\Forms\IFormRenderer;
use Nette\Localization\ITranslator;

/**
 * Description of BaseForm
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class BaseForm extends BaseControl implements BaseFormFactory
{

    /** @var IFormRenderer */
    private $renderer;

    /** @var ITranslator */
    private $translator;

    /** @var IReCaptchaValidatorFactory */
    private $validatorFactory;

    public function __construct(IFormRenderer $renderer = null, ITranslator $translator = null, IReCaptchaValidatorFactory $validatorFactory = null)
    {
        parent::__construct();

        $this->renderer = $renderer;
        $this->translator = $translator;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @param  string  $name   Field name
     * @param  string  $label  Html label
     * @return ReCaptchaField
     */
    public function addReCaptcha($name = 'recaptcha', $label = NULL)
    {
        $recaptcha = $this[$name] = new ReCaptchaField(ReCaptchaHolder::getSiteKey(), $label);

        $validator = $this->validatorFactory->create();
        $recaptcha->addRule([$validator, 'validateControl'], 'You`re bot!');

        return $recaptcha;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();
        $form->setRenderer($this->renderer);
        $form->setTranslator($this->translator);
        $form->addProtection('Please resend form.');

        return $form;
    }

}
