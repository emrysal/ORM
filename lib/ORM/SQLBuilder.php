<?php
/**
**  @file SQLBuilder.php
**  @author Dregian
**  @date 2012-06-24
**/
namespace ORM;

class SQLBuilder
{
	private $_conn;
	private $_table;
	
	private $_operation = 'SELECT';
	private $_select = '*';
	private $_data;
	
	private $_limit = '18446744073709551615';
	private $_offset = 0;
	
	private $_joins;
	private $_order;
	private $_group;
	private $_having;
	private $_where;

	public function __construct( $conn, $table ) {
		$this->_conn = $conn;
		$this->_table = $table;
	}

	public function __toString() {
		$func = '_build' . ucfirst(strtolower($this->_operation));
		return $this->$func();
	}
	
	// @todo: utilize $this->quote() someway
	public function insert( $data ) {
		
		$this->_operation = 'INSERT';
		
		if ( is_string( $data ) ) {
			$this->_data = $data;
		} else {
			
			$this->_data = "(";
			$this->_data .= join(', ', array_keys($data));
			$this->_data .= ") VALUES (";
			$this->_data .= join(', ', array_map(function($value) { 
				return ( is_numeric($value) ) ? $value : "'".$value."'"; 
			}, array_values($data)));
			$this->_data .= ")";
		}
		return $this;
	}
	
	// @todo: add hash functionality.
	public function select( $data ) {
		$this->_operation = 'SELECT';
		$this->_select = $data;
		return $this;
	}
	
	public function update( $data ) {
		
		$this->_operation = 'UPDATE';
		
		if ( is_string( $data ) ) {
			$this->_data = $data;
		} else {
			
			$this->_data = '';
			
			foreach ( $data as $key => $value ) {
				
				if ( is_string($key) ) {
					$this->_data .= '`' . $key . '` = ' . $this->_quote($value) . ', ';
				}
			}
			$this->_data = rtrim($this->_data, ', ') . ' ';
		}
		return $this;
	}
	
	public function delete() {
		$this->_operation = 'DELETE';
		return $this;
	}
	
	public function where() {
	
		$args = func_get_args();
		
		if ( is_string($args[0]) ) {
			$this->_where = $args[0];
		} else {
			
			$this->_where = '';

			foreach ( $args[0] as $key => $condition ) {
				$this->_where .= '`' . $key . '` = ' 
				. $this->_quote($condition) . ' AND ';
			}
			$this->_where = rtrim($this->_where, ' AND');
		}
		return $this;
	}
	
	public function joins( $joins ) {
		$this->_joins = $joins;
		return $this;
	}
	
	public function order( $order ) {
		$this->_order = $order;	
		return $this;
	}
	
	public function having( $having ) {
		$this->_having = $having;
		return $this;
	}
	
	public function group( $group ) {
		$this->_group = $group;
		return $this;
	}
	
	public function limit( $limit ) {
		$this->_limit = intval($limit);
		return $this;
	}
	
	public function offset( $offset ) {
		$this->_offset = intval($offset);
		return $this;
	}

	// @todo: Don't quote functions
	private function _quote( $str ) {
		return is_numeric($str) ? $str : "'".$str."'";
	}

	private function _buildInsert() {
		$sql = "INSERT INTO $this->_table $this->_data";
		return $sql;
	}
	
	private function _buildSelect() {
		
		$sql = "SELECT $this->_select FROM `$this->_table`";
		
		if ( $this->_joins ) {
			$sql .= ' ' . $this->_joins;
		}
		
		if ( $this->_where ) {
			$sql .= ' WHERE ' . $this->_where;
		}
		
		if ( $this->_group ) {
			$sql .= ' GROUP BY ' . $this->_group;
		}
		
		if ( $this->_having ) {
			$sql .= ' HAVING ' . $this->_having;
		}
		
		if ( $this->_order ) {
			$sql .= ' ORDER BY ' . $this->_order;
		}
		
		if ( $this->_limit !== '18446744073709551615' 
			|| $this->_offset !== 0 ) {
			$sql .= ' LIMIT ' . $this->_offset . ',' . $this->_limit;
		}
		return $sql;
	}
	
	private function _buildUpdate() {
		
		$sql = "UPDATE $this->_table SET $this->_data";
		
		if ( $this->_where ) {
			$sql .= 'WHERE ' . $this->_where;
		}
		
		if ( $this->_order ) {
			$sql .= 'ORDER BY ' . $this->_order;
		}
		
		if ( $this->_limit !== '18446744073709551615' 
			|| $this->_offset !== 0 ) {
			$sql .= 'LIMIT ' . $this->_offset . ',' . $this->_limit;
		}
		return $sql;
	}
	
	private function _buildDelete() {
		
		$sql = "DELETE FROM $this->_table";
		
		if ( $this->_where ) {
			$sql .= ' WHERE ' . $this->_where;
		}
		
		if ( $this->_order ) {
			$sql .= ' ORDER BY ' . $this->_order;
		}
		
		if ( $this->_limit !== '18446744073709551615' 
			|| $this->_offset !== 0 ) {
			$sql .= 'LIMIT ' . $this->_offset . ',' . $this->_limit;
		}
		return $sql;
	}
}
