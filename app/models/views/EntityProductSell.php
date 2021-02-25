<?php

class EntityProductSell extends BaseModel
{
    protected $table_name = 'vwEntityProductSell';

    public function buildDataTableQuery($datatableQuery, $query)
    {
        if (!empty($datatableQuery['startDate'] && !empty($datatableQuery['endDate']))) {
            $query .= " AND stockUpdateDateTime BETWEEN '{$datatableQuery['startDate']}' AND '{$datatableQuery['endDate']}'";
        }

        $productName = $datatableQuery['productName'];
        if (isset($productName) && is_array($productName)) {
            $query .= " AND (";
            foreach ($productName as $key => $value) {
                if ($key !== 0) {
                    $query .= " OR ";
                }
                $query .= "productName_en LIKE '%{$value}%' OR productName_ar LIKE '%{$value}%' OR productName_fr LIKE '%{$value}%'";
            }
            $query .= ")";
        }

        $scientificName = $datatableQuery['scientificName'];
        if (isset($scientificName) && is_array($scientificName)) {
            $query .= " AND (";
            foreach ($scientificName as $key => $value) {
                if ($key !== 0) {
                    $query .= " OR ";
                }
                $query .= "scientificName LIKE '%{$value}%'";
            }
            $query .= ")";
        }

        $stockOption = $datatableQuery['stockOption'];
        if (isset($stockOption) && $stockOption == 1) {
            $query .= " AND stockStatusId = 1 ";
        }

        $categoryId = $datatableQuery['categoryId'];
        if (isset($categoryId) && is_array($categoryId)) {
            $query .= " AND ( categoryId in (" . implode(",", $categoryId) . ") OR subCategoryId in (" . implode(",", $categoryId) . ") )";
        }

        return $query;
    }
}