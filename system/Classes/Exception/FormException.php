<?php

namespace Asylamba\Classes\Exception;

class FormException extends \Exception
{
    /** @var string **/
    protected $redirect;
    
    /**
     * @param string $message
     * @param string $redirect
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message = "", $redirect = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->redirect = $redirect;
    }
    
    public function getRedirect()
    {
        return $this->redirect;
    }
}
