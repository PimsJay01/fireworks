<?php 

class Bdd
{ 
    private $connect;
    
	private $select;
	private $from;
	private $where;
	private $group;
	private $order;
	
	private $value;
	
	public function __construct()
	{
		$this->clear_infos();
		
		$this->connect = new Connect();
	}
	
	public function clear_infos()
	{
		$this->select = "";
		$this->from = "";
		$this->where = "";
		$this->group = "";
		$this->order = "";
		
		$this->value = "";
	}
	
	// Ajout des données pour la requête
	
	public function set_select($select)
	{
		if(is_string($select) AND !empty($select))
		{
			$this->select = $select;
		}
	}
	
	public function set_select_array($select = array())
	{
		$this->select = "";
		foreach($select as $value)
		{
			if(is_string($value) AND !empty($value))
			{
				$this->select .= $value . ", ";
			}
		}
		$this->select = substr($this->select,0,strlen($this->select)-strlen(", "));
	}
	
	public function add_select($select)
	{
		if(is_string($select) AND !empty($select))
		{
			$this->select .= ", " . $select;
		}
	}
	
	public function _select($select)
	{
        if(!empty($select))
        {
            if(is_array($select)) {
                $this->set_select_array($select);
            }
            else
            {
                if(empty($this->select))
                {
                    $this->set_select($select);
                }
                else
                {
                    $this->add_select($select);
                }
            }
        }
	}
	
	public function set_from($from)
	{
		if(is_string($from) AND !empty($from))
		{
			$this->from = $from;
		}
	}
	
	// lie la nouvelle table $from1 à la table précèdement déclarée $from2
	// liées par la valeur du champ commun $field
	public function add_from($from1, $field1, $from2, $field2)
	{
		if(is_string($from1) AND !empty($from1) AND is_string($field1) AND !empty($field1) AND is_string($from2) AND !empty($from2) AND is_string($field2) AND !empty($field2))
		{
			$pos = strpos($from1," ");
			if($pos === false)
			{
				$this->from .= " JOIN " . $from1 . " ON " . $this->link($from1, $field1, $from2, $field2);
			}
			else
			{
				$this->from .= " JOIN " . $from1 . " ON " . $this->link(substr($from1,$pos,strlen($from1)), $field1, $from2, $field2);
			}
		}
	}
	
	public function add_from_2($from1, $field1a, $field1b, $from2, $field2a, $field2b)
	{
		if(is_string($from1) AND !empty($from1) AND is_string($field1a) AND !empty($field1a) AND is_string($field1b) AND !empty($field1b) AND is_string($from2) AND !empty($from2) AND is_string($field2a) AND !empty($field2a) AND is_string($field2b) AND !empty($field2b))
		{
			$pos = strpos($from1," ");
			if($pos === false)
			{
				$this->from .= " JOIN " . $from1 . " ON (" . $this->link($from1, $field1a, $from2, $field2a) . " AND " . $this->link($from1, $field1b, $from2, $field2b) . ")";
			}
			else
			{ 
				$this->from .= " JOIN " . $from1 . " ON (" . $this->link(substr($from1,$pos,strlen($from1)), $field1a, $from2, $field2a) . " AND " . $this->link(substr($from1,$pos,strlen($from1)), $field1b, $from2, $field2b) . ")";
			}
		}
	}
	
	// lie si possible, la nouvelle table $from1 à la table précèdement déclarée $from2
	// liées par la valeur du champ commun $field
// 	public function add_left_join($from1, $field1, $from2, $field2)
// 	{
// 		if(is_string($from1) AND !empty($from1) AND is_string($field1) AND !empty($field1) AND is_string($from2) AND !empty($from2) AND is_string($field2) AND !empty($field2))
// 		{
// 			$pos = strpos($from1," ");
// 			if($pos === false)
// 			{
// 				$this->from .= " LEFT JOIN " . $from1 . " ON " . $this->link($from1, $field1, $from2, $field2);
// 			}
// 			else
// 			{
// 				$this->from .= " LEFT JOIN " . $from1 . " ON " . $this->link(substr($from1,$pos,strlen($from1)), $field1, $from2, $field2);
// 			}
// 		}
// 	}
	
	// lie si possible, la nouvelle table $from1 à la table précèdement déclarée $from2
	// liées par la valeur du champ commun $field
	public function add_left_join($from, $where)
	{
		if(is_string($from) AND !empty($from) AND is_string($where) AND !empty($where))
		{
			$this->from .= " LEFT JOIN " . $from . " ON " . $where;
		}
	}
	
	public function set_where($where)
	{
		if(is_string($where) AND !empty($where))
		{
			$this->where = $where;
		}
	}
	
	public function add_where($where)
	{
		if(is_string($where) AND !empty($where))
		{
			$this->where .= " AND " . $where;
		}
	}
	
	public function set_group($group)
	{
		if(is_string($group) AND !empty($group))
		{
			$this->group = $group;
		}
	}
	
	public function add_group($group)
	{
		if(is_string($group) AND !empty($group))
		{
			$this->group .= ", " . $group;
		}
	}
	
	public function set_order($order)
	{
		if(is_string($order) AND !empty($order))
		{
			$this->order = $order;
		}
	}
	
	public function add_order($order)
	{
		if(is_string($order) AND !empty($order))
		{
			$this->order .= ", " . $order;
		}
	}
	
	public function set_value($field, $value)
	{
        $value = $this->connect->escape($value);
		if(is_string($field) AND !empty($field))
		{
			$this->value .= $this->equal($field,$value);
		}
	}
	
