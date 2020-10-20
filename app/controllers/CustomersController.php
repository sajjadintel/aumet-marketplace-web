<?php

class CustomersController extends Controller
{
    function getOrderCustomersFeedback()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vModule_feedback_title');
            $this->webResponse->data = View::instance()->render('app/entity/feedback/list.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function postOrderCustomersFeedback()
    {
        $query = "";
        $datatable = array_merge(array('pagination' => array(), 'sort' => array(), 'query' => array()), $_REQUEST);

        $sort = !empty($datatable['sort']['sort']) ? $datatable['sort']['sort'] : 'desc';
        $field = !empty($datatable['sort']['field']) ? $datatable['sort']['field'] : 'id';

        $meta = array();
        $page = !empty($datatable['pagination']['page']) ? (int)$datatable['pagination']['page'] : 1;
        $perpage = !empty($datatable['pagination']['perpage']) ? (int)$datatable['pagination']['perpage'] : 10;

        $total = 0;
        $offset = ($page - 1) * $perpage;

        global $dbConnection;

        $dbData = new BaseModel($dbConnection, "vwOrderUserRate");
        $dbData->entityName= "entityName_en";
        $dbData->rateName= "rateName_en";
        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

        $query = "entitySellerId IN ($arrEntityId)";
        $data = [];

        if ($query == "") {
            $total = $dbData->count();
            $data = $dbData->findAll("$field $sort", $perpage, $offset);
        } else {
            $total = $dbData->count($query);
            $data = $dbData->findWhere($query, "$field $sort", $perpage, $offset);
        }

        $pages = 1;

        // $perpage 0; get all data
        if ($perpage > 0) {
            $pages = ceil($total / $perpage); // calculate total pages
            $page = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
            $page = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
            $offset = ($page - 1) * $perpage;
            if ($offset < 0) {
                $offset = 0;
            }

            //$data = array_slice($data, $offset, $perpage, true);
        }

        $meta = array(
            'page' => $page,
            'pages' => $pages,
            'perpage' => $perpage,
            'total' => $total,
        );

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

        $result = array(
            'q' => $query,
            'meta' => $meta + array(
                    'sort' => $sort,
                    'field' => $field,
                ),
            'data' => $data
        );

        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
