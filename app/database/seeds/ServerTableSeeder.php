<?php 
class ServerTableSeeder extends Seeder {

	public function run()
	{
		DB::table('servers')->delete();
		
		Server::create(array(
			'provider'=>'jpeg',
			'ip'=>'190.105.36.9',
			'type'=>'slave',
			'port'=>'80',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		Server::create(array(
			'provider'=>'danielftapiar',
			'ip'=>'190.161.79.11',
			'type'=>'master',
			'port'=>'80',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		Server::create(array(
			'provider'=>'castor.entelchile.net',
			'ip'=>'200.72.1.253',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));
		Server::create(array(
			'provider'=>'polux.entelchile.net',
			'ip'=>'200.72.1.254',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));
		Server::create(array(
			'provider'=>'resolver2.entelchile.net',
			'ip'=>'200.72.1.11',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));
		Server::create(array(
			'provider'=>'resolver.entelchile.net',
			'ip'=>'200.72.1.5',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		Server::create(array(
			'provider'=>'ns00.vtr.net',
			'ip'=>'200.104.255.130',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		Server::create(array(
			'provider'=>'ns01.vtr.net',
			'ip'=>'200.74.121.200',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));
		
		Server::create(array(
			'provider'=>'Movistar',
			'ip'=>'200.28.216.1',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		Server::create(array(
			'provider'=>'Movistar',
			'ip'=>'200.28.216.2',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		Server::create(array(
			'provider'=>'Movistar',
			'ip'=>'200.28.4.129',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		Server::create(array(
			'provider'=>'Movistar',
			'ip'=>'200.28.4.130',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		
		Server::create(array(
			'provider'=>'resolver.gtdinternet.com',
			'ip'=>'200.75.0.4',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		Server::create(array(
			'provider'=>'resolver2.gtdinternet.com',
			'ip'=>'200.75.0.5',
			'type'=>'dns',
			'port'=>'53',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));
		// Server::create(array(
		// 	'provider'=>'Level31',
		// 	'ip'=>'209.244.0.3',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Level31',
		// 	'ip'=>'209.244.0.4',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Google2',
		// 	'ip'=>'8.8.8.8',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Google2',
		// 	'ip'=>'8.8.4.4',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Securly3',
		// 	'ip'=>'184.169.143.224',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Securly3',
		// 	'ip'=>'184.169.161.155',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Comodo Secure DNS',
		// 	'ip'=>'8.26.56.26',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Comodo Secure DNS',
		// 	'ip'=>'8.20.247.20',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'OpenDNS Home4',
		// 	'ip'=>'208.67.222.222',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'OpenDNS Home4',
		// 	'ip'=>'208.67.220.220',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'DNS Advantage',
		// 	'ip'=>'156.154.70.1',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'DNS Advantage',
		// 	'ip'=>'156.154.71.1',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Norton ConnectSafe5',
		// 	'ip'=>'198.153.192.40',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Norton ConnectSafe5',
		// 	'ip'=>'198.153.194.40',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'GreenTeamDNS6',
		// 	'ip'=>'81.218.119.11',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'GreenTeamDNS6',
		// 	'ip'=>'209.88.198.133',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'SafeDNS7',
		// 	'ip'=>'195.46.39.39',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'SafeDNS7',
		// 	'ip'=>'195.46.39.40',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'OpenNIC8',
		// 	'ip'=>'216.87.84.211',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'OpenNIC8',
		// 	'ip'=>'23.90.4.6',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Public-Root9',
		// 	'ip'=>'199.5.157.131',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Public-Root9',
		// 	'ip'=>'208.71.35.137',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'SmartViper',
		// 	'ip'=>'208.76.50.50',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'SmartViper',
		// 	'ip'=>'208.76.51.51',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Dyn',
		// 	'ip'=>'216.146.35.35',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Dyn',
		// 	'ip'=>'216.146.36.36',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'censurfridns.dk10',
		// 	'ip'=>'89.233.43.71',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'censurfridns.dk10',
		// 	'ip'=>'89.104.194.142',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		// Server::create(array(
		// 	'provider'=>'Hurricane Electric11',
		// 	'ip'=>'74.82.42.42',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		

		// Server::create(array(
		// 	'provider'=>'puntCAT12',
		// 	'ip'=>'109.69.8.51',
		// 	'type'=>'dns',
		// 	'port'=>'53',
		// 	'created_at' => new DateTime,
		// 	'updated_at' => new DateTime
		// ));

		

		

	}
}

