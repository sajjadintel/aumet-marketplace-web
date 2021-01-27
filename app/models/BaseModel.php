<?php

class BaseModel extends DB\SQL\Mapper
{

    private $table_name;
    private $exception;

    public function __construct(DB\SQL $db, $table_name)
    {
        $this->table_name = $table_name;
        parent::__construct($db, $table_name);
    }

    public static function getTableNames(DB\SQL $db)
    {
        $query = "SELECT table_name FROM information_schema.tables where table_type='BASE TABLE' and table_schema='" . $db->name() . "'";
        return $db->exec($query);
    }

    public static function getTablesAndViews(DB\SQL $db)
    {
        $query = "SELECT table_name, table_type FROM information_schema.tables where table_schema='" . $db->name() . "'";
        return $db->exec($query);
    }

    public function all($order = false, $limit = 0, $offset = 0)
    {
        if (!$order && $limit == 0) {
            $this->load();
        } else if ($order && $limit == 0) {
            $this->load(array(), array('order' => $order, 'offset' => $offset));
        } else if (!$order && $limit >= 0) {
            $this->load(array(), array('limit' => $limit, 'offset' => $offset));
        } else {
            $this->load(array(), array('order' => $order, 'limit' => $limit, 'offset' => $offset));
        }

        return $this->query;
    }

    public function findAll($order = false, $limit = 0, $offset = 0)
    {
        $result = null;

        if (!$order && $limit == 0) {
            $result = $this->find();
        } else if ($order && $limit == 0) {
            $result = $this->find(array(), array('order' => $order, 'offset' => $offset));
        } else if (!$order && $limit >= 0) {
            $result = $this->find(array(), array('limit' => $limit, 'offset' => $offset));
        } else {
            $result = $this->find(array(), array('order' => $order, 'limit' => $limit, 'offset' => $offset));
        }
        $result = array_map(array($this, 'cast'), $result);

        return $result;
    }

    public function getFullSchema()
    {

        return $this->schema();
    }

    public function getContraints()
    {

        $query = "SELECT COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME 
           FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
           WHERE TABLE_SCHEMA = 'gsr_ae' AND TABLE_NAME = '$this->table_name'";

        return $this->db->exec($query);
    }

    public function getById($value)
    {
        $this->load(array("id=?", $value));
        return $this->query;
    }

    public function getByField($name, $value, $order = false)
    {
        if ($order) {
            $this->load(array("$name=?", $value), array('order' => $order));
        } else {
            $this->load(array("$name=?", $value));
        }

        return $this->query;
    }

    public function getWhere($where, $order = "", $limit = 0, $offset = 0)
    {
        if (!is_array($where)) {
            $where = array($where);
        }
        if ($order == "") {
            $this->load($where);
        } else if ($limit == 0) {
            $this->load($where, array('order' => $order, 'offset' => $offset));
        } else {
            $this->load($where, array('order' => $order, 'limit' => $limit, 'offset' => $offset));
        }
        return $this->query;
    }

    public function findWhere($where, $order = "", $limit = 0, $offset = 0)
    {
        $result = null;
        if (!is_array($where)) {
            $where = array($where);
        }
        if ($order == "") {
            $result = $this->find($where);
        } else if ($limit == 0) {
            $result = $this->find($where, array('order' => $order, 'offset' => $offset));
        } else {
            $result = $this->find($where, array('order' => $order, 'limit' => $limit, 'offset' => $offset));
        }
        $result = array_map(array($this, 'cast'), $result);

        return $result;
    }

    public function getCurrentDateTime()
    {
        return date('Y-m-d H:i:s');
    }

    public function addReturnID()
    {
        try {
            $this->insert();
            $this->id = $this->get('_id');
            return TRUE;
        } catch (Exception $ex) {
            $this->exception = $ex->getMessage() . " - " . $ex->getTraceAsString();
            return false;
        }
    }

    public function add()
    {
        try {
            $this->insert();
            $this->id = $this->get('_id');
            return TRUE;
        } catch (Exception $ex) {
            $this->exception = $ex->getMessage() . " - " . $ex->getTraceAsString();
            return false;
        }
    }

    public function edit()
    {
        try {
            $this->update();
            return TRUE;
        } catch (Exception $ex) {
            $this->exception = $ex->getMessage() . " - " . $ex->getTraceAsString();
            return false;
        }
    }

    public function delete()
    {
        try {
            if (isset($this->isActive)) {
                if ($this->isActive == 0) {
                    $this->exception = "Already deleted";
                    return false;
                }
                $this->isActive = 0;
                $this->update();
            } else {
                $this->erase();
            }
            return TRUE;
        } catch (Exception $ex) {
            $this->exception = $ex->getMessage() . " - " . $ex->getTraceAsString();
            return false;
        }
    }

    public static function escapeMySQL($inp)
    {
        if (is_array($inp))
            return array_map(__METHOD__, $inp);

        if (!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }
}
