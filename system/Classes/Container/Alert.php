<?php

namespace Asylamba\Classes\Container;

class Alert
{
    /** @var array **/
    private $alerts = array();

    /**
     * @param string $message
     * @param string $type
     * @return int
     */
    public function add($message, $type = ALERT_STD_INFO)
    {
        $this->alerts[] = array($message, $type);
        return $this->size() - 1;
    }

    public function clear()
    {
        $this->alerts = array();
    }

    /**
     * @return int
     */
    public function size()
    {
        return count($this->alerts);
    }

    /**
     * @param int $position
     * @return array|boolean
     */
    public function get($position)
    {
        if (isset($this->alerts[$position])) {
            return $this->alerts[$position];
        }
        return false;
    }

    /**
     * @param string $tag
     * @param string $type
     * @return string
     */
    public function getAlerts($tag, $type = ALERT_DEFAULT)
    {
        $format = '';
        foreach ($this->alerts as $k) {
            if ($type != ALERT_DEFAULT and $type = $k[1]) {
                $format .= '<' . $tag . ' class="alert_' . $k[1] . '">' . $k[0] . '</' . $tag . '>';
            }
        }
        return $format;
    }

    /**
     * @param string $path
     * @param boolean $date
     * @param array $supp
     */
    public function logAlerts($path, $date = true, $supp = array())
    {
        # TODO
    }

    public function readUrl()
    {
        if (isset($_GET['say'])) {
            $this->alerts[] = array($_GET['say'], ALERT_URL_INFO);
        }
    }
}
