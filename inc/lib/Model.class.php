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

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = [];

    public function __construct($attributes = [])
    {
        $this->attributes = (array)$attributes;
    }

    public static function query()
    {
        $instance = new static();
        return Query::table($instance->table);
    }

    public static function all()
    {
        return self::query()->get();
    }

    public static function find($id)
    {
        $instance = new static();
        return self::query()->where($instance->primaryKey, $id)->first();
    }

    public static function where($column, $operator, $value = null)
    {
        return self::query()->where($column, $operator, $value);
    }

    public static function create($data)
    {
        $instance = new static();
        return self::query()->insert($data);
    }

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

    public function destroy()
    {
        if (isset($this->attributes[$this->primaryKey])) {
            return self::query()
                ->where($this->primaryKey, $this->attributes[$this->primaryKey])
                ->delete();
        }
        return false;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }
}
