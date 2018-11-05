<?php

	class MySqlConnection
	{
		//get connection
		public static function getConnection()
		{
			//read config
			$data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/lineup/config/connection.json');
			$config = json_decode($data, true);
			//parameters
			if (isset($config['server'])) 
				$server = $config['server']; 
			else { 
				echo 'Configuration error : MySql Server name not found'; 
				die; 
			}
			if (isset($config['user'])) 
				$user = $config['user']; 
			else { 
				echo 'Configuration error : User name not found'; 
				die; 
			} 
			if (isset($config['password'])) 
				$password = $config['password'];  
			else { 
				echo 'Configuration error : Password not found'; 
				die; 
			}
			if (isset($config['database'])) 
				$database = $config['database']; 
			else { 
				echo 'Configuration error : Database name not found'; 
				die; 
			}
			//open connection
			$connection = mysqli_connect($server, $user, $password, $database);
			//error in connection
			if ($connection === false) { 
				echo 'Could not connect to MySql'; 
				die; 
			}
			//character set 
			$connection->set_charset('utf8');
			//return connection object
			return $connection;
		}
	}
?>
