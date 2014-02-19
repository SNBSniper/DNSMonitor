<?php

class SlaveServerController extends BaseController {

	function __construct() {
		// $this->beforeFilter('slave');
	}

    /**
     * Changes the refresh rate to the database and starts the crontab with new
     * the updated crontab file.
     * @param integer $rate new refresh rate. Replicates the database change from master
     */
	public function ChangeRefreshRate($rate = 15)
	{
        // Replicate database change
		$server = Server::current();
        $server->refresh_rate = $rate;
        $server->save();
        Log::info("Updating refresh rate to $rate min on server ". $server->ip);

        // Write new refresh rate to crontab file and init crontab
        file_put_contents(public_path().'/crontab.txt', "*/$rate * * * * ".Config::get('app.php_path')." ".base_path()."/artisan monitor > /dev/null 2>&1\n");

        Log::info("Crontab file updated to {$server->refresh_rate} min on server {$server->ip}");

        return Response::json(array(
            'error' => false,
            'msg' => 'crontab updated'
        ))->setCallback(Input::get('callback'));;
	}

    /**
     * Stops the crontab execution
     * 
     * @return JSON Response Json response
     */
    public function stopCron()
    {
        exec('crontab -r');

        Log::info('Crontab stopped on server '. Server::current()->ip);

        return Response::json(array(
            'error' => false,
            'msg'   => 'Cron stopped successfully'
        ))->setCallback(Input::get('callback'));;
    }

    /**
     * Starts the crontab execution
     * 
     * @return JSON Response Json response
     */
    public function startCron()
    {
        exec('crontab '.public_path().'/crontab.txt');
        Log::info('Crontab started on server '. Server::current()->ip);

        return Response::json(array(
            'error' => false,
            'msg'   => 'Cron started successfully'
        ))->setCallback(Input::get('callback'));;
    }

    /**
     * Yields the crontab status
     * 
     * @return JSON Response Json response
     */
    public function getCronStatus()
    {
        $status = exec('crontab -l') == "" ? 'stopped' : 'running';

        return Response::json(array(
            'error'  => false,
            'msg'    => $status == 'stopped' ? 'Cron is stopped' : 'Cron is running',
            'status' => $status
        ))->setCallback(Input::get('callback'));;
    }

}
