<?php

class ExportReportPharmaciesActivation implements Command
{
    public function run()
    {
        $data = (new ReportPharmaciesActivation)->find([]);
        $this->createTmpDirectory();

        $fileName = 'export_report_pharmacies_activation_' . (new DateTime)->getTimestamp() . '.csv';
        $path = "./tmp/{$fileName}";
        $csv = fopen($path, 'w+');
        $columns = $data[0]->fields();
        fputcsv($csv, $columns);
        foreach ($data as $datum) {
            $values = [];
            foreach ($columns as $column) {
                $values[] = $datum->$column;
            }
            fputcsv($csv, $values);
        }

        $this->sendMail($path, $fileName);

        unlink($path);
    }

    private function createTmpDirectory()
    {
        if (!is_dir('tmp')) {
            mkdir('tmp');
        }
    }

    private function sendMail($filePath, $fileName)
    {
        $attachment = file_get_contents($filePath);
        $attachment = new \SendGrid\Mail\Attachment($attachment, 'text/csv', $fileName);
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(getenv('MAIN_EMAIL_FROM'), getenv('MAIN_EMAIL_FROM_NAME'));
        $email->setSubject('Pharmacies Activation Report ' . (new DateTime)->format('D/M/Y H:i:s'));
        $email->addContent('text/plain', 'Attached is the report of pharmacy activations.');

        $emails = getenv('CRON_EXPORT_PHARMACIES_ACTIVATION_REPORT_EMAILS');
        if (empty($emails)) {
            throw new InvalidArgumentException('env variable CRON_EXPORT_PHARMACIES_ACTIVATION_REPORT_EMAILS is empty, terminating export');
        }

        $emails = explode(',', $emails);
        $emails = array_map(function ($email) {
            return new \SendGrid\Mail\To($email);
        }, $emails);
        $email->addTos($emails);
        $email->addAttachment($attachment);

        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        $response = $sendgrid->send($email);
        $statusCode = $response->statusCode();

        return $statusCode;
    }
}