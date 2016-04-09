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

use Nette\Diagnostics\Debugger;

/**
 * Error presenter.
 */
class ErrorPresenter extends \Nette\Application\UI\Presenter
{

    public function startup()
    {
        parent::startup();
    }

    /**
     * @param  Exception
     * @return void
     */
    public function renderDefault($exception)
    {
        if ($exception instanceof \Nette\Application\BadRequestException)
        {
            $code = $exception->getCode();
        }
        else
        {
            $code = 500;
        }

        if ($this->isAjax())
        { // AJAX request? Just note this error in payload.
            $this->payload->error = TRUE;
            $this->terminate();
        }
        else
        {
            if (in_array($code, array(403, 404, 405, 410)))
            {
                Debugger::log("HTTP code $code: {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}", 'read');
            }
            else
            {
                Debugger::log($exception, Debugger::ERROR);
            }
            $this->setView($code);
        }
    }

}
