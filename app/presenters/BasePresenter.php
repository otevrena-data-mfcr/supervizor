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

use \App\Model\Entities\User;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter
{

    /**
     * @return \Nette\Security\IIdentity|User|NULL
     */
    public function getUserEntity()
    {
        return $this->getUser()->getIdentity();
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
