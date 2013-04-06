<?php 
/**
**  @file Model.php
**  @author Dregian
**  @date 2012-06-24
**/
namespace ORM;

class Model 
{	
	private $_isNewRecord = true;
	private $_isReadonly = false;
	private $_attributes = array();

	static public $primaryKey;
	
	public function __wakeup() { 
		static::table();
	}
	
	public function __construct( array $attrs = array(), $newRecord = true ) {
		$this->_isNewRecord = $newRecord;
		$this->_setAttributes( $attrs );
	}
	
	private function _setAttributes( array &$attrs ) {
		foreach ( $attrs as $key => $value ) {
			$this->_attributes[$key] = $value;
		}
	}
	
	static public function table() {
		return Table::load(get_called_class());
	}
	
	static public function create( array $attributes ) {
	
		$class_name = get_called_class();
		
		$model = new $class_name($attributes);
		$model->save();
		return $model;
	}
	
	static public function findBySQL( $sql ) {
		$this->_isNewRecord = false;
		return static::table()->select($sql);
	}
	
	public function &__get($name)
	{
		// check for getter
		if ( method_exists($this, "get".ucfirst($name)) ) {
			$name = "get".ucfirst($name);
			$value = $this->$name();
			return $value;
		}
		return $this->_attributes[$name];
	}

	public function __set($prop, $val) {
		
		if ( method_exists($this, "set".ucfirst($prop)) ) {
			$name = "set".ucfirst($prop);
			return $this->$name($value);
		}
		
		if ( array_key_exists($prop, $this->_attributes) ) {
			return $this->_attributes[$prop] = $val;
		}
		$this->_attributes[$prop] = $val;
	}

	// find object
	static public function find() {

		$args = func_get_args();

		if ( func_num_args() === 0 ) {
			$args[0] = 'all';
		}

		if ( $args[0] === 'all' || $args[0] === 'first' || $args[0] === 'last' ) {

			if ( isset($args[1]) && is_array($args[1]) ) {
				$options = $args[1];
			} else {
				$options = array();
			}

			switch ( $args[0]) {
				
				case 'all' :
				    $options['select'] = '*';
					break;
	
				// @todo: provide support for reversing order
				case 'last' :
					$options['order'] = static::table()->pk . ' DESC';
				case 'first' :
					$options['limit'] = 1;
			 		$options['offset'] = 0;
					break;
			}
		}
		else if ( func_num_args() > 1 ) {
			$options = array('where' => array($args[0] => $args[1]));
		}
		else if ( is_numeric($args[0]) ) {
			$options = array('where' => array(static::table()->pk => $args[0]));
		} else if ( $args[0] === 'all' || $args[0] === 'first' || $args[0] === 'last' ) {

			if ( isset($args[1]) && is_array($args[1]) ) {
				$options = $args[1];
			} else {
				$options = array();
			}

			switch ( $args[0]) {
				
				case 'all' :
				    $options['select'] = '*';
					break;
	
				// @todo: provide support for reversing order
				case 'last' :
					$options['order'] = static::table()->pk . ' DESC';
				case 'first' :
					$options['limit'] = 1;
			 		$options['offset'] = 0;
					break;
			}
		} else {
			$options = $args[0];
		}
		return static::table()->select($options);
	}

	// @todo
	static public function count() { 

		$args = func_get_args();
		$options = array();

		if ( func_num_args() > 1 ) {
			$options['where'] = array($args[0] => $args[1]);
		} else if ( isset($args[0]) && is_array($args[0]) ) {
			$options = $args[0];
		}
		$options['select'] = 'COUNT(*)';

		return static::table()->selectSingle($options);
	}
	
	public function newRecord() {
		$this->_isNewRecord = ( $this->_isNewRecord ) ? false : true;
	}

	public function isNewRecord() {
		return $this->_isNewRecord;
	}

	public function readonly() {
		$this->_isReadonly = ( $this->_isReadonly ) ? false : true;
	}
	
	// @todo
	public function save( $validate = true ) { 
	
		echo $this->name;

		$result = false;

		if ( $this->isNewRecord() ) {
			if ( $result = $this->_insert($validate) ) {
				$this->newRecord();
			}
		} else { 
			$result = $this->_update($validate);
		}
		return $result;
	}
	
	// @todo: validate
	private function _insert( $validate = true ) {
		return static::table()->insert($this->_attributes);
	}
	
	private function _update( $validate = true ) {
		return static::table()->update($this->_attributes, array($this->table()->pk => $this->_attributes[$this->table()->pk]));
	}
	
	// Remove object
	static public function delete( array $options = array() ) {
		return static::table()->delete($options); 
	}
	

	// alias for find(); for clarity
	static public function all() {
		return static::find();
	}

	static public function first( array $options = array() ) { 
		return static::find('first', $options);
	}
	static public function last( array $options = array() ) { 
		return static::find('last', $options);
	}
}
