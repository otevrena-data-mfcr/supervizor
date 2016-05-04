<?php

namespace Supervizor\UI;

use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter
{

    /**
     * Formats layout template file names.
     * @return array
     */
    public function formatLayoutTemplateFiles()
    {
        $name = $this->getName();
        $presenter = substr($name, strrpos(':' . $name, ':'));
        $layout = $this->layout ? $this->layout : 'layout';
        $dir = dirname($this->getReflection()->getFileName());
        $list = array(
            "$dir/$presenter/@$layout.latte",
        );
        do {
            $list[] = "$dir/@$layout.latte";
            $dir = dirname($dir);
        } while ($dir && ($name = substr($name, 0, strrpos($name, ':'))));
        return $list;
    }



    /**
     * Formats view template file names.
     * @return array
     */
    public function formatTemplateFiles()
    {
        $name = $this->getName();
        $presenter = substr($name, strrpos(':' . $name, ':'));
        $dir = dirname($this->getReflection()->getFileName());
        return array(
            "$dir/$presenter/$this->view.latte",
        );
    }

}
