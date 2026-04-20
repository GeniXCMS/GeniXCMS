<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 1.1.0
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
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
    private $limit = null;
    private $offset = null;
    private $joins = [];
    private $groupBy = '';

    /**
     * Query Constructor.
     *
     * @param string|null $table The database table to query.
     */
    public function __construct($table = null)
    {
        $this->table = $table;
    }

    /**
     * Static factory to begin a fluent query builder chain.
     *
     * @param string $table Target table name.
     * @return Query        New instance of the query builder.
     */
    public static function table($table)
    {
        return new self($table);
    }

    /**
     * Set the columns to be selected.
     *
     * @param string|array $columns Column name or array of column names.
     * @return $this
     */
    public function select($columns = '*')
    {
        $this->select = is_array($columns) ? implode(', ', array_map([Db::class, 'quoteIdentifier'], $columns)) : $columns;
        return $this;
    }

    /**
     * Set the query to return distinct results.
     *
     * @return $this
     */
    public function distinct()
    {
        if (strpos(strtoupper($this->select), 'DISTINCT') !== 0) {
            $this->select = "DISTINCT " . $this->select;
        }
        return $this;
    }

    /**
     * Adds a basic WHERE clause with parameter binding.
     * Supports both two-argument (column, value) and three-argument (column, operator, value) usage.
     *
     * @param string $column   Column name.
     * @param string $operator Selection operator or value.
     * @param mixed  $value    Value to compare (if operator provided).
     * @return $this
     */
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

    /**
     * Adds an OR WHERE clause with parameter binding.
     *
     * @param string $column   Column name.
     * @param string $operator Selection operator or value.
     * @param mixed  $value    Value to compare (if operator provided).
     * @return $this
     */
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

    /**
     * Adds a grouped WHERE clause using a closure.
     *
     * @param callable $callback A closure that receives a sub-query instance.
     * @return $this
     */
    public function groupWhere(callable $callback)
    {
        $subQuery = new self();
        $callback($subQuery);

        if (!empty($subQuery->wheres)) {
            $whereStr = "";
            foreach ($subQuery->wheres as $i => $w) {
                if ($i > 0 && strpos($w, 'OR ') !== 0) {
                    $whereStr .= " AND ";
                }
                $whereStr .= $w . " ";
            }
            $this->wheres[] = "(" . trim($whereStr) . ")";
            $this->params = array_merge($this->params, $subQuery->params);
        }

        return $this;
    }

    /**
     * Adds a grouped OR WHERE clause using a closure.
     *
     * @param callable $callback A closure that receives a sub-query instance.
     * @return $this
     */
    public function orGroupWhere(callable $callback)
    {
        $subQuery = new self();
        $callback($subQuery);

        if (!empty($subQuery->wheres)) {
            $whereStr = "";
            foreach ($subQuery->wheres as $i => $w) {
                if ($i > 0 && strpos($w, 'OR ') !== 0) {
                    $whereStr .= " AND ";
                }
                $whereStr .= $w . " ";
            }
            $this->wheres[] = "OR (" . trim($whereStr) . ")";
            $this->params = array_merge($this->params, $subQuery->params);
        }

        return $this;
    }


    /**
     * Adds a WHERE IN clause.
     *
     * @param string $column Column name.
     * @param array  $values List of values.
     * @return $this
     */
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

    /**
     * Adds a raw WHERE clause with optional bindings.
     *
     * @param string $sql      Raw SQL where fragment.
     * @param array  $bindings List of parameter values.
     * @return $this
     */
    public function whereRaw($sql, array $bindings = [])
    {
        $this->wheres[] = $sql;
        foreach ($bindings as $binding) {
            $this->params[] = $binding;
        }
        return $this;
    }

    /**
     * Adds a JOIN clause to the query.
     *
     * @param string $table    Table to join.
     * @param string $first    Left-side column for the ON condition.
     * @param string $operator Comparison operator (e.g. '=', '!=').
     * @param string $second   Right-side column for the ON condition.
     * @param string $type     Join type (INNER, LEFT, RIGHT).
     * @return $this
     */
    public function join($table, $first, $operator, $second, $type = 'INNER')
    {
        $this->joins[] = "$type JOIN " . $table . " ON " . $first . " $operator " . $second;
        return $this;
    }

    /**
     * Adds a LEFT JOIN clause.
     *
     * @param string $table    Table to join.
     * @param string $first    Left-side column.
     * @param string $operator Comparison operator.
     * @param string $second   Right-side column.
     * @return $this
     */
    public function leftJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Adds a RIGHT JOIN clause.
     *
     * @param string $table    Table to join.
     * @param string $first    Left-side column.
     * @param string $operator Comparison operator.
     * @param string $second   Right-side column.
     * @return $this
     */
    public function rightJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    /**
     * Adds an ORDER BY clause.
     *
     * @param string $column    Column to sort by.
     * @param string $direction Sort direction (ASC or DESC).
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->order = "ORDER BY " . Db::quoteIdentifier($column) . " " . strtoupper($direction);
        return $this;
    }

    /**
     * Adds an ORDER BY DESC clause.
     *
     * @param string $column Column to sort by.
     * @return $this
     */
    public function desc($column)
    {
        return $this->orderBy($column, 'DESC');
    }

    /**
     * Adds an ORDER BY ASC clause.
     *
     * @param string $column Column to sort by.
     * @return $this
     */
    public function asc($column)
    {
        return $this->orderBy($column, 'ASC');
    }

    /**
     * Adds a raw ORDER BY clause.
     *
     * @param string $expression Raw SQL ordering expression.
     * @return $this
     */
    public function orderByRaw($expression)
    {
        $this->orderRaw = "ORDER BY " . $expression;
        return $this;
    }

    /**
     * Adds LIMIT and OFFSET clauses.
     *
     * @param int      $limit  Maximum number of records to return.
     * @param int|null $offset Offset to start from.
     * @return $this
     */
    public function limit($limit, $offset = null)
    {
        $this->limit = (int) $limit;
        if ($offset !== null) {
            $this->offset((int) $offset);
        }
        return $this;
    }

    /**
     * Adds an OFFSET clause.
     *
     * @param int $offset Offset to start from.
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = (int) $offset;
        return $this;
    }

    /**
     * Adds a GROUP BY clause.
     *
     * @param string $column Column to group by.
     * @return $this
     */
    public function groupBy($column)
    {
        $this->groupBy = "GROUP BY " . Db::quoteIdentifier($column);
        return $this;
    }

    /**
     * Executes the SELECT query and returns the results.
     *
     * @return array List of result records as objects.
     */
    public function get()
    {
        $sql = $this->buildSelect();
        return Db::result($sql, $this->params);
    }

    /**
     * Executes the query and returns the first result record.
     *
     * @return object|null Result object or null if no records found.
     */
    public function first()
    {
        $this->limit(1);
        $res = $this->get();
        return (isset($res[0]) && is_object($res[0])) ? $res[0] : null;
    }

    /**
     * Returns the total count of matching records.
     *
     * @return int Total count.
     */
    public function count($column = '*')
    {
        $oldSelect = $this->select;
        $oldLimit = $this->limit;
        $oldOffset = $this->offset;

        // Handle distinct count if select already has DISTINCT
        if (strpos(strtoupper($this->select), 'DISTINCT') === 0 && $column === '*') {
            $col = trim(str_ireplace('DISTINCT', '', $this->select));
            $this->select = "COUNT(DISTINCT {$col}) as total";
        } else {
            $this->select = "COUNT({$column}) as total";
        }

        $this->limit = null;
        $this->offset = null;

        $res = $this->first();
        
        $this->select = $oldSelect;
        $this->limit = $oldLimit;
        $this->offset = $oldOffset;

        return $res ? (int) $res->total : 0;
    }

    /**
     * Returns the sum of a specific column.
     *
     * @param string $column Column to aggregate.
     * @return float          The sum result.
     */
    public function sum($column)
    {
        $oldSelect = $this->select;
        $this->select = "SUM(" . Db::quoteIdentifier($column) . ") as total";
        $res = $this->first();
        $this->select = $oldSelect;
        return $res ? (float) $res->total : 0;
    }

    /**
     * Inserts data into the query table.
     *
     * @param array $data Associative array of column => values.
     * @return bool|int    Result of the insertion.
     */
    public function insert($data)
    {
        $vars = [
            'table' => $this->table,
            'key' => $data
        ];
        return Db::insert($vars);
    }

    /**
     * Updates matching records in the query table.
     * Maps the fluent WHERE clauses to the query execution.
     *
     * @param array $data Associative array of column => values for the SET clause.
     * @return bool|int    Result of the update.
     */
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

    /**
     * Deletes matching records from the query table.
     *
     * @return bool|int Result of the deletion.
     */
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

    /**
     * Internal helper to construct the final SELECT statement string.
     *
     * @return string Fully structured SQL query.
     */
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

        if ($this->limit !== null) {
            $sql .= " LIMIT " . (int) $this->limit;
            if ($this->offset !== null) {
                $sql .= " OFFSET " . (int) $this->offset;
            }
        }

        return $sql;
    }
}
