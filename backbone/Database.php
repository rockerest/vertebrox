<?php
//	Database.php
//	Programmed by Tom Randolph
//	11 March 2010
//	Last Updated: 08 August 2010
//	====================================

//	v01112010	- added some documentation and prep() function
//	v08082010	- removed explicit direct binding
//	v05082010	- added direct binding of variables
//				- added reporting of prepared query strings
//	====================================

//	There are 8 public functions in this object:
//		public q
//		public qwv
//		public stat
//		public log
//		public qwviterations
//		public qiterations
//		public getq
//		public getqwv
//		public last
//		public reset
//		public prep
//	====================================

//	There are 13 variables in this object:
//		private $dsn
//		private $dbuser
//		private $dbpass
//		private $dbh
//		private $prepq
//		private $qsql
//		private $qiterate
//		private $prepqwv
//		private $qwvsql
//		private $qwvvals
//		private $qwviterate
//		protected $log
//		protected $status
//	====================================

//	$dsn is a variable with the database information so that the PDO
//		object can use it to connect.  It should have the form:
//		'DBTYPE:dbname=DATABASE;host=HOSTADDRESS'
//		
//		an example could be:
//		'mysql:dbname=mydatabase;host=123.45.67.890'
//	====================================

//	===========-- q() --================
//	q stands for "query", hopefully that helps give some context.
//	q() is called with either an SQL query or an empty string ('').
//
//	if q() is called with SQL, the SQL is prepared, executed, and the
//		database results are returned to where q() was called from, in
//		associative array form.
//		
//		Note that the array is 0-indexed _by records returned_.  You must know the name of
//		the column to reference _the field_.  I'm sure there are ways to
//		re-index the array (probably with array_values()) but that's
//		beyond the scope of this documentation
//		
//		Whenever q() is called with SQL, the private variable $qsql is
//		updated to contain the SQL.  Also, the private variable $qiterate
//		is reset to 0, and the private variable $prepq is updated to
//		contain the PDO prepared statement using the SQL.
//		
//	if q() is called withOUT SQL, the function attempts to use
//		the previously prepared PDO object without any changes.
//		
//	if any errors are encountered, FALSE is pushed onto the protected variable $status
//	A cryptic message about why the error is set is pushed onto $log
//		Otherwise, TRUE is pushed to $status and some message is pushed onto $log
//	====================================

//	===========-- qwv() --==============
//	qwv stands for "query with values", hopefully that helps give some context.
//	qwv() is called with either an SQL query AND an array of values
//  	or an empty string ('') AND an array of values.
//
//	Note that qwv() must be called with a 0-indexed array of values.
//		array('val1','val2','val3',...) works great for this.
//
//	if qwv() is called with SQL, the SQL is prepared, the values are fed in to match
//		the '?' marks, the statement is executed (in the same call), and the
//		database results are returned to where qwv() was called from, in
//		associative array form.
//		
//		Note that the array is 0-indexed _by records returned_.  You must know the name of
//		the column to reference _the field_.  I'm sure there are ways to
//		re-index the array (probably with array_values()) but that's
//		beyond the scope of this documentation
//
//	Note that the SQL passed into qwv() must have an equivalent number of '?' marks
//		to match the values passed in.  If they don't match, horrible things will happen.
//		
//		Whenever qwv() is called with SQL, the private variable $qwvsql is
//		updated to contain the SQL.  Also, the private variable $qwviterate
//		is reset to 0, and the private variable $prepqwv is updated to
//		contain the PDO prepared statement using the SQL.
//		
//	if qwv() is called withOUT SQL, the function attempts to use
//		the previously prepared SQL ($qwvsql) and inserts the new data passed in through
//		the $values parameter.
//		
//	if any errors are encountered, FALSE is pushed onto the protected variable $status
//	A cryptic message about why the error is set is pushed onto $log
//		Otherwise, TRUE is pushed to $status and some message is pushed onto $log
//	====================================

//	============-- stat() --============
//	This function returns elements from the status array.
//	If you send an actual #, the function will return that status index (not recommended)
//	If you send TRUE, the function will return the last status entry
//				[If you send nothing, it does that too]
//	If you send the string 'yes', the function will return the whole damn status array to you
//	(REALLY not recommended)
//	====================================

