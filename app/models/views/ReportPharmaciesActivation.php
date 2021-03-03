<?php

class ReportPharmaciesActivation extends BaseModel
{
    public function __construct()
    {
        global $dbConnection;
        parent::__construct($dbConnection, 'vwReport_PharmaciesActivation');
    }
}