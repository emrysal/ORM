<?php
/**
**	@file Table.php
**	@author Dregian
**	@date 2012-06-24
**/
namespace ORM;

class Table {

	static private $cache = array();

	public $class;
	public $pk;

	protected $_db;
	protected $_table;

	static public function load( $class ) {

		if ( ! isset(static::$cache[ $class ]) ) {
			static::$cache[ $class ] = new Table($class);
		}
		return static::$cache[ $class ];
	}

	public function __construct( $class ) {
		include('../config/config.php');
		$this->_db = new \PDO('mysql:dbname='.$config['db']['name'].';host='.$config['db']['host'], $config['db']['user'], $config['db']['pass']);
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

	public function select($sql, $readonly = false) {

		// if $sql is still an array, convert it to a SQL string.
		if ( is_array($sql) ) {
			$sql = $this->_toSQL($sql);
		}

		$sth = $this->_db->query($sql);

		$result = array();

		if ( ! $sth ) {
			return $result;
		}

		while ( $row = $sth->fetch() ) {

			$model = new $this->class($row);

			if ( $readonly ) {
				$model->readonly();
			}
			$model->newRecord();

			$result[] = $model;
		}
		return ( count($result) > 1 ) ? $result : ( empty($result) ? $result : $result[0] );
	}

	public function selectSingle($sql, $readonly = false) {

		// if $sql is still an array, convert it to a SQL string.
		if ( is_array($sql) ) {
			$sql = $this->_toSQL($sql);
		}

		$sth = $this->_db->query($sql);
		$result = array();

		if ( ! $sth ) {
			return $result;
		}
		$row = $sth->fetch(\PDO::FETCH_NUM);
		return $row[0];
	}

	public function insert( &$data ) {

		$sql = new SQLBuilder($this->_db, $this->_table);
		if ( $this->_db->query($sql->insert($data)) )
			return $this->_db->lastInsertId();
		return false;
	}

	public function update( $data, $where ) {
		$sql = new SQLBuilder($this->_db, $this->_table);

		if ( $where ) {
			$sql->where($where);
		}

		if ( $sth = $this->_db->query($sql->update($data)) ) {
			return $sth->rowCount();
		}
		return false;
	}

	public function delete( $data = array() ) {
		$sql = new SQLBuilder($this->_db, $this->_table);

		if ( isset($data['where']) ) {
			$sql->where($data['where']);
		}

		if ( $sth = $this->_db->query($sql->delete($data)) ) {
			return $sth->rowCount();
		}
		return false;
	}
}
