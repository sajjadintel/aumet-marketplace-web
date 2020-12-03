<?php
class Datatable
{
    public $draw, $offset, $limit, $columnIndex, $sortBy, $sortByOrder, $query;

    function __construct($request)
    {
        $this->draw = $request['draw'];
        $this->offset = $request['start'];
        $this->limit = $request['length']; // Rows display per page
        $this->columnIndex = $request['order'][0]['column']; // Column index
        $this->sortBy = $request['columns'][$this->columnIndex]['data']; // Column name
        $this->sortByOrder = $request['order'][0]['dir']; // asc or desc
        $this->query = $request['query']; // custom data for filtering

    }
}
