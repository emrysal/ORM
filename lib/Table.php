<?php
/**
**  @file Table.php
**  @author Dregian
**  @date 2012-06-24
**/
namespace ORM;

class Table
{
	static private $cache = array();

	public $class;
	public $pk;
	
	protected $_db;
	protected $_table;
	
	static public function load( $class ) {
	
		if ( ! isset(static::$cache[$class]) ) {
			static::$cache[$class] = new Table($class);
		}
		return static::$cache[$class];
	}

	public function __construct($class) {
		
		// pseudo DB, adjust to your own specifications. 
		$this->_db = new \PDO('mysql:dbname=example;host=<host>', '<user>', '<pass>');
		$this->class = $class;
		
		$this->_setTableName();
		$this->_setPrimaryKey();
	}

	private function _setTableName() {
		$parts = explode('\\',$this->class);
		$this->_table = strtolower($parts[count($parts)-1]).'s';
	}
	
	private function _setPrimaryKey() {
		$this->pk = 'id';
	}

	public function find( &$options, $readonly = false ) {
		return $this->_select($this->_toSQL($options), $readonly);
	}

	public function findBySQL( $sql, $readonly = false ) {
		return $this->_select($sql, $readonly);
	}
	
	// morph $options to SQL string
	private function _toSQL(&$options) {
		
		$sql = new SQLBuilder($this->_db, $this->_table);
		
		if ( array_key_exists('select', $options) ) {
			$sql->select($options['select']);
		}
		
		if ( array_key_exists('where', $options) ) {
			$sql->where($options['where']);
		}
		
		if ( array_key_exists('order',$options) ) {
			$sql->order($options['order']);
		}

		if ( array_key_exists('limit',$options) ) {
			$sql->limit($options['limit']);
		}

		if ( array_key_exists('offset',$options) ) {
			$sql->offset($options['offset']);
		}

		if ( array_key_exists('group',$options) ) {
			$sql->group($options['group']);
		}

		if ( array_key_exists('having',$options) ) {
			$sql->having($options['having']);
		}
		return $sql;
	}
	
	private function _select($sql, $readonly = false) {
	
		$sth = $this->_db->query($sql);
		$result = array();
		
		if ( ! $sth ) {
			return array();
		}
			
		while ( $row = $sth->fetch() ) {
			
			$model = new $this->class($row);
			
			if ( $readonly ) {
				$model->readonly();
			}
			$model->newRecord(false);
			
			$result[] = $model;
		}
		return ( count($result) > 1 ) ? $result : ( empty($result) ? $result : $result[0] );
	}
	
	public function insert(&$data) {
		$sql = new SQLBuilder($this->_db, $this->_table);
		return $this->_db->query($sql->insert($data));
	}
	
	public function update( &$data, $where ) {
		
		$sql = new SQLBuilder($this->_db, $this->_table);
		$sql->update($data)->where($where);
		return $this->_db->query($sql);
	}
	
	public function delete( array $options = array() ) {

		$sql = new SQLBuilder($this->_db, $this->_table);
		
		if ( array_key_exists('where', $options) ) {
			$sql->where($options['where']);
		}

		// check again for options since function may throw an error 
		// - without halting execution
		if ( $options && $this->_db->query($sql->delete($options)) )
			return true;
		return false;
	}
	
	// disabling delete all in the delete function as a safety precaution
	public function deleteAll() {
		
		$sql = new SQLBuilder($this->_db, $this->_table);
		
		if ( $this->_db->query($sql->delete()) )
			return true;
		return false;
	}
}
