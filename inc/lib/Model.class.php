<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.0.0
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = [];

    /**
     * Model Constructor.
     *
     * @param array $attributes Initial mapping of column names to values.
     */
    public function __construct($attributes = [])
    {
        $this->attributes = (array) $attributes;
    }

    /**
     * Initializes a new Query builder instance for the model's table.
     *
     * @return Query builder instance.
     */
    public static function query()
    {
        $instance = new static();
        return Query::table($instance->table);
    }

    /**
     * Retrieves all records from the model's table.
     *
     * @return array Array of model objects/database rows.
     */
    public static function all()
    {
        return self::query()->get();
    }

    /**
     * Finds a single record by its primary key.
     *
     * @param mixed $id The primary key value.
     * @return object|null Database row object or null if not found.
     */
    public static function find($id)
    {
        $instance = new static();
        return self::query()->where($instance->primaryKey, $id)->first();
    }

    /**
     * Adds a where clause to the model's query.
     *
     * @param string $column   Column name.
     * @param string $operator Comparison operator or value if three params aren't used.
     * @param mixed  $value    Value to compare (optional).
     * @return Query           The query builder instance.
     */
    public static function where($column, $operator, $value = null)
    {
        return self::query()->where($column, $operator, $value);
    }

    /**
     * Creates a new record in the database.
     *
     * @param array $data Data to insert.
     * @return bool|int   Result of the insertion.
     */
    public static function create($data)
    {
        $instance = new static();
        return self::query()->insert($data);
    }

    /**
     * Saves the current model state to the database.
     * Perfroms an update if ID exists, otherwise performs an insert.
     *
     * @return bool|int Result of the save operation.
     */
    public function save()
    {
        if (isset($this->attributes[$this->primaryKey])) {
            return self::query()
                ->where($this->primaryKey, $this->attributes[$this->primaryKey])
                ->update($this->attributes);
        } else {
            return self::query()->insert($this->attributes);
        }
    }

    /**
     * Deletes the current model record from the database.
     *
     * @return bool Result of the deletion.
     */
    public function destroy()
    {
        if (isset($this->attributes[$this->primaryKey])) {
            return self::query()
                ->where($this->primaryKey, $this->attributes[$this->primaryKey])
                ->delete();
        }
        return false;
    }

    /**
     * Magic getter for model attributes.
     *
     * @param string $name Attribute name.
     * @return mixed       Attribute value or null.
     */
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Magic setter for model attributes.
     *
     * @param string $name  Attribute name.
     * @param mixed  $value Attribute value.
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }
}
