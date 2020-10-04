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
        if ($order == "") {
            $this->load(array($where));
        } else if ($limit == 0) {
            $this->load(array($where), array('order' => $order, 'offset' => $offset));
        } else {
            $this->load(array($where), array('order' => $order, 'limit' => $limit, 'offset' => $offset));
        }
        return $this->query;
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
            $this->erase();
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
