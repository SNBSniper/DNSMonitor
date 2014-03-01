<?php

class MonitorTest extends TestCase {

	public function testOnlySlavesCanMonitor()
	{
        $master = new MasterServer;
        $slave  = new SlaveServer;
        $dns    = new DnsServer;

        $this->assertNotEquals(Response::json(array('error' => true, 'msg' => 'The current server is not a slave')), $slave->monitor());
        $this->assertEquals(Response::json(array('error' => false, 'msg' => 'Monitor finished')), $slave->monitor());
	}

}