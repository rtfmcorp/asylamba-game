<?php

namespace Asylamba\Classes\Logger;

class FileLogger extends AbstractLogger
{
    /** @var string **/
    protected $directory;
    /** @var int **/
    protected $rotation;
    
    /**
     * @param string $directory
     * @param int $rotation
     */
    public function __construct($directory, $rotation = null)
    {
        $this->directory = $directory;
        $this->rotation = $rotation;
    }
    
    /**
     * {@inheritdoc}
     */
    public function log($message, $level = self::LOG_LEVEL_DEBUG, $type = self::LOG_TYPE_PHP)
    {
        $datetime = new \DateTime();
        file_put_contents(
            "{$this->directory}/$type/{$datetime->format('Y-m-d')}.log",
            $this->formatMessage($message, $level, $datetime),
            FILE_APPEND
        );
    }
}
