<?php

namespace Asylamba\Classes\Worker;

use Asylamba\Classes\Library\Bug;

abstract class Manager {
    // SESSION MANAGER CORE
    /** @var string **/
    protected $managerType = '_Main';
    protected $currentSession;
    /** @var array **/
    protected $sessions = array();

    public function __construct() {
        $this->newSession();
    }

    public function newSession($uMode = ASM_UMODE) {
        $this->statSessions++;
        $this->statChangeSessions++;

        if (count($this->sessions) == 0) {
            $session = new ManagerSession('_1', $this->managerType, ASM_UMODE);
        } else {
            $session = new ManagerSession('_' . (count($this->sessions) + 1), $this->managerType, $uMode);
        }

        $this->currentSession = $session;
        $this->sessions[] = $session;

        $this->objects[$session->getId()] = array();
        $this->origin[$session->getId()]  = array();

        return $session;
    }

    public function changeSession(ManagerSession $session) {
        $this->statChangeSessions++;

        if (in_array($session, $this->sessions) AND $session->getType() == $this->managerType) {
            $this->currentSession = $session;
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getCurrentSession() { return $this->currentSession; }
    public function getFirstSession()   { return $this->sessions[0]; }

    // OBJECT MANAGER CORE
    protected $objects = array();
    protected $origin  = array();

    public function get($i = 0) {
        if (isset($this->objects[$this->currentSession->getId()][$i])) {
            return $this->objects[$this->currentSession->getId()][$i];
        } else {
            return FALSE;
        }
    }

    public function getById($id) {
        foreach ($this->objects[$this->currentSession->getId()] AS $o) {
            if ($o->getId() == $id) {
                return $o;
            }
        }
        return FALSE;
    }

    public function getAll() {
        return $this->objects[$this->currentSession->getId()];
    }

    public function size() {
        return count($this->objects[$this->currentSession->getId()]);
    }

    protected function _ObjectExist($object) {
        foreach ($this->sessions AS $s) {
            foreach ($this->origin[$s->getId()] as $k => $o) {
                if ($o->getId() == $object->getId()) {
                    if ($s->getId() == $this->currentSession->getId()) {
                        return TRUE;
                    } else {
                        return $this->objects[$s->getId()][$k];
                    }
                }
            }
        }
        return FALSE;
    }

    protected function _Add($object) {
        $element = $this->_ObjectExist($object);

        if ($element === FALSE) {
            $this->statObject++;
            $this->statRealObject++;

            $this->objects[$this->currentSession->getId()][] = $object;
            $this->origin[$this->currentSession->getId()][]  = clone($object);
            return $object;
        } elseif ($element === TRUE) {
            $currentIdSession = $this->currentSession->getId();
            foreach ($this->origin[$currentIdSession] as $k => $o) {
                    if ($o == $object) {
                            return $this->objects[$currentIdSession][$k];
                    }
            }
            return FALSE;
        } else {
            $this->statObject++;
            $this->statReferenceObject++;

            $this->objects[$this->currentSession->getId()][] = $element;
            $this->origin[$this->currentSession->getId()][]  = $element;
            return $element;
        }
    }

    protected function _Remove($id) {
        foreach ($this->sessions as $session) {
            foreach ($this->objects[$session->getId()] AS $k => $o) {
                if ($o->getId() == $id) {
                    unset($this->objects[$session->getId()][$k]);
                    unset($this->origin[$session->getId()][$k]);
                }
            }
            $this->objects[$session->getId()] = array_values($this->objects[$session->getId()]);
            $this->origin[$session->getId()]  = array_values($this->origin[$session->getId()]);
        }
        return NULL;
    }

    public function _Save() {
        if (!empty($this->objects)) {
            return [];
        }
        $savingList = array();
        foreach ($this->sessions AS $s) {
            foreach ($this->objects[$s->getId()] AS $k => $o) {
                if ($this->objects[$s->getId()][$k] != $this->origin[$s->getId()][$k]) {
                    $savingList[] = $o;
                }
            }
        }
        $this->statSavingObject = count($savingList);
        return $savingList;
    }

    public function _EmptyCurrentSession() {
        $currentSessionId = $this->currentSession->getId();
        foreach ($this->objects[$currentSessionId] as $k => $o) {
            # code...
            unset($this->objects[$currentSessionId][$k]);
            unset($this->origin[$currentSessionId][$k]);
        }
    }

    // DEBUG & STATISTIC MANAGER CORE
    protected $statObject = 0;
    protected $statRealObject = 0;
    protected $statReferenceObject = 0;
    protected $statSavingObject = 0;

    protected $statSessions = 0;
    protected $statChangeSessions = 0;

    public function saveStat($path) {
        $ret  = 'objet ' . $this->managerType . '<br />';
        $ret .= 'object : ' . $this->statObject . '<br />';
        $ret .= 'real object : ' . $this->statRealObject . '<br />';
        $ret .= 'ref object : ' . $this->statReferenceObject . '<br />';
        $ret .= 'saving object : ' . $this->statSavingObject . '<br />';

        $ret .= 'sessions : ' . $this->statSessions . '<br />';
        $ret .= 'change sessions : ' . $this->statChangeSessions . '<br />';
        $ret .= '-----------------------------------------------------<br />';
        Bug::writeLog($path, $ret);
    }

    public function showStat() {
        $ret  = 'objet ' . $this->managerType . '<br />';
        $ret .= 'object : ' . $this->statObject . '<br />';
        $ret .= 'real object : ' . $this->statRealObject . '<br />';
        $ret .= 'ref object : ' . $this->statReferenceObject . '<br />';
        $ret .= 'saving object : ' . $this->statSavingObject . '<br />';

        $ret .= 'sessions : ' . $this->statSessions . '<br />';
        $ret .= 'change sessions : ' . $this->statChangeSessions . '<br />';
        $ret .= '-----------------------------------------------------<br />';
        echo $ret;
    }

    public function show($flag = 1) {
        if ($flag == 1) {
            var_dump($this->objects);
        } elseif ($flag == 2) {
            var_dump($this->origin);
        } else {
            var_dump($this->objects);
            var_dump($this->origin);
        }
        for ($i = 0; $i < 200; $i++) {
            echo '-';
        }
    }
}
