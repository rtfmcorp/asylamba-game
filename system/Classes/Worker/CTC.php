<?php

namespace Asylamba\Classes\Worker;

use Asylamba\Classes\Library\Bug;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Session\SessionWrapper;

class CTC
{
    /** @var boolean **/
    private $running = false;
    
    private $currentDate;
    /** @var array **/
    public $events  = array();
    /** @var SessionWrapper **/
    protected $sessionWrapper;
    /** @var string **/
    protected $logDirectory;
    
    /**
     * @param SessionWrapper $session
     * @param string $logDirectory
     */
    public function __construct(SessionWrapper $session, $logDirectory)
    {
        $this->sessionWrapper = $session;
        $this->logDirectory = $logDirectory;
    }

    public function createContext($creator = null)
    {
        $this->create++;

        if (!$this->running) {
            $this->running = true;
            $this->context++;
            $this->creator = $creator;

            return true;
        } else {
            return false;
        }
    }

    public function applyContext($token)
    {
        $this->apply++;

        if ($token) {
            if (count($this->events) > 0) {
                $beforeUsort = count($this->events);

                $this->events = $this->insertion($this->events);

                $afterUsort = count($this->events);

                $logt  = '> ' . date('H:i:s') . ', start to apply context';
                $logt .= ($this->sessionWrapper->exist('playerId')) ? ' [Player ' . $this->sessionWrapper->get('playerId') . ']' : null;
                $logt .= "\n";

                //$logt .= '> Page : ' . $_SERVER['REQUEST_URI'] . "\n";

                $j = 0;
                foreach ($this->events as $k => $event) {
                    $j++;
                    $this->currentDate = $event['date'];
                    call_user_func_array(array($event['object'], $event['method']), $event['args']);

                    $logt .= '> [' . $event['date'] . '] ' . $event['model_class'] . '(' . $event['model_id'] . ')::' . $event['method'] . "\n";
                }

                $logt .= '> ';
                $logt .= 'create/apply : ' . $this->create . '/' . $this->apply . ' | ';
                $logt .= 'add/iter : ' . $this->add . '/' . $j . ' | ';
                $logt .= 'before/after-usort : ' . $beforeUsort . '/' . $afterUsort . ' | ';
                $logt .= 'context : ' . $this->context . ' | ';
                $logt .= 'creator : ' . $this->creator . "\n";

                $logt .= "> \n";

                $logt .= "\n";

                $path  = $this->logDirectory . '/ctc/' . date('Y') . '-' . date('m') . '-' . date('d') . '.log';
                Bug::writeLog($path, $logt);
            }

            //$this->add = 0;
            $this->running = false;
            $this->events  = array();
        }
    }

    /**
     * @param string $date
     * @param object $object
     * @param string $method
     * @param object $model
     * @param array $args
     * @return boolean
     * @throws \Exception
     */
    public function add($date, $object, $method, $model = null, $args = array())
    {
        if (!$this->running) {
            throw new \Exception('CTC isn\'t running actually', 1);
        } else {
            $this->add++;
            $event = array(
                'timest' => strtotime($date),
                'date'     => $date,
                'object' => $object,
                'model_class'  => ($model !== null) ? get_class($model) : '',
                'model_id' => ($model !== null) ? $model->getId() : '',
                'method' => $method,
                'args'   => $args,
                'random' => rand()
            );
            $this->events[] = $event;
            return true;
        }
    }

    public function now()
    {
        if ($this->running) {
            if ($this->currentDate == null) {
                return Utils::now();
            } else {
                return $this->currentDate;
            }
        } else {
            return Utils::now();
        }
    }

    public function size()
    {
        return count($this->events);
    }

    public function insertion(array $array)
    {
        $length = count($array);

        for ($i = 1; $i < $length; $i++) {
            $element = $array[$i];
            $j = $i;

            while ($j > 0 && $array[$j - 1]['timest'] > $element['timest']) {
                $array[$j] = $array[$j - 1];
                $j = $j - 1;
            }

            $array[$j] = $element;
        }

        return $array;
    }

    private $add     = 0;
    private $create  = 0;
    private $apply   = 0;
    private $context = 0;
    private $creator = null;

    public function get()
    {
        return $this->events;
    }
}
