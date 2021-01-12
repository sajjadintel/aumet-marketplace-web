<?php

class PDF extends TCPDF {

    var $top_margin = 20;

    // Page header
    function Header()
    {
        // Logo
        $this->Image('@' . file_get_contents('assets/img/aumet-logo.png'), 10, 6, 30);
        // Line break
        $this->Ln(10);
        // padding for second page
        $this->top_margin = $this->GetY() + 20;
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }

    // Simple table
    function BasicTable($header, $data)
    {
        // Header
        foreach ($header as $col)
            $this->Cell(45, 7, $col, 1);
        $this->Ln();
        // Data
        foreach ($data as $row) {
            foreach ($row as $col)
                $this->Cell(45, 6, $col, 1);
            $this->Ln();
        }
    }

    // Better table
    function ImprovedTable($header, $data)
    {
        // Column widths
        $w = array(40, 35, 40, 45);
        // Header
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        $this->Ln();
        // Data
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR');
            $this->Cell($w[1], 6, $row[1], 'LR');
            $this->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R');
            $this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R');
            $this->Ln();
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }

    // Colored table
    function FancyTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(19, 185, 169);
        $this->SetTextColor(255);
        $this->SetDrawColor(51, 51, 51);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        // Header
        $w = array(80, 100);
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $row[2], 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }

    // Colored table
    function FancyTableOrderDetail($header, $data)
    {
        // use html for table
        $html = "<table border=\"0.5\" cellpadding=\"3\"><thead><tr>";

        // Header
        $w = array(40, 180, 68, 109, 113);
        for ($i = 0; $i < count($header); $i++)
            $html .= "<th width=\" $w[$i] \" style=\"background-color:#13b9a9;color:#ffffff;font-weight:bold;\"> $header[$i] </th>";


        $html .= "</tr ></thead><tbody>";

        $fill = false;
        foreach ($data as $row) {
            $fillText = $fill ? "background-color:#e1eaff;" : '';

            $html .= "<tr nobr=\"true\">";
            for ($i = 0; $i < count($row); $i++)
                $html .= "<td style=\"$fillText color:#000000;\" width=\"$w[$i]\" >$row[$i]</td>";
            $html .= "</tr>";

            $fill = !$fill;
        }

        $html .= "</tbody></table>";

        $this->writeHTML($html, true, false, false, false, '');
    }

}
