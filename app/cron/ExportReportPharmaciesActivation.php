<?php

class ExportReportPharmaciesActivation implements Command
{
    public function run()
    {
        $data = (new ReportPharmaciesActivation)->find([]);
        $this->createTmpDirectory();

        $fileName = 'export_report_pharmacies_activation_' . (new DateTime)->getTimestamp() . '.csv';
        $csv = fopen("./tmp/{$fileName}", 'w+');
        $columns = $data[0]->fields();
        fputcsv($csv, $columns);
        foreach ($data as $datum) {
            $values = [];
            foreach ($columns as $column) {
                $values[] = $datum->$column;
            }
            fputcsv($csv, $values);
        }

        // Send Email

        unlink("./tmp/{$fileName}");
    }

    private function createTmpDirectory()
    {
        if (!is_dir('tmp')) {
            mkdir('tmp');
        }
    }
}