//	 ===========-- log() --=============
//	This function returns elements from the log array.
//	If you send an actual #, the function will return that log index (not recommended)
//	If you send TRUE, the function will return the last log entry
//				[If you send nothing, it does that too]
//	If you send the string 'yes', the function will return the whole damn log to you.
//	====================================

//	  =========-- qwviterations() --== 
//	   ========-- qiterations() --===
//	These functions return their respective variables to the calling location.
//	====================================

//	 ===========-- getq() --===========
//	   ========-- getqwv() --========
//	These functions return the prepared query string for their respective query types
//	====================================

//	 ===========-- last() --============
//	This function will return the id of the last insert as a string.
//	====================================

//	 ===========-- reset() --============
//	This function resets every stored value in the object as if it were being called for the
//		first time, including "remembering" the passed database type, and handling it
//		according to how it would be handled on the first __construct call.
//		reset() does NOT reset the database information (user, pass, db name, location, db type)
//	=====================================

//	 ===========-- prep() --============
//	This function allows the calling script to prepare an SQL statement without actually
//		executing the statement on the database.  This would typically be used where the
//		calling script would loop through some data on a single statement using something
//		similar to:
//			$db->qwv(null,$data);
//		Because the qwv() function uses previously prepared SQL if it's first argument is null,
//		a loop using this function would be inefficient if it had to prepare SQL once, then
//		use null every time after.
//		To limit this inefficiency, the calling script could be similar to:
//			$db->prep($sql, 1);
//			loop
//			{
//				$db->qwv(null, $data);
//			}

//		prep() takes two arguments:
//			First, the SQL statement to be prepared ($sql)
//			The second argument is answers the question "With Values?" ($wv)
//				If the second argument is true (1), the prepared SQL is treated as a filler for
//					$prepqwv.
//				If the second statement is false (0), the prepared SQL is treated as filler for
//					$prepq.
//				$wv defaults to true (1)
//	====================================
	class Database
	{
		private $dbuser;
		private $dbpass;
		private $db;
		private $loc;
		private $type;
		
		private $dsn;
		private $dbh;
		
		private $prepq = '';
		private $qsql = '';
		private $qiterate = 0;
		
		private $prepqwv = '';
		private $qwvsql = '';
		private $qwvvals = '';
		private $qwviterate = 0;
		
		private $log = array();
		private $status = array();
		
		public function  __construct($user,$pass,$db,$loc,$type)
		{
			$this->dbuser = $user;
			$this->dbpass = $pass;
			$this->db = $db;
			$this->loc = $loc;
			$this->type = $type;
			
			if( $type == 'mysql' )
			{
				$this->dsn = 'mysql:dbname='.$db.';host='.$loc;
			}
			else
			{
				$this->logStat("UNHANDLED_DB_TYPE",FALSE);
			}
			
			try
			{
				$this->dbh = new PDO($this->dsn, $this->dbuser, $this->dbpass);
			}
			catch (PDOException $e)
			{
				$this->logStat("DB_CONN_FAIL",FALSE);
			}
		}
		
		public function q($sql)
		{				
			if( $sql === '' || $sql == null)
			{
				if( $this->prepq == '' )
				{
					$this->logStat("PREV_PREP_DNE",TRUE);;
				}
				else
				{
					$this->prepq->setFetchMode(PDO::FETCH_ASSOC);
				
					$this->logStat("Q_OLD_SQL",TRUE);
					$this->qiterate += 1;
					return $this->prepq->fetchAll();
				}
			}
			else
			{
				$this->qsql = $sql;
				$this->qiterate = 0;
				
				if( $this->dbh )
				{
					$this->prepq = $this->dbh->prepare($this->qsql);
					$this->prepq->execute();
					$this->prepq->setFetchMode(PDO::FETCH_ASSOC);
					
					$this->logStat("Q_NEW_SQL",TRUE);
					$this->qiterate += 1;
					return $this->prepq->fetchAll();
				}
				else
				{
					$this->logStat("DB_CONN_DNE",FALSE);
				}
			}
		}
		
		public function qwv($sql, $vars_array, $bind = false)
		{
			$this->qwvvals = $vars_array;
			if( $sql === '' || $sql == null)
			{
				if( $this->prepqwv == '' )
				{
					$this->logStat("PREV_PREP_DNE", FALSE);
				}
				else
				{
					$this->prepqwv->execute(array_values($this->qwvvals));
					
					$this->prepqwv->setFetchMode(PDO::FETCH_ASSOC);
					$this->logStat("QWV_OLD_SQL",TRUE);
					$this->qwviterate += 1;
					return $this->prepqwv->fetchAll();
				}
			}
			else
			{
				$this->qwvsql = $sql;
				$this->qwviterate = 0;
				
				if( $this->dbh )
				{
					$this->prepqwv = $this->dbh->prepare($this->qwvsql);
					
					$this->prepqwv->execute(array_values($this->qwvvals));
					
					$this->prepqwv->setFetchMode(PDO::FETCH_ASSOC);
					$this->logStat("QWV_NEW_SQL",TRUE);
					$this->qwviterate += 1;
					return $this->prepqwv->fetchAll();
				}
				else
				{
					$this->logStat("DB_CONN_DNE", FALSE);
				}
			}
		}
		
		public function prep($sql, $wv = 1)
		{
			if( $wv)
			{
				$this->qwvsql = $sql;
				if( $this->dbh )
				{
					$this->prepqwv = $this->dbh->prepare($this->qwvsql);
				}
				else
				{
					$this->logStat("DB_CONN_DNE", FALSE);
				}
			}
			else
			{
				$this->qsql = $sql;
				if( $this->dbh )
				{
					$this->prepq = $this->dbh->prepare($this->qsql);
				}
				else
				{
					$this->logStat("DB_CONN_DNE",FALSE);
				}
			}
		}
		
		public function qwviterations()
		{
			return $this->qwviterations;
		}
		
		public function qiterations()
		{
			return $this->qiterations;
		}
		
		public function getq()
		{
			return $this->prepq->queryString;
		}
		
		public function getqwv()
		{
			return $this->prepqwv->queryString;
		}
		
		public function reset()
		{
			$this->dsn = '';
			$this->dbh = '';
			$this->prepq = '';
			$this->qsql = '';
			$this->qiterate = 0;
			$this->prepqwv = '';
			$this->qwvsql = '';
			$this->qwvvals = '';
			$this->qwviterate = 0;
			$this->logStat("RESET",TRUE);
			
			if( $this->type == 'mysql' )
			{
				$this->dsn = 'mysql:dbname='.$this->db.';host='.$this->loc;
			}
			else
			{
				$this->logStat("UNHANDLED_DB_TYPE",FALSE);
			}
			
			try
			{
				$this->dbh = new PDO($this->dsn, $this->dbuser, $this->dbpass);
			}
			catch (PDOException $e)
			{
				$this->logStat("DB_CONN_FAIL",FALSE);
			}
		}
		
		private function logStat($msg, $bool)
		{
			//this updates the error and status arrays
			array_push($this->log, $msg);
			array_push($this->status, $bool);
		}
		
		//If you send an actual #, the function will return that log index (not recommended)
		//If you send TRUE, the function will return the last log entry
		//[If you send nothing, it does that too]
		//If you send the string 'yes', the function will return the whole damn log to you. (REALLY not recommended)
		public function log($command = NULL)
		{
			$low = -1;
			$high = count( $this->log );
			if( is_int( $command ) && $low < $command && $command < $high)
			{
				return $this->log[$command];
			}
			elseif( (is_bool( $command ) && $command == TRUE) || $command == NULL )
			{
				return $this->log[$high - 1];
			}
			elseif( is_string( $command ) && $command == 'yes' )
			{
				return $this->log;
			}
			else
			{
				return "Bad input parameter.  Index out of scope or command unknown.";
			}			
		}
		
		//If you send an actual #, the function will return that status index (not recommended)
		//If you send TRUE, the function will return the last status entry
		//[If you send nothing, it does that too]
		//If you send the string 'yes', the function will return the whole damn status array to you. 
		public function stat( $command = NULL )
		{
			$low = -1;
			$high = count( $this->status );
			if( is_int( $command ) && $low < $command && $command < $high)
			{
				return $this->status[$command];
			}
			elseif( (is_bool( $command ) && $command == TRUE) || $command == NULL )
			{
				return $this->status[$high - 1];
			}
			elseif( is_string( $command ) && $command == 'yes' )
			{
				return $this->status;
			}
			else
			{
				return "Bad input parameter.  Index out of scope or command unknown.";
			}
		}
		
		//last returns the id of most recent insert
		public function last()
		{
			return $this->dbh->lastInsertId();
		}
	}
?>