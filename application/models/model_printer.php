<?php
class Model_printer extends CI_model
	{
		function print_bill_tellerpay()
		{

			if(PHP_OS=='WINNT') {
                $computer_name = $this->printer_hostname();
                $connector = new WindowsPrintConnector("//SE/$computer_name");
            }else {
                $computer_name = $this->printer_hostname();
                $connector = new WindowsPrintConnector("smb://$computer_name/Receipt");
            }

			$printer = new Escpos($connector);
		}
		
		function printer_hostname(){
			if (!empty($computer_name)){
				return $computer_name;
			}else{
				$ip = $_SERVER['REMOTE_ADDR'];
				return "ITD-SE";
				//return exec("nmblookup -A $ip | grep '<00' | grep -v GROUP | awk '{print $1}'");//get the computer name of $ip, only works when server is Linux
			}
		}

		function print_or_payments() {
            $computer_name = $this->input->post('computer_name');
        }

        function docs_preview($id = false, $doctype = false, $print = false) {
            $this->load->helper('cad');
		    $id = ($id) ? $id : $this->input->post('id') ;
            $doctype = ($doctype) ? $doctype : $this->input->post('doctype');
		    $data = array();
            $html = '';

            $type = get_types_name($doctype);

            if ($doctype != 3436) {
                $saved = $this->db->select('sysid,html,signed')
                    ->from('prime_documents_main')
                    ->where(array('dataid' => $id, 'doctype' => $doctype, 'status' => 1))
                    ->get()->row();

                if ($saved) {
                    //$html .= $saved->html;
                    $newhtml = rehash_pdf_img($saved->html);
                    if ($saved->signed) {
                        $domDoc = new DOMDocument();
                        $domDoc->loadHTML($newhtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING | LIBXML_NOERROR);
                        $xpath = new DOMXPath($domDoc);

                        $signpane = $xpath->query('//img[@class="signature"]');
                        $signature = $this->db->select('imgdata')
                            ->from('prime_user_signature')
                            ->where(array('userid' => user_id(), 'status' => 1))
                            ->get()->row();

                        if ($signature) {
                            foreach ($signpane as $sign) {
                                $sign->setAttribute('src',$signature->imgdata);
                                $signstyle = 'width: 25%; height: auto; position: absolute; margin-top: -50px; margin-left: -20%';
                                $sign->setAttribute('style',$signstyle);
                            }
                            $html .= $domDoc->saveHTML();
                        }
                    } else {
                        $html .= $newhtml;
                    }
                } else {
                    $html .= '<h1 style="alignment: center">NOT FOUND</h1>';
                    $html .= '<p>No document of this type has been published yet.</p>';
                }
            } else {
                $html .= get_tssr_layout($id);
            }

            $data['html'] = $html;
            $data['title'] = ($type) ? $type->desc : '';
            $data['filename'] = ($type) ? $type->desc : '';

            if ($print) {
                return json_encode($data);
            } else {
                return (object)$data;
            }
        }
	}
?>