<?php
	class Sys{
		static protected $_connection;
	
		/**
		 * Constructs environment.
		 */
		public function __construct(){
			
		}

		/**
		 * Kill myself, wait... Disconnect from database first :]
		 */
		public function __destruct() {
			$this->DisconnectDB();
		}

		/**
		 * Connects to database if not connected yet
		 * @return object connection
		 */
		protected function ConnectDB(){
			if (!isset(self::$_connection)) {
				print 'Connect DB: Ok.<br />';
				self::$_connection = true; //self::$_connection = new PDO(...);
			}
			return self::$_connection;
		}
		
		/**
		 * Disconnecting database
		 */
		protected function DisconnectDB(){
			self::$_connection=null;
		}

		/**
		 * Inserts each element of array to database
		 * @param type $table table name
		 * @param array $data simple array data in key=>value order
		 */
		protected function InsertDB($table, array $data){
			Sys::ConnectDB();
			if (self::$_connection){
				$sql='INSERT INTO ' . $table . ' (' . implode(', ', array_keys($data)) . ') VALUES (' . substr(str_repeat('?, ', sizeof($data)), 0, -2) . ')';

				//Display query
				print 'Inserting to "' . $table . '" DB table:<br /><br />';
				print $sql;
				print '<br /><br />Parameters:<pre>';
				print_r($data);
				print '</pre>';
			}
		}
		
	}