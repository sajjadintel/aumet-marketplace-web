<?php

class ProductsController extends Controller
{

    function getEntityProduct()
    {

        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $entityId = $this->f3->get('PARAMS.entityId');
            $productId = $this->f3->get('PARAMS.productId');

            global $dbConnection;

            $dbEntityProduct = new BaseModel($dbConnection, "vwEntityProductSell");
            $dbEntityProduct->getWhere("entityId=$entityId and productId=$productId");
            $this->f3->set('objEntityProduct', $dbEntityProduct);

            $dbEntityProductRelated = new BaseModel($dbConnection, "vwEntityProductSell");
            $arrRelatedEntityProduct = $dbEntityProductRelated->getWhere("stockStatusId=1 and scientificNameId =$dbEntityProduct->scientificNameId and id != $dbEntityProduct->id");
            $this->f3->set('arrRelatedEntityProduct', $arrRelatedEntityProduct);

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vTitle_entityProductDetail');
            $this->webResponse->data = View::instance()->render('app/products/single/entityProduct.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getDistributorProducts()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $arrStockStatus = $dbStockStatus->findAll("id asc");
            $this->f3->set('arrStockStatus', $arrStockStatus);

            $dbScientificName = new BaseModel($this->db, "scientificName");
            $arrScientificName = $dbScientificName->findAll();


            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vModule_product_title');
            $this->webResponse->data = View::instance()->render('app/products/distributor/products.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getProductDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $productId = $this->f3->get('PARAMS.productId');

            $dbProduct = new BaseModel($this->db, "vwEntityProductSell");
            //$dbProduct->getByField("productId", $productId);
            //$this->f3->set("objProduct", $dbProduct);
            $arrProduct = $dbProduct->findWhere("productId = $productId");

            $data['product'] = $arrProduct[0];

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);

            //$this->webResponse->errorCode = 1;
            //$this->webResponse->title = $this->f3->get('vModule_feedback_title');
            //$this->webResponse->data = View::instance()->render('app/products/distributor/modals/edit.php');
            //echo $this->webResponse->jsonResponse();
        }
    }

    function getProductQuantityDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $productId = $this->f3->get('PARAMS.productId');

            $dbProduct = new BaseModel($this->db, "vwEntityProductSell");
            $arrProduct = $dbProduct->findWhere("productId = '$productId'");

            $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
            $dbBonus->bonusId = 'id';
            $arrBonus = $dbBonus->findWhere("entityProductId = '$productId' AND isActive = 1");

