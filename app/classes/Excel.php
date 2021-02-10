<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Excel {
    
    // Styles
    public const STYlE_CENTER_BOLD_BORDER_THICK = array(
        'alignment' => array(
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ),
        'font'    => array(
            'bold'      => true
        ),
        'borders' => array(
            'bottom'     => array(
                'borderStyle' => Border::BORDER_THICK,
                'color' => array(
                    'rgb' => '000000'
                )
            )
        )
    );
    public const STYlE_CENTER_BOLD_BORDER_NORMAL = array(
        'alignment' => array(
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ),
        'font'    => array(
            'bold'      => true
        ),
        'borders' => array(
            'bottom'     => array(
                'borderStyle' => Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '000000'
                )
            )
        )
    );
    public const STYlE_BOLD_BORDER_THICK = array(
        'font'    => array(
            'bold'      => true
        ),
        'borders' => array(
            'bottom'     => array(
                'borderStyle' => Border::BORDER_THICK,
                'color' => array(
                    'rgb' => '000000'
                )
            )
        )
    );

    // Load file from path
    public static function loadFile($filePath) {
        $spreadsheet = IOFactory::load($filePath);
        return $spreadsheet;
    }

    // Save spreadsheet
    public static function saveSpreadsheetToPath($spreadsheet, $path) {
        $allPathParts = explode(".", $path);
        $ext = end($allPathParts);
        
        switch($ext) {
            case "xlsx":
            case "xlsm":
                $writer = new Xlsx($spreadsheet);
                $writer->setPreCalculateFormulas(false);
                $writer->save($path);
                break;
            case "xls":
                $writer = new Xls($spreadsheet);
                $writer->setPreCalculateFormulas(false);
                $writer->save($path);
                break;
            case "csv":
                $writer = new Csv($spreadsheet);
                $writer->setPreCalculateFormulas(false);
                $writer->save($path);
                break;
            default:
                return false;
        }
        return true;
    }

    // Hide sheet by name
    public static function hideSheetByName($spreadsheet, $sheetname) {
        $spreadsheet->getSheetByName($sheetname)->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
    }

    // Set VLookup formula on cells
    public static function setCellFormulaVLookup($sheet, $startCell, $rowCount, $value, $table) {
        $rowArray = [];
        for($i = 3; $i <= $rowCount + 3; $i++) {
            $inputVal = $value.$i;
            $rowArray[] = "=VLOOKUP($inputVal, $table, 2, FALSE)";
        }
    
        $columnArray = array_chunk($rowArray, 1);
        $sheet->fromArray(
                $columnArray,
                NULL, 
                $startCell
        );
    }

    // Set data validation on cells
    public static function setDataValidation($sheet, $startCell, $endCell, $validationType, $formula1, $formula2 = '') {
        $validation = $sheet->getCell($startCell)->getDataValidation();
    
        switch ($validationType) {
            case "TYPE_CUSTOM":
                $validation->setType(DataValidation::TYPE_CUSTOM);
                break;
            case "TYPE_DECIMAL":
                $validation->setType(DataValidation::TYPE_DECIMAL);
                break;
            case "TYPE_LIST":
                $validation->setType(DataValidation::TYPE_LIST);
                break;
        }
        
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
    
        // Set dropdown in case of list type validation
        if($validationType == 'TYPE_LIST') $validation->setShowDropDown(true);
    
        $validation->setFormula1($formula1);
        if($formula2 != '') $validation->setFormula2($formula2);
    
        return $sheet->setDataValidation($startCell.":".$endCell, $validation);
    }

    // Convert excel date to regular date
    public static function excelDateToRegularDate($excelDate, $format = "Y-m-d") {
        return date($format, ($excelDate - 25569) * 86400);
    }
}
