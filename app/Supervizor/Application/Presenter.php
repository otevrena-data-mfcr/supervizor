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

use Supervizor\Security\User;
use Supervizor\Import\ImportRepository;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;

/**
 * Base presenter for all application presenters.
 */
abstract class Presenter extends \Supervizor\UI\BasePresenter
{
    /** @var ImportRepository @inject */
    public $importRepository;

    /** @var string @persistent */
    public $importGroupSlug = null;

    /** @var string @persistent */
    public $importSlug = null;

    /** @var \WebLoader\Nette\LoaderFactory @inject */
    public $webLoader;

    /**
     * @return \Nette\Security\IIdentity|User|NULL
     */
    public function getUserEntity()
    {
        return $this->getUser()->getIdentity();
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->importGroups = $this->importRepository->getImportGroups();

        if ($this->importGroupSlug) {
            $selectedImportGroup = $this->importRepository->getImportGroupBySlug($this->importGroupSlug);
        } else {
            $selectedImportGroup = $this->importRepository->getDefaultImportGroup();
        }

        $this->template->imports = $this->importRepository->getImportsByGroup($selectedImportGroup);

        if ($this->importSlug) {
            $selectedImport = $this->importRepository->getImportByGroupAndSlug($selectedImportGroup, $this->importSlug);
        } else {
            $selectedImport = $this->importRepository->getDefaultImport();
        }

        $this->importGroupSlug = $selectedImportGroup->getSlug();
        $this->importSlug = $selectedImport->getSlug();

        $this->template->selectedImport = $selectedImport;
        $this->template->selectedImportGroup = $selectedImportGroup;


    }
    
    /**
     * @return \Nette\Bridges\ApplicationLatte\Template
     */
    public function createTemplate()
    {
        /** @var \Nette\Bridges\ApplicationLatte\Template $template */
        $template = parent::createTemplate();

        $template->registerHelper('formatNumber', $this->formatNumber);

        $template->registerHelper('formatPrice', $this->formatPrice);

        return $template;
    }

    public function formatPrice($price)
    {
        return $this->formatNumber($price) . ' Kč'; //!FIXME Locales
    }

    public function formatNumber($number)
    {
        return number_format($number, 0, ',', ' '); //!FIXME Locales
    }

    /** @return CssLoader */
    protected function createComponentCss()
    {
        return $this->webLoader->createCssLoader('default');
    }



    /** @return JavaScriptLoader */
    protected function createComponentJs()
    {
        return $this->webLoader->createJavaScriptLoader('default');
    }

}
