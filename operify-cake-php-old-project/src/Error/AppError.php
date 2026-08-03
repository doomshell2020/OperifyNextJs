<?php
namespace App\Error;

use Cake\Error\BaseErrorHandler;

class AppError extends BaseErrorHandler
{
    public function _displayError($error, $debug)
    {
        echo 'There has been an error!';
    }

    public function _displayException($exception)
    {
        echo $exception;die;
    }
}
