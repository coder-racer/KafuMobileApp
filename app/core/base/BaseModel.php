<?php

namespace Core\Base;

use Core\App;
use PDO;

class BaseModel
{
    private static $pdo;
    private ?string $table = null;
    private $select = '*';
    private $joins = '';
    private $where = '';
    private $params = [];
    private $whereIn = '';

    public function __construct($table = null)
    {
        self::$pdo = App::getPDO();

        if ($table === null) {
            $fullClassName = get_class($this);
            $classNameParts = explode('\\', $fullClassName);
            $className = end($classNameParts);
            $table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
            $table = str_replace("_model", '', $table);
        }

        $this->table = $table;
    }


    public function select($columns = ['*'])
    {
        $this->select = is_array($columns) ? implode(',', $columns) : $columns;
        return $this;
    }

    public function join($table, $on, $type = '')
    {
        $this->joins .= " $type JOIN $table ON $on";
        return $this;
    }

    public function leftJoin($table, $on)
    {
        return $this->join($table, $on, 'LEFT');
    }

    public function where($column, $operator, $value)
    {
        $this->where .= ($this->where === '' ? ' WHERE ' : ' AND ') . "$column $operator ?";
        $this->params[] = $value;
        return $this;
    }

    public function whereOr($column, $operator, $value)
    {
        $this->where .= " OR $column $operator ?";
        $this->params[] = $value;
        return $this;
    }

    public function whereIn($column, $values)
    {
        $this->whereIn .= " AND $column IN (" . implode(',', array_fill(0, count($values), '?')) . ")";
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    public function like($column, $value)
    {
        $this->where .= ($this->where === '' ? ' WHERE ' : ' AND ') . "$column LIKE ?";
        $this->params[] = $value;
        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy = " ORDER BY $column $direction";
        return $this;
    }

    public function limit($count)
    {
        $this->limit = " LIMIT $count";
        return $this;
    }

    public function offset($count)
    {
        $this->offset = " OFFSET $count";
        return $this;
    }

    public function paginate($perPage, $page)
    {
        $this->limit = " LIMIT $perPage OFFSET " . (($page - 1) * $perPage);
        return $this;
    }

    public function count()
    {
        $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM $this->table $this->joins $this->where");
        $stmt->execute($this->params);
        return $stmt->fetchColumn();
    }

    public function pages($perPage)
    {
        $count = $this->count();
        return ceil($count / $perPage);
    }

    public function insert(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $this->table ($columns) VALUES ($values)";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $this->select = '*';
        $this->joins = '';
        $this->where = '';
        $this->params = [];
        return $stmt->rowCount();
    }

    public function update(array $data)
    {
        $set = '';
        $param =[];
        foreach ($data as $column => $value) {
            $set .= "$column=?, ";
            $param[] = $value;
        }
        $this->params = array_merge($param, $this->params);
        $set = rtrim($set, ', ');
        $sql = "UPDATE $this->table SET $set $this->where $this->whereIn";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($this->params);
        $this->reset();
        return $stmt->rowCount();
    }

    public function get()
    {
        $stmt = self::$pdo->prepare($this->toSql());
        $stmt->execute($this->params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->reset();
        return $result;
    }

    public function first()
    {
        $stmt = self::$pdo->prepare($this->toSql() . " LIMIT 1");
        $stmt->execute($this->params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->reset();
        return $result;
    }

    private function reset()
    {
        $this->select = '*';
        $this->joins = '';
        $this->where = '';
        $this->params = [];
    }

    public function delete()
    {
        $sql = "DELETE FROM $this->table $this->where";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($this->params);
        $this->select = '*';
        $this->joins = '';
        $this->where = '';
        $this->params = [];
        return $stmt->rowCount();
    }

    // Используйте whereIn в вашем методе toSql:
    public function toSql()
    {
        return trim("SELECT $this->select FROM $this->table $this->joins $this->where $this->whereIn $this->orderBy $this->limit $this->offset");
    }
}