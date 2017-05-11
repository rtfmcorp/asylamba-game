<?php

namespace Asylamba\Classes\Process;

use Asylamba\Classes\Process\Process;

class ProcessGateway
{
    /**
     * Send JSON data to a process
     * 
     * @param Process $process
     * @param array $data
     */
    public function writeTo(Process $process, $data)
    {
        fwrite($process->getOutput(), json_encode($data) . "\n");
    }
	
	/**
	 * @param array $data
	 */
	public function writeToMaster($data)
	{
		\Asylamba\Classes\Daemon\Server::debug(json_encode($data));
        fwrite(STDOUT, json_encode($data) . "\n");
	}
    
    /**
     * Get data from a process pipe
     * 
     * @param Process $process
     * @return array
     */
    public function read(Process $process)
    {
        $output = $process->getOutput();
        rewind($output);
        return json_decode(fgets($output), true);
    }
}