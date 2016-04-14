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

use App\Model\Entities\User;
use App\Model\Repository\ImportRepository;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
    /** @var ImportRepository @inject */
    public $importRepository;

    /** @var string @persistent */
    public $importGroupSlug = null;

    /** @var string @persistent */
    public $importSlug = null;



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

    public function createTemplate()
    {
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

    /**
     * @return \WebLoader\Nette\CssLoader
     * @throws \WebLoader\InvalidArgumentException
     */
    public function createComponentCss()
    {
        $wwwDir = $this->getContext()->parameters['wwwDir'];
        $files = new \WebLoader\FileCollection($wwwDir . '/bower_components');
        $files->addRemoteFile('http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600&subset=latin,latin-ext');

        $files->addFiles(array(
            'jquery-ui/themes/smoothness/jquery-ui.min.css',
            'bootstrap/dist/css/bootstrap.min.css',
            'bootstrap/dist/css/bootstrap-theme.min.css',
            $wwwDir . '/js/jquery/jQRangeSlider/jQAllRangeSliders-classic-min.css',
            'fancybox/source/jquery.fancybox.css',
            $wwwDir . '/scss/style-default.scss'
        ));

        $compiler = \WebLoader\Compiler::createCssCompiler($files, $wwwDir . '/webtemp');

        $compiler->addFileFilter(new \WebLoader\Filter\ScssFilter());

        $root = $wwwDir . '/bower_components';
        $base = $this->template->basePath . '/bower_components';
        $compiler->addFileFilter(new \WebLoader\Filter\CssUrlsFilter($root, $base));

        return new \WebLoader\Nette\CssLoader($compiler, $this->template->basePath . '/webtemp');
    }

    /**
     * @return \WebLoader\Nette\JavaScriptLoader
     */
    public function createComponentJs()
    {
        $wwwDir = $this->getContext()->parameters['wwwDir'];
        $files = new \WebLoader\FileCollection($wwwDir . '/bower_components');

        $files->addRemoteFile('https://www.google.com/recaptcha/api.js');


        $files->addFiles(array(
            'jquery/jquery.min.js',
            'jquery-ui/jquery-ui.min.js',
            'fancybox/source/jquery.fancybox.pack.js',
            'bootstrap/dist/js/bootstrap.min.js',
            'history.js/scripts/bundled/html4+html5/native.history.js',
            $wwwDir . '/js/jquery/jQRangeSlider/jQAllRangeSliders-min.js',
            'raphael/raphael-min.js',
            $wwwDir . '/js/raphael-style.js',
            $wwwDir . '/js/global.js'
        ));

        $compiler = \WebLoader\Compiler::createJsCompiler($files, $wwwDir . '/webtemp');
        return new \WebLoader\Nette\JavaScriptLoader($compiler, $this->template->basePath . '/webtemp');
    }

}
