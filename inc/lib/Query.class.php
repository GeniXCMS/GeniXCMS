<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.1.0
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Query
{
    private $table;
    private $select = '*';
    private $wheres = [];
    private $params = [];
    private $order = '';
    private $orderRaw = '';
    private $limit = '';
    private $joins = [];
    private $groupBy = '';

    public function __construct($table = null)
    {
        $this->table = $table;
    }

    public static function table($table)
    {
        return new self($table);
    }

    public function select($columns = '*')
    {
        $this->select = is_array($columns) ? implode(', ', array_map([Db::class, 'quoteIdentifier'], $columns)) : $columns;
        return $this;
    }

    public function where($column, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = Db::quoteIdentifier($column) . " $operator ?";
        $this->params[] = $value;
        return $this;
    }

    public function orWhere($column, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = "OR " . Db::quoteIdentifier($column) . " $operator ?";
        $this->params[] = $value;
        return $this;
    }

    public function whereIn($column, array $values)
    {
        if (empty($values)) {
            $this->wheres[] = "0=1"; // Force no results
            return $this;
        }
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $this->wheres[] = Db::quoteIdentifier($column) . " IN ($placeholders)";
        foreach ($values as $value) {
            $this->params[] = $value;
        }
        return $this;
    }

    public function whereRaw($sql, array $bindings = [])
    {
        $this->wheres[] = $sql;
        foreach ($bindings as $binding) {
            $this->params[] = $binding;
        }
        return $this;
    }

    public function join($table, $first, $operator, $second, $type = 'INNER')
    {
        $this->joins[] = "$type JOIN " . $table . " ON " . $first . " $operator " . $second;
        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->order = "ORDER BY " . Db::quoteIdentifier($column) . " " . strtoupper($direction);
        return $this;
    }

    public function orderByRaw($expression)
    {
        $this->orderRaw = "ORDER BY " . $expression;
        return $this;
    }

    public function limit($limit, $offset = null)
    {
        $this->limit = "LIMIT " . (int)$limit . ($offset !== null ? " OFFSET " . (int)$offset : "");
        return $this;
    }

    public function groupBy($column)
    {
        $this->groupBy = "GROUP BY " . Db::quoteIdentifier($column);
        return $this;
    }

    public function get()
    {
        $sql = $this->buildSelect();
        return Db::result($sql, $this->params);
    }

    public function first()
    {
        $this->limit(1);
        $res = $this->get();
        return (isset($res[0]) && is_object($res[0])) ? $res[0] : null;
    }

    public function count()
    {
        $this->select = "COUNT(*) as total";
        $res = $this->first();
        return $res ? (int)$res->total : 0;
    }

    public function sum($column)
    {
        $this->select = "SUM(" . Db::quoteIdentifier($column) . ") as total";
        $res = $this->first();
        return $res ? (float)$res->total : 0;
    }

    public function insert($data)
    {
        $vars = [
            'table' => $this->table,
            'key' => $data
        ];
        return Db::insert($vars);
    }

    public function update($data)
    {
        // This is a bit tricky because Db::update expects an 'id' or 'where' array
        // We'll map our fluent where to it
        $sql = "UPDATE " . Db::quoteIdentifier($this->table) . " SET ";
        $sets = [];
        $update_params = [];
        
        foreach ($data as $key => $val) {
            $sets[] = Db::quoteIdentifier($key) . " = ?";
            $update_params[] = $val;
        }
        $sql .= implode(', ', $sets);

        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $whereStr = "";
            foreach ($this->wheres as $i => $w) {
                if ($i > 0 && strpos($w, 'OR ') !== 0) {
                    $whereStr .= " AND ";
                }
                $whereStr .= $w . " ";
            }
            $sql .= trim($whereStr);
        }

        $final_params = array_merge($update_params, $this->params);
        return Db::query($sql, $final_params);
    }

    public function delete()
    {
        $sql = "DELETE FROM " . Db::quoteIdentifier($this->table);
        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $whereStr = "";
            foreach ($this->wheres as $i => $w) {
                if ($i > 0 && strpos($w, 'OR ') !== 0) {
                    $whereStr .= " AND ";
                }
                $whereStr .= $w . " ";
            }
            $sql .= trim($whereStr);
        }
        return Db::query($sql, $this->params);
    }

    private function buildSelect()
    {
        $sql = "SELECT {$this->select} FROM " . Db::quoteIdentifier($this->table);
        
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $whereStr = "";
            foreach ($this->wheres as $i => $w) {
                if ($i > 0 && strpos($w, 'OR ') !== 0) {
                    $whereStr .= " AND ";
                }
                $whereStr .= $w . " ";
            }
            $sql .= trim($whereStr);
        }

        if ($this->groupBy) {
            $sql .= " " . $this->groupBy;
        }

        if ($this->orderRaw) {
            $sql .= " " . $this->orderRaw;
        } elseif ($this->order) {
            $sql .= " " . $this->order;
        }

        if ($this->limit) {
            $sql .= " " . $this->limit;
        }

        return $sql;
    }
}
