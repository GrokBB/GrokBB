<?php
namespace GrokBB;

/**
 * A lightweight database abstraction layer for PDO
 */
class DB extends \PDO {
	/**
     * The bind parameters being used in the WHERE clause
     * @var array
     */
	protected $bindings = array();
	
	/**
     * The columns being in the MATCH / AGAINST
     * @var array
     */
	protected $colsMatchAgainst = array();
	
	/**
     * A log of all executed queries
     * @var array
     */
	public $log = array();
	
	/**
     * Creates a DB object
     *
     * @param  string $dsn      the data source name
     * @param  string $username the username for the DSN
     * @param  string $password the password for the DSN
     * @param  array  $options  the database driver options
     * @return object           a DB object
     */
	function __construct($dsn, $username = '', $password = '', $options = array()) {
		parent::__construct($dsn, $username, $password, $options);
		$this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}
	
	/**
     * Gets one record
     *
     * @param  string $table the table name
     * @param  array  $where the WHERE conditions
     * @param  string $sort  the column to sort on
     * @param  array  $cols  the column list to return
     * @param  string $group the column to group on
     * @return object        the selected record
     */
	public function getOne($table, $where = false, $sort = false, $cols = false, $group = false) {
		$query = $this->select($table, $where, $sort, $cols, 1, 0, $group);
		return $query->fetchObject();
	}
	
	/**
     * Gets all records
     *
     * @param  string $table the table name
     * @param  array  $where the WHERE conditions
     * @param  string $sort  the column to sort on
     * @param  array  $cols  the column list to return
     * @param  int    $limit the maximum # of records to return
     * @param  int    $start the record to start counting from
     * @param  string $group the column to group on
     * @return array         all the selected records
     */
	public function getAll($table, $where = false, $sort = false, $cols = false, $limit = false, $start = false, $group = false) {
		$query = $this->select($table, $where, $sort, $cols, $limit, $start, $group);
		return $query->fetchAll(\PDO::FETCH_OBJ);
	}
	
	/**
     * Gets the last error message
     *
     * @return string the error message
     */
	public function getErr() {
	    $error = $this->errorInfo();
	    return 'DB Error #' . $error[0] . (($error[2]) ? ' - ' . $error[2] : '');
	}
	
	/**
     * Gets all records
     *
     * @param  string $table the table name
     * @param  array  $where the WHERE conditions
     * @param  string $sort  the column to sort on
     * @param  array  $cols  the column list to return
     * @param  int    $limit the maximum # of records to return
     * @param  int    $start the record to start counting from
     * @param  string $group the column to group on
     * @return object        a PDO Statement object
     */
	public function select($table, $where = false, $sort = false, $cols = false, $limit = false, $start = false, $group = false) {
	    $this->bindings = array();
		$table = DB_PREFIX . $table;
		
		// build the WHERE
		if (is_array($where) && count($where) > 0) {
			$whereSQL = $this->where($where);
		} else {
		    $whereSQL = "";
		}
		
		// build the GROUP BY
		if ($group) {
			$groupSQL = "GROUP BY $group";
		} else {
			$groupSQL = "";
		}
		
		// build the ORDER BY
		if ($sort) {
			$sortSQL = "ORDER BY $sort";
		} else {
			$sortSQL = "";
		}
		
		// build the column list
		if (is_array($cols) && count($cols) > 0) {
			$colsSQL = implode(", ", $cols);
		} else {
			$colsSQL = "*";
		}
		
		// automatically append our MATCH / AGAINST columns
		// so we can apply logic based on their relevance
		if ($this->colsMatchAgainst) {
		    for ($col = 1, $rel = 1, $cmaCount = count($this->colsMatchAgainst); $col <= $cmaCount; $col++) {
		        foreach ($this->colsMatchAgainst[$col] as $cma) {
    		        $colsSQL .= ", MATCH($cma) AGAINST (:match{$col} IN BOOLEAN MODE) AS match_{$col}_{$rel}";
    		        $rel++;
    		    }
		    }
		}
		
		// build the LIMIT
		if ($limit) {
			$limitSQL = "LIMIT $limit";
		} else {
			$limitSQL = "";
		}
		
		// build the OFFSET
		if ($start) {
			$startSQL = "OFFSET $start";
		} else {
			$startSQL = "";
		}
		
		$query = $this->prepare("SELECT {$colsSQL} FROM {$table} {$whereSQL} {$groupSQL} {$sortSQL} {$limitSQL} {$startSQL}");
		$queryForLog = $query->queryString;
		
		foreach ($this->bindings as $param => $value) {
			$query->bindValue($param, $value);
			
			// emulate the query we're running to make debugging easier
			$queryForLog = str_replace($param, "'" . str_replace("'", "\'", $value) . "'", $queryForLog);
		}
		
		$this->log[] = $queryForLog;
		
		$query->execute();
		
		return $query;
	}
	
