<?php

class Printer extends CI_Controller {
    public $load;
    public $input;
    public $model_receipt;
    public $printer;
    public function __construct()
    {
        parent::__construct();
        //init_printer_escpos();
        $this->load->model('model_receipt');
        $this->load->model('model_printer','printer',true);
    }

    function index()
    {

    }

    function testprint() {
        $data['compname'] = $this->model_receipt->printer_hostname();
        $this->load->view('printing/test', $data);
    }

    function printpaybill() {
        $data['compname'] = $this->model_receipt->printer_hostname();
        $this->load->view('printing/test', $data);
    }

    function preview() {
        $html = $this->input->post('html');
        $title = $this->input->post('title');
        $this->load->library('PDF_HTML');
        //set_magic_quotes_runtime(false);
        $pview = new PDF_HTML();

        $txt = ($html) ? $html : 'No data.';

        $pview->AliasNbPages();

        //add page automatically for its true parameter

        $pview->SetAutoPageBreak(true, 15);

        $pview->AddPage();

        //add logo image here

        //$pdf->Image('images/logo.png',18,13,33);

        //set font style

        $pview->SetFont('Arial','B',14);

        $pview->AddPage();

        $pview->WriteHTML($txt);

        $pview->output('I',$title);

    }

    function PDFview() {
        $html = $this->input->post('html');
        $title = $this->input->post('title');
        $filename = $this->input->post('filename');
        $papersize = $this->input->post('papersize');

        /*echo "<pre>";
        print_r ($this->input->post());
        echo "</pre>";
        exit();*/

        $this->load->library('pdf');
        $options = new Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf\Dompdf($options);
        $style = '<style>@page{margin:28mm 12mm 18mm 12mm;} *{box-sizing:border-box;} body{font-family: "DejaVu Sans", Arial, sans-serif; font-size:12px;} .tbl-xs{width:100%; border-collapse:collapse;} .tbl-xs th, .tbl-xs td{font-size:11px; padding:6px 8px; border: 1px solid #ddd;} .tbl-xs thead th{background:#f3f3f3;} .tbl-xs tbody tr:nth-child(even){background:#fafafa;} .text-right{text-align:right;} .text-center{text-align:center;} .avoid-break{page-break-inside:avoid;} thead{display: table-header-group;} tfoot{display: table-footer-group;} .print-header{position: fixed; top: 0; left: 0; right: 0; height: 60px;} hr{margin-top:6px;}</style>';
        $dompdf->loadHtml($style.$html);
        $customPaper = array(0, 0, 615, 930);
        if ($papersize) {
            $dompdf->setPaper($papersize, 'portrait');
        } else {
            $dompdf->setPaper('letter', 'portrait');
        }
        $dompdf->render();
        $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(520, 810, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 9, array(0,0,0));
        // Add PDF Document Information
        $dompdf->add_info('Subject', $title);
        $dompdf->add_info('Author', user_info()->username);
        $dompdf->add_info('Creator', 'ITD');
        $dompdf->add_info('Keywords', $title);
        $dompdf->stream($filename,array('Attachment' => false));
    }

    function docspreview($id = false, $doctype = false) {
        $print = $this->input->post('print');
        $doc = $this->printer->docs_preview($id,$doctype,$print);

        if ($print) {
            echo $doc;
        } else {
            $html = $doc->html;
            $title = $doc->title;
            $filename = $doc->filename;
            $papersize = isset($doc->papersize) ? $doc->papersize : false;
            /*echo "<pre>";
            print_r ($doc);
            echo "</pre>";
            exit();*/

            $this->load->library('pdf');
            $options = new Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $dompdf = new Dompdf\Dompdf($options);
            $style = '<style>@page{margin:28mm 12mm 18mm 12mm;} *{box-sizing:border-box;} body{font-family: "DejaVu Sans", Arial, sans-serif; font-size:12px;} .tbl-xs{width:100%; border-collapse:collapse;} .tbl-xs th, .tbl-xs td{font-size:11px; padding:6px 8px; border: 1px solid #ddd;} .tbl-xs thead th{background:#f3f3f3;} .tbl-xs tbody tr:nth-child(even){background:#fafafa;} .text-right{text-align:right;} .text-center{text-align:center;} .avoid-break{page-break-inside:avoid;} thead{display: table-header-group;} tfoot{display: table-footer-group;} .print-header{position: fixed; top: 0; left: 0; right: 0; height: 60px;} hr{margin-top:6px;}</style>';
            $dompdf->loadHtml($style.$html);
            $customPaper = array(0, 0, 615, 930);
            if ($papersize) {
                $dompdf->setPaper($papersize, 'portrait');
            } else {
                $dompdf->setPaper('letter', 'portrait');
            }
            $dompdf->render();
            $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
            $canvas = $dompdf->getCanvas();
            $canvas->page_text(520, 810, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 9, array(0,0,0));
            // Add PDF Document Information
            $dompdf->add_info('Subject', $title);
            $dompdf->add_info('Author', user_info()->username);
            $dompdf->add_info('Creator', 'PAE');
            $dompdf->add_info('Keywords', $title);
            $dompdf->stream($filename, array('Attachment' => false));
        }
    }

}