            $data['product'] = $arrProduct[0];
            $data['bonus'] = $arrBonus;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
        }
    }

    function postDistributorProducts()
    {
        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entityId IN ($arrEntityId)";

        $datatable = array_merge(array('pagination' => array(), 'sort' => array(), 'query' => array()), $_REQUEST);

        if ($datatable['query'] != "") {

            $productId = $datatable['query']['productId'];
            if (isset($productId)) {
                if (is_array($productId)) {
                    $query .= " AND id in (" . implode(",", $productId) . ")";
                } else {
                    $query .= " AND id = $productId";
                }
            }

            $scientificNameId = $datatable['query']['scientificNameId'];
            if (isset($scientificNameId)) {
                if (is_array($scientificNameId)) {
                    $query .= " AND scientificNameId in (" . implode(",", $scientificNameId) . ")";
                } else {
                    $query .= " AND scientificNameId = $scientificNameId";
                }
            }

            $entityId = $datatable['query']['entityId'];
            if (isset($entityId)) {
                if (is_array($entityId)) {
                    $query .= " AND entityId in (" . implode(",", $entityId) . ")";
                } else {
                    $query .= " AND entityId = $entityId";
                }
            }

            if ($datatable['query']['stockOption'] == 1) {
                $query .= " AND stockStatusId=1";
            }
        }

        $sort = !empty($datatable['sort']['sort']) ? $datatable['sort']['sort'] : 'asc';
        $field = !empty($datatable['sort']['field']) ? $datatable['sort']['field'] : 'id';

        $meta = array();
        $page = !empty($datatable['pagination']['page']) ? (int)$datatable['pagination']['page'] : 1;
        $perpage = !empty($datatable['pagination']['perpage']) ? (int)$datatable['pagination']['perpage'] : 10;

        $offset = ($page - 1) * $perpage;
        $total = 0;

        $dbProducts = new BaseModel($this->db, "vwEntityProductSell");

        if (!$dbProducts->exists($field)) {
            $field = 'id';
        }
        if ($query == "") {
            $total = $dbProducts->count();
            $data = $dbProducts->findAll("$field $sort", $perpage, $offset);
        } else {
            $total = $dbProducts->count($query);
            $data = $dbProducts->findWhere($query, "$field $sort", $perpage, $offset);
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

    function postDistributorProductsBestSelling()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entityId IN ($arrEntityId)";
        $meta = array();
        $dbProducts = new BaseModel($this->db, "vwEntityProductSell");

        $data = [];

        $totalRecords = $dbProducts->count($query);
        $totalFiltered = 5;
        $data = $dbProducts->findWhere($query, "quantityOrdered DESC", 5, 0);

        ## Response
        $response = array(
            "draw" => intval($datatable->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        );

        $this->jsonResponseAPI($response);
    }

    function postEditDistributorProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $id = $this->f3->get('POST.id');

            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->getWhere("productId=$id");

            $dbProduct = new BaseModel($this->db, "product");
            $dbProduct->getWhere("id=$id");

            if ($dbEntityProduct->dry() || $dbProduct->dry()) {
                $this->webResponse->errorCode = 2;
                $this->webResponse->title = "";
                $this->webResponse->message = "No Product";
                echo $this->webResponse->jsonResponse();
            } else {
                $unitPrice = $this->f3->get('POST.unitPrice');

                $dbEntityProduct->unitPrice = $unitPrice;

                $dbEntityProduct->update();

                $scientificNameId = $this->f3->get('POST.scientificNameId');
                $madeInCountryId = $this->f3->get('POST.madeInCountryId');
                $name_en = $this->f3->get('POST.name_en');
                $name_ar = $this->f3->get('POST.name_ar');
                $name_fr = $this->f3->get('POST.name_fr');

                $dbProduct->name_en = $name_en;
                $dbProduct->name_fr = $name_fr;
                $dbProduct->name_ar = $name_ar;
                $dbProduct->scientificNameId = $scientificNameId;
                $dbProduct->madeInCountryId = $madeInCountryId;

                $dbProduct->update();

                $this->webResponse->errorCode = 1;
                $this->webResponse->title = "";
                $this->webResponse->data = $dbProduct->name_ar;
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postEditQuantityDistributorProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $id = $this->f3->get('POST.id');

            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->getWhere("productId=$id");

            $dbProduct = new BaseModel($this->db, "product");
            $dbProduct->getWhere("id=$id");

            if ($dbEntityProduct->dry() || $dbProduct->dry()) {
                $this->webResponse->errorCode = 2;
                $this->webResponse->title = "";
                $this->webResponse->message = "No Product";
                echo $this->webResponse->jsonResponse();
            } else {
                $stock = $this->f3->get('POST.stock');
                $stockStatusId = $this->f3->get('POST.stockStatus');
                $bonusTypeId = $this->f3->get('POST.bonusType');
                $bonusRepeater = $this->f3->get('POST.bonusRepeater');

                if ($stock > 0) {
                    $stockStatusId = 1;
                } else {
                    if (isset($stockStatusId) && $stockStatusId == 'on') {
                        $stockStatusId = 3;
                    } else {
                        $stockStatusId = 2;
                    }
                }

                if (isset($bonusTypeId) && $bonusTypeId == 'on') {
                    $bonusTypeId = 2;
                } else {
                    $bonusTypeId = 1;
                }

                $dbEntityProduct->stock = $stock;
                $dbEntityProduct->stockStatusId = $stockStatusId;
                $dbEntityProduct->bonusTypeId = $bonusTypeId;
                $dbEntityProduct->stockUpdateDateTime = $dbEntityProduct->getCurrentDateTime();

                $dbEntityProduct->update();

                if ($bonusTypeId != 1) {
                    $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
                    $dbBonus->load(array("entityProductId = $id AND isActive = 1"));
                    while (!$dbBonus->dry()) {
                        $dbBonus->delete();
                        $dbBonus->next();
                    }

                    foreach ($bonusRepeater as $bonus) {
                        $dbBonus->reset();
                        if ($bonus['minOrder'] != '' && $bonus['bonus'] != '') {
                            $dbBonus->entityProductId = $id;
                            $dbBonus->minOrder = $bonus['minOrder'];
                            $dbBonus->bonus = $bonus['bonus'];
                            $dbBonus->add();
                        }
                    }
                }

                $this->webResponse->errorCode = 1;
                $this->webResponse->title = "";
                $this->webResponse->data = $bonusRepeater;
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postAddDistributorProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $scientificNameId = $this->f3->get('POST.scientificNameId');
            $madeInCountryId = $this->f3->get('POST.madeInCountryId');
            $name_en = $this->f3->get('POST.name_en');
            $name_ar = $this->f3->get('POST.name_ar');
            $name_fr = $this->f3->get('POST.name_fr');
            $image = $this->f3->get('POST.image');


            $dbProduct = new BaseModel($this->db, "product");
            $dbProduct->scientificNameId = $scientificNameId;
            $dbProduct->madeInCountryId = $madeInCountryId;
            $dbProduct->name_en = $name_en;
            $dbProduct->name_fr = $name_fr;
            $dbProduct->name_ar = $name_ar;
            $dbProduct->image = $image;

            $dbProduct->addReturnID();
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $entityId = $arrEntityId;
            $unitPrice = $this->f3->get('POST.unitPrice');
            $stock = $this->f3->get('POST.stock');


            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->productId = $dbProduct->id;
            $dbEntityProduct->entityId = $entityId;
            $dbEntityProduct->unitPrice = $unitPrice;
            $dbEntityProduct->stock = $stock;
            $dbEntityProduct->stockStatusId = 1;
            $dbEntityProduct->bonusTypeId = 1;
            $dbEntityProduct->stockUpdateDateTime = $dbEntityProduct->getCurrentDateTime();

            $dbEntityProduct->add();

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = "";
            $this->webResponse->data = $dbProduct['name_' . $this->objUser->language];
            echo $this->webResponse->jsonResponse();
        }
    }

    function getStockUpload()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = 1;
            $this->webResponse->title = "Stock Update"; //$this->f3->get('vModule_stock_title');
            $this->webResponse->data = View::instance()->render('app/products/stock/upload.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getStockDownload()
    {
        if ($this->f3->ajax()) {
            ini_set('max_execution_time', 600);

            // Get all related products
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $query = "entityId IN ($arrEntityId)";
            $dbProducts = new BaseModel($this->db, "vwEntityProductSell");
            $allProducts = $dbProducts->findWhere($query);

            // Setup excel sheet
            $sheetnameUserInput = 'User Input';
            $sheetnameDatabaseInput = 'Database Input';
            $sheetnameVariables = 'Variables';

            // Prepare data for variables sheet
            $arrProducts = [
                ['Name', 'Value']
            ];
            $arrStockAvailability = [
                ['Name', 'Value']
            ];

            $mapProductIdName = [];
            $productsNum = 2;
            $nameField = "productName_" . $this->objUser->language;
            foreach ($allProducts as $product) {
                $products_num++;
                $arrProducts[] = array($product[$nameField], $product['productId']);
                $mapProductIdName[$product['productId']] = $product[$nameField];
            }

            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $allStockStatus = $dbStockStatus->findAll("id asc");

            $mapStockIdName = [];
            $stock_availability_num = 2;
            foreach ($allStockStatus as $stockStatus) {
                $stock_availability_num++;
                $arrStockAvailability[] = array($stockStatus['name'], $stockStatus['id']);
                $mapStockIdName[$stockStatus['id']] = $stockStatus['name'];
            }

            $sampleFilePath = $this->getRootDirectory() . '\app\files\samples\products-stock-sample.xlsx';
            $spreadsheet = Excel::loadFile($sampleFilePath);

            // Change active sheet to variables
            $sheet = $spreadsheet->setActiveSheetIndex(2);

            // Set products and stock availability in excel
            $sheet->fromArray($arrProducts, NULL, 'A2', true);
            $sheet->fromArray($arrStockAvailability, NULL, 'D2', true);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            // Set validation and formula 
            Excel::setCellFormulaVLookup($sheet, 'A3', count($allProducts), "'User Input'!A", 'Variables!$A$3:$B$' . $products_num);
            Excel::setCellFormulaVLookup($sheet, 'D3', count($allStockStatus), "'User Input'!D", 'Variables!$D$3:$E$' . $stock_availability_num);

            // Hide database and variables sheet
            Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
            Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

            // Change active sheet to user input
            $sheet = $spreadsheet->setActiveSheetIndex(0);

            // Set data validation for products and stock availability
            Excel::setDataValidation($sheet, 'A3', 'A' . count($allProducts), 'TYPE_LIST', 'Variables!$A$3:$A$' . $products_num);
            Excel::setDataValidation($sheet, 'D3', 'D' . count($allProducts), 'TYPE_LIST', 'Variables!$D$3:$D$' . $stock_availability_num);

            // Add all products to multidimensional array 
            $multiProducts = [];
            $fields = [
                "productId",
                "unitPrice",
                "vat",
                "stockStatusId",
                "stock",
                "expiryDate"
            ];
            $i = 3;
            foreach ($allProducts as $product) {
                $singleProduct = [];
                foreach ($fields as $field) {
                    if ($field == "productId") {
                        $cellValue = $mapProductIdName[$product[$field]];
                    } else if ($field == "stockStatusId") {
                        $cellValue = $mapStockIdName[$product[$field]];
                    } else {
                        $cellValue = $product[$field];
                    }
                    array_push($singleProduct, $cellValue);
                }
                array_push($multiProducts, $singleProduct);
                $i++;
            }

            // Fill rows with products
            $sheet->fromArray($multiProducts, NULL, 'A3', true);

            // Create excel sheet
            $productsSheetUrl = "files/downloads/reports/products-stock/products-stock-" . $this->objUser->id . "-" . time() . ".xlsx";
            Excel::saveSpreadsheetToPath($spreadsheet, $productsSheetUrl);

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = "Stock Download";
            $this->webResponse->data = "/" . $productsSheetUrl;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postStockUpload()
    {
        $fileName = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_FILENAME);
        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);
        // basename($_FILES["file"]["name"])

        $targetFile = $this->getUploadDirectory() . "reports/products-stock/" . $this->objUser->id . "-" . $fileName . "-" . time() . ".$ext";

        if ($ext == "xlsx" || $ext == "xls" || $ext == "csv") {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                global $dbConnection;
                $dbStockUpdateUpload = new BaseModel($dbConnection, "stockUpdateUpload");
                $dbStockUpdateUpload->userId = $this->objUser->id;
                $dbStockUpdateUpload->filePath = $targetFile;
                $dbStockUpdateUpload->entityId = $this->objUser->entityId;
                $dbStockUpdateUpload->addReturnID();
                echo "OK";
            }
        }
    }

    function postStockUploadProcess()
    {
        ini_set('max_execution_time', 600);
        ini_set('mysql.connect_timeout', 600);

        global $dbConnection;

        $dbStockUpdateUpload = new BaseModel($dbConnection, "stockUpdateUpload");

        $dbStockUpdateUpload->getByField("userId", $this->objUser->id, "insertDateTime desc");

        // $inputFileType = Excel::identifyFileType($dbStockUpdateUpload->filePath);
        try {
            // $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            // $spreadsheet = $reader->load($dbStockUpdateUpload->filePath);
            $spreadsheet = Excel::loadFile($dbStockUpdateUpload->filePath);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $dbEntityProductSell = new BaseModel($this->db, "entityProductSell");

            // Get all related products
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $query = "entityId IN ($arrEntityId)";
            $dbProducts = new BaseModel($this->db, "vwEntityProductSell");
            $allProducts = $dbProducts->findWhere($query);

            // Get all stock status
            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $allStockStatus = $dbStockStatus->findAll("id asc");
            $allStockStatusId = [];
            foreach ($allStockStatus as $stockStatus) {
                array_push($allStockStatusId, $stockStatus['id']);
            }

            $fields = [
                "A" => "productId",
                "B" => "unitPrice",
                "C" => "vat",
                "D" => "stockStatusId",
                "E" => "stock",
                "F" => "expiryDate"
            ];

            $successProducts = [];
            $failedProducts = [];

            $allErrors = [];

            $dbStockUpdateUpload->recordsCount = 0;
            $successRecords = 0;
            $failedRecords = 0;
            $unchangedRecords = 0;

            $firstRow = true;
            $secondRow = false;
            $finished = false;
            foreach ($sheet->getRowIterator() as $row) {
                $product = [];

                if ($firstRow) {
                    $firstRow = false;
                    $secondRow = true;
                    continue;
                } else if ($secondRow) {
                    $secondRow = false;
                    continue;
                }

                $dbStockUpdateUpload->recordsCount++;
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);

                $errors = [];

                $fieldsChanged = false;
                $stockFieldsChanged = false;
                foreach ($cellIterator as $cell) {
                    $cellLetter = $cell->getColumn();
                    $cellValue = $cell->getCalculatedValue();

                    array_push($product, $cellValue);

                    switch ($cellLetter) {
                        case "A":
                            if (!is_numeric($cellValue)) {
                                $finished = true;
                                break;
                            } else {
                                $dbEntityProductSell->getWhere("productId=$cellValue and entityId IN ($arrEntityId)");
                                if ($dbEntityProductSell->dry()) {
                                    array_push($errors, "Product not found");
                                }
                            }
                            break;
                        case "B":
                            if (!is_numeric($cellValue) || (float) $cellValue < 0) {
                                array_push($errors, "Price must be a positive number");
                            } else {
                                $unitPrice = round((float) $cellValue, 2);
                                if ($dbEntityProductSell->unitPrice != $unitPrice) {
                                    $fieldsChanged = true;
                                    $dbEntityProductSell->unitPrice = $unitPrice;
                                }
                            }
                            break;
                        case "C":
                            if (!is_numeric($cellValue) || (float) $cellValue < 0) {
                                array_push($errors, "VAT must be a positive number");
                            } else {
                                $vat = round((float) $cellValue, 2);
                                if ($dbEntityProductSell->vat != $vat) {
                                    $fieldsChanged = true;
                                    $dbEntityProductSell->vat = $vat;
                                }
                            }
                            break;
                        case "D":
                            if (!in_array($cellValue, $allStockStatusId)) {
                                array_push($errors, "Stock Availability invalid");
                            } else {
                                $stockStatusId = $cellValue;
                                if ($dbEntityProductSell->stockStatusId != $stockStatusId) {
                                    $stockFieldsChanged = true;
                                    $dbEntityProductSell->stockStatusId = $stockStatusId;
                                }
                            }
                            break;
                        case "E":
                            if (!filter_var($cellValue, FILTER_VALIDATE_INT) || (float) $cellValue < 0) {
                                array_push($errors, "Stock Quantity must be a positive whole number");
                            } else {
                                $stock = (int) $cellValue;
                                if ($dbEntityProductSell->stock != $stock) {
                                    $stockFieldsChanged = true;
                                    $dbEntityProductSell->stock = $stock;
                                }
                            }
                            break;
                        case "F":
                            if (!is_null($cellValue)) {
                                if (!filter_var($cellValue, FILTER_VALIDATE_INT)) {
                                    array_push($errors, "Expiry Date must fit a date format (YYYY-MM-DD)");
                                } else {
                                    $expiryDate = Excel::excelDateToRegularDate($cellValue);
                                    if ($dbEntityProductSell->expiryDate != $expiryDate) {
                                        $fieldsChanged = true;
                                        $dbEntityProductSell->expiryDate = $expiryDate;
                                    }
                                }
                            }
                            break;
                    }
                }

                if ($finished) {
                    break;
                }

                if (!$dbEntityProductSell->dry() && ($fieldsChanged || $stockFieldsChanged) && count($errors) === 0) {
                    $currentDate = date("Y-m-d H:i:s");
                    if ($fieldsChanged) {
                        $dbEntityProductSell->updateDateTime = $currentDate;
                    }

                    if ($stockFieldsChanged) {
                        $dbEntityProductSell->stockUpdateDateTime = $currentDate;
                    }

                    array_push($successProducts, $product);

                    $dbEntityProductSell->update();
                    $successRecords++;
                } else if (count($errors) > 0) {
                    array_push($failedProducts, $product);
                    array_push($allErrors, $errors);
                    $failedRecords++;
                } else {
                    $unchangedRecords++;
                }

                $dbEntityProductSell->reset();
            }

            if (count($failedProducts) > 0) {
                // Setup excel sheet
                $sheetnameUserInput = 'User Input';
                $sheetnameDatabaseInput = 'Database Input';
                $sheetnameVariables = 'Variables';

                // Prepare data for variables sheet
                $arrProducts = [
                    ['Name', 'Value']
                ];
                $arrStockAvailability = [
                    ['Name', 'Value']
                ];

                $mapProductIdName = [];
                $productsNum = 2;
                $nameField = "productName_" . $this->objUser->language;
                foreach ($allProducts as $product) {
                    $products_num++;
                    $arrProducts[] = array($product[$nameField], $product['productId']);
                    $mapProductIdName[$product['productId']] = $product[$nameField];
                }

                $mapStockIdName = [];
                $stock_availability_num = 2;
                foreach ($allStockStatus as $stockStatus) {
                    $stock_availability_num++;
                    $arrStockAvailability[] = array($stockStatus['name'], $stockStatus['id']);
                    $mapStockIdName[$stockStatus['id']] = $stockStatus['name'];
                }

                $sampleFilePath = $this->getRootDirectory() . '\app\files\samples\products-stock-sample.xlsx';
                $spreadsheet = Excel::loadFile($sampleFilePath);

                // Change active sheet to variables
                $sheet = $spreadsheet->setActiveSheetIndex(2);

                // Set products and stock availability in excel
                $sheet->fromArray($arrProducts, NULL, 'A2', true);
                $sheet->fromArray($arrStockAvailability, NULL, 'D2', true);

                // Change active sheet to database input
                $sheet = $spreadsheet->setActiveSheetIndex(1);

                // Set validation and formula 
                Excel::setCellFormulaVLookup($sheet, 'A3', count($allProducts), "'User Input'!A", 'Variables!$A$3:$B$' . $products_num);
                Excel::setCellFormulaVLookup($sheet, 'D3', count($allStockStatus), "'User Input'!D", 'Variables!$D$3:$E$' . $stock_availability_num);

                // Hide database and variables sheet
                Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
                Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

                // Change active sheet to user input
                $sheet = $spreadsheet->setActiveSheetIndex(0);

                // Set data validation for products and stock availability
                Excel::setDataValidation($sheet, 'A3', 'A' . count($failedProducts), 'TYPE_LIST', 'Variables!$A$3:$A$' . $products_num);
                Excel::setDataValidation($sheet, 'D3', 'D' . count($failedProducts), 'TYPE_LIST', 'Variables!$D$3:$D$' . $stock_availability_num);

                $sheet->setCellValue('G2', 'Error');
                $sheet->getStyle('G2')->applyFromArray(Excel::STYlE_CENTER_BOLD_BORDER_THICK);

                // Add all products to multidimensional array 
                $multiProducts = [];
                $fields = [
                    "productId",
                    "unitPrice",
                    "vat",
                    "stockStatusId",
                    "stock",
                    "expiryDate"
                ];
                $i = 3;
                for ($i = 0; $i < count($failedProducts); $i++) {
                    $product = $failedProducts[$i];
                    $singleProduct = [];
                    foreach ($fields as $field) {
                        if ($field == "productId") {
                            $cellValue = $mapProductIdName[$product[$field]];
                        } else if ($field == "stockStatusId") {
                            $cellValue = $mapStockIdName[$product[$field]];
                        } else {
                            $cellValue = $product[$field];
                        }
                        array_push($singleProduct, $cellValue);
                    }
                    $errors = $allErrors[$i];
                    $error = join(", ", $errors);
                    array_push($singleProduct, $error);

                    array_push($multiProducts, $singleProduct);
                    $i++;
                }
                echo "--multiproducts--";
                echo json_encode($multiProducts);
                // Fill rows with products
                $sheet->fromArray($multiProducts, NULL, 'A3', true);

                // Create excel sheet
                $failedProductsSheetUrl = "files/downloads/reports/products-stock/products-stock-" . $this->objUser->id . "-" . time() . ".xlsx";
                Excel::saveSpreadsheetToPath($spreadsheet, $failedProductsSheetUrl);
            }

            // Update logs
            if (count($successProducts) > 0) {
                $dbStockUpdateUpload->successLog = json_encode($successProducts);
            }

            if (count($failedProducts) > 0) {
                $dbStockUpdateUpload->failedLog = json_encode($failedProducts);
            }

            // Update counts and rates
            $dbStockUpdateUpload->completedCount = $successRecords;
            $dbStockUpdateUpload->failedCount = $failedRecords;
            $dbStockUpdateUpload->unchagedCount = $unchangedRecords;

            if ($successRecords + $failedRecords !== 0) {
                $dbStockUpdateUpload->importSuccessRate = round($successRecords / ($successRecords + $failedRecords), 2) * 100;
                $dbStockUpdateUpload->importFailureRate = round($failedRecords / ($successRecords + $failedRecords), 2) * 100;
            } else {
                $dbStockUpdateUpload->importSuccessRate = 0;
                $dbStockUpdateUpload->importFailureRate = 0;
            }

            $this->f3->set("objStockUpdateUpload", $dbStockUpdateUpload);
            if (!is_null($failedProductsSheetUrl)) {
                $this->f3->set("failedProductsSheetUrl", "/" . $failedProductsSheetUrl);
            }

            $dbStockUpdateUpload->update();

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = "";
            $this->webResponse->data = View::instance()->render('app/products/stock/uploadResult.php');
            echo $this->webResponse->jsonResponse();;
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        }
    }

    function getBonusUpload()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = 1;
            $this->webResponse->title = "Bonus Update"; //$this->f3->get('vModule_bonus_title');
            $this->webResponse->data = View::instance()->render('app/products/bonus/upload.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function postBonusUpload()
    {
        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);
        // basename($_FILES["file"]["name"])

        $targetFile = $this->getUploadDirectory() . $this->generateRandomString(16) . ".$ext";

        if ($ext == "xlsx" || $ext == "xls" || $ext == "csv") {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                global $dbConnection;
                $dbStockUpdateUpload = new BaseModel($dbConnection, "stockUpdateUpload");
                $dbStockUpdateUpload->userId = $this->objUser->id;
                $dbStockUpdateUpload->filePath = $targetFile;
                $dbStockUpdateUpload->entityId = $this->objUser->entityId;
                $dbStockUpdateUpload->addReturnID();
                echo "OK";
            }
        }
    }

    function postBonusUploadProcess()
    {
        global $dbConnection;

        $dbStockUpdateUpload = new BaseModel($dbConnection, "stockUpdateUpload");

        $dbStockUpdateUpload->getByField("userId", $this->objUser->id, "insertDateTime desc");

        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($dbStockUpdateUpload->filePath);
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $spreadsheet = $reader->load($dbStockUpdateUpload->filePath);

            $worksheet = $spreadsheet->getActiveSheet();

            $dbStockUpdateUpload->recordsCount = 0;
            foreach ($worksheet->getRowIterator() as $row) {
                $dbStockUpdateUpload->recordsCount++;
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,

            }
            $dbStockUpdateUpload->recordsCount -= 1;


            $dbStockUpdateUpload->completedCount = $dbStockUpdateUpload->recordsCount - 5;
            $dbStockUpdateUpload->importSuccessRate = round($dbStockUpdateUpload->completedCount / $dbStockUpdateUpload->recordsCount, 2) * 100;

            $dbStockUpdateUpload->failedCount = $dbStockUpdateUpload->recordsCount - $dbStockUpdateUpload->completedCount;
            $dbStockUpdateUpload->importFailureRate = round($dbStockUpdateUpload->failedCount / $dbStockUpdateUpload->recordsCount, 2) * 100;

            $dbStockUpdateUpload->update();

            $this->f3->set("objBonusUpdateUpload", $dbStockUpdateUpload);

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = "";
            $this->webResponse->data = View::instance()->render('app/products/bonus/uploadResult.php');
            echo $this->webResponse->jsonResponse();
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        }
    }
}