	public function add_value($field, $value)
	{
        $value = $this->connect->escape($value);
		if(is_string($field) AND !empty($field))
		{
			$this->value .= ", " . $this->equal($field,$value);
		}
	}
	
	// fonctions de requêtes
	
	public function select($start = 0, $offset = 0)
	{
		$query;
		if(!empty($this->select))
		{
			$query = "SELECT " . $this->select;
		}
		else
		{
			$query = "SELECT *";
		}
		$query .= " FROM " . $this->from;
		if(!empty($this->where))
		{
			$query .= " WHERE " . $this->where;
		}
		if(!empty($this->group))
		{
			$query .= " GROUP BY " . $this->group;
		}
		if(!empty($this->order))
		{
			$query .= " ORDER BY " . $this->order;
		}
		if(is_numeric($start) AND is_numeric($offset) AND ($offset != 0))
		{
			$query .= " LIMIT " . $start . "," . $offset;
		}
		$this->clear_infos();
		
// 		echo '<p>' . $query . '</p>';
		
		return $this->connect->query($query . ";");
	}
	
	public function insert($table = '')
	{
		$query = "INSERT INTO " . $table;
		if(!empty($this->value))
		{
			$query .= " SET " . $this->value;
		}
		$this->clear_infos();
		
//         echo '<p>' . $query . '</p>';
		
		return $this->connect->insert($query . ";");
	}
	
	public function update($table = '')
	{
		$query = "UPDATE " . $table;
		if(!empty($this->value))
		{
			$query .= " SET " . $this->value;
		}
		if(!empty($this->where))
		{
			$query .= " WHERE " . $this->where;
		}
		$this->clear_infos();
		
//         echo '<p>' . $query . '</p>';
		
		return $this->connect->query($query . ";");
	}
	
	public function delete($table = '')
	{
		$query = "DELETE FROM " . $table;
		if(!empty($this->where))
		{
			$query .= " WHERE " . $this->where;
		}
		$this->clear_infos();
		
		return $this->connect->query($query . ";");
	}
		
	// fonctions d'aides aux requêtes
	
	public function select_min($select, $from, $where)
    {
        if(is_string($select) AND !empty($select) AND is_string($from) AND !empty($from) AND is_string($where) AND !empty($where))
        {
            return $select . " = (SELECT MIN(" . $select . ") FROM " . $from . " WHERE " . $where . ")";
        }
        return "";
    }
    
    public function select_max($select, $from, $where)
    {
        if(is_string($select) AND !empty($select) AND is_string($from) AND !empty($from) AND is_string($where) AND !empty($where))
        {
            return $select . " = (SELECT MAX(" . $select . ") FROM " . $from . " WHERE " . $where . ")";
        }
        return "";
    }
	
	public function abs($order)
	{
		if(is_string($order) AND !empty($order))
		{
			return " ABS(" . $order . ") ASC";
		}
		return "";
	}
	
	public function desc($order)
	{
		if(is_string($order) AND !empty($order))
		{
			return $order . " DESC";
		}
		return "";
	}
	
	public function dot($from, $field)
	{
		if(is_string($from) AND !empty($from) AND is_string($field) AND !empty($field))
		{
			return $from . "." . $field;
		}
		return "";
	}
	
	public function equal($field, $value)
	{
        $value = $this->connect->escape($value);
		if(is_string($field) AND !empty($field))
		{
			if($this->is_function($value))
				return $field . " = " . $value;
			else
				return $field . " = '" . $value . "'";
		}
		return "";
	}
	
	public function equal_null($field)
	{
		if(is_string($field) AND !empty($field))
		{
			return $field . " IS NULL";
		}
		return "";
	}
	
	public function not_equal($field, $value)
	{
        $value = $this->connect->escape($value);
		if(is_string($field) AND !empty($field))
		{
			if($this->is_function($value))
				return $field . " <> " . $value;
			else
				return $field . " <> '" . $value . "'";
		}
		return "";
	}
	
	public function link($from1, $field1, $from2, $field2)
	{
		if(is_string($from1) AND !empty($from1) AND is_string($field1) AND !empty($field1) AND is_string($from2) AND !empty($from2) AND is_string($field2) AND !empty($field2))
		{
			return $this->dot($from1,$field1) . " = " . $this->dot($from2,$field2);
		}
		return "";
	}
	
	public function and_($where1, $where2)
	{
		if(is_string($where1) AND !empty($where1) AND is_string($where2) AND !empty($where2))
		{
			return "(" . $where1 . " AND " . $where2 . ")";
		}
		return "";
	}
	
	public function or_($where1, $where2)
	{
		if(is_string($where1) AND !empty($where1) AND is_string($where2) AND !empty($where2))
		{
			return "(" . $where1 . " OR " . $where2 . ")";
		}
		return "";
	}
	
	public function smaller($field, $value)
	{
        $value = $this->connect->escape($value);
		if(is_string($field) AND !empty($field))
		{
			if($this->is_function($value))
				return $field . " <= " . $value;
			else
				return $field . " <= '" . $value . "'";
		}
		return "";
	}
	
	public function greater($field, $value)
	{
        $value = $this->connect->escape($value);
		if(is_string($field) AND !empty($field))
		{
			if($this->is_function($value))
				return $field . " >= " . $value;
			else
				return $field . " >= '" . $value . "'";
		}
		return "";
	}
	
	private function is_function($value)
	{
		if(strlen($value) > 2)
			return ((substr($value,-1) == ")") AND ($value == strtoupper($value)));
		return false;
	}
	
// 	public function show()
// 	{
// 		echo $this->select;
// 	}	
}