	/**
     * Runs a custom SQL query
     *
     * @param  string $sql the SQL query
     * @return object      all the selected records or false
     */
	public function custom($sql) {
		$query = $this->query($sql);
		$this->log[] = $query->queryString;
		
		if (substr($sql, 0, 6) == 'SELECT') {
		    return $query->fetchAll(\PDO::FETCH_OBJ);
		} else {
		    return true;
		}
	}
	
	/**
     * Inserts a record
     *
     * @param  string $table  the table name
     * @param  object $record the record to insert
     * @return string         the new primary key
     */
	public function insert($table, $record) {
		$table = DB_PREFIX . $table;
		
		// convert the record to an array
		if (is_object($record) || is_array($record)) {
			$record = (array) $record;
			
			if (count($record) == 0) {
				throw new \Exception('The $record object has no data');
			}
		} else {
			throw new \Exception('The $record parameter must be an object');
		}
		
		// build the column list and VALUES
		$columnSQL = "(`" . implode("`, `", array_keys($record)) . "`)";
		$valuesSQL = "VALUES (:" . implode(", :", array_keys($record)) . ")";
		
		$query = $this->prepare("INSERT INTO {$table} {$columnSQL} {$valuesSQL}");
		
		foreach ($record as $param => $value) {
			$query->bindValue(":" . $param, $value);
		}
		
		$this->log[] = $query->queryString;
		
		if ($query->execute()) {
		    return $this->lastInsertId();
		} else {
		    return false;
		}
	}
	
	/**
     * Updates a record
     *
     * @param  string $table  the table name
     * @param  object $record the record to update
     * @param  string $pKey   the primary key name
     * @return bool           TRUE on success
     */
	public function update($table, $record, $pKey = 'id') {
		$table = DB_PREFIX . $table;
		
		// convert the record to an array
		if (is_object($record) || is_array($record)) {
			$record = (array) $record;
			
			if (count($record) == 1) {
				throw new \Exception('The $record object has no data');
			} else if (!$record[$pKey]) {
				throw new \Exception('The $record object is missing the primary key (' . $pKey . ')');
			}
		} else {
			throw new \Exception('The $record parameter must be an object');
		}
		
		$columnSQL = '';
		
		// build the column list
		foreach (array_keys($record) as $column) {
			if ($column != $pKey) {
			    $columnSQL .= "`{$column}` = :{$column}, ";
			}
		}
		
		$columnSQL = substr($columnSQL, 0, -2);
		
		$query = $this->prepare("UPDATE {$table} SET {$columnSQL} WHERE {$pKey} = :pKey");
		
		foreach ($record as $param => $value) {
		    if ($param != $pKey) {
			    $query->bindValue(":" . $param, $value);
			}
		}
		
		$query->bindValue(":pKey", $record[$pKey]);
		
		$this->log[] = $query->queryString;
		
		return $query->execute();
	}
	
	/**
     * Deletes records
     *
     * @param  string $table the table name
     * @param  array  $where the WHERE conditions
     * @return bool          TRUE on success
     */
	public function delete($table, $where) {
	    $this->bindings = array();
		$table = DB_PREFIX . $table;
		
		// build the WHERE
		if (is_array($where) && count($where) > 0) {
			$whereSQL = $this->where($where);
		}
		
		$query = $this->prepare("DELETE FROM {$table} {$whereSQL}");
		
		foreach ($this->bindings as $param => $value) {
			$query->bindValue($param, $value);
		}
		
		$this->log[] = $query->queryString;
		
		return $query->execute();
	}
	
	/**
     * Builds a WHERE clause
     *
     * @param  array  $where the WHERE conditions
     * @return string        the WHERE clause
     */
	protected function where($where) {
	    $this->bindings = array();
	    $this->colsMatchAgainst = array();
	    
		// make sure we have an array
		if (is_array($where) && count($where) > 0) {
			$whereSQL = "WHERE ";
			$matchCounter = 0;
			
			foreach ($where as $key => $value) {
				$bAllow = true;
				
				// a value can include a comparison operator
				// Ex. $where = array('colname', array('>', 10))
				if (is_array($value)) {
					$cmpOp = (isset($value[0])) ? $value[0] : "";
					$value = (isset($value[1])) ? $value[1] : "";
					
					// a comparison operator must exist
					if ($cmpOp === "" || $cmpOp === NULL) {
						throw new \Exception('The comparison operator is missing in the $where array');
					}
					
					// a value is allowed to be empty
					// Ex. $where = array('colname', array('IS NULL', ''))
					if ($value === "" || $value === NULL) {
						$bAllow = false; // no need to bind a parameter
					}
				} else {
					$cmpOp = "=";
					$value = $value;
					
					// a value must exist
					if ($value === "" || $value === NULL) {
						throw new \Exception('The value is missing in the $where array');
					}
				}
    			
    			$columns = array();
			    
			    // this is an OR group when multiple keys are present
			    if (strpos($key, ',') !== false && $cmpOp != 'MATCH') {
			        $columns = explode(',', $key);
			        $whereSQL .= "(";
			    } else {
			        $columns[] = $key;
			    }
			    
			    foreach ($columns as $column) {
			        $bParam = "";
			        
    				// locate the table alias
    				$dotPos = strpos($column, '.');
    				$tblAls = '';
    				$tblBnd = '';
    				
    				if ($dotPos !== false && $cmpOp != 'MATCH') {
    				    $tblAls = substr($column, 0, $dotPos + 1);
    				    $tblBnd = substr($tblAls, 0, -1);
    				    
    				    $column = substr($column, $dotPos + 1);
    				}
    				
    				// make sure a bind parameter is required
    				if ($bAllow) {
    				    // a value can be an array when using an IN clause
    				    if (is_array($value)) {
    				        for ($i = 1; $i <= count($value); $i++) {
    				            $aParam  = ":{$tblBnd}{$column}{$i}";
    				            $bParam .= "{$aParam},";
        					    $this->bindings[$aParam] = $value[$i - 1];
    				        }
    				        
    				        $bParam = "(" . substr($bParam, 0, -1) . ")";
    				    } else {
    				        if ($cmpOp == 'MATCH') {
    				            $matchCounter++;
    				            $bParam = ":match{$matchCounter}";
    				        } else {
        				        $bParam = ":{$tblBnd}{$column}";
        				    }
        				    
        					$this->bindings[$bParam] = $value;
    					}
    				}
    				
    				if ($cmpOp == 'MATCH') {
    				    $this->colsMatchAgainst[$matchCounter] = array_map('trim', explode(',', $column));
    				    $whereSQL .= "MATCH ({$column}) AGAINST ({$bParam} IN BOOLEAN MODE) AND ";
    				} else {
    				    $whereSQL .= "{$tblAls}`{$column}` {$cmpOp} {$bParam} " . ((count($columns) > 1) ? "OR " : "AND ");
    				}
    			}
    			
    			// close the OR group
    			$whereSQL = (count($columns) > 1) ? substr($whereSQL, 0, -4) . ") AND " : $whereSQL;
			}
			
			$whereSQL = substr($whereSQL, 0, -5); // remove the trailing AND
			
			return $whereSQL;
		} else {
			throw new \Exception('The $where parameter must be an array');
		}
	}
}
?>