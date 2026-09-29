<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchasing extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('model_purchasing', 'purchasing', true);
        $this->load->model('model_bos');
        $this->load->model('model_eprs');
        //include Bos controller since some functions were needed and to minimize function duplicates.
        $this->load->library('../controllers/Bos');
    }
    /**
     * Get the account codes that were saved in the database.
     * @return array Retuns an array of data which contains the HTML of <option> to be used in account code selection front-end.
     */
    public function getAccountCodes(){
        $query = $this->model_purchasing->getAccountCodes();
        $html = '';
        foreach ($query->result() as $row) {
            $html .= '<option value="' . $row->sysid . '">' . $row->codes . ' - ' . $row->descs .'</option>';
        }
        $data = [
            'html' => $html
        ];
        echo json_encode($data);
    }
    /**
     * Get all the predefined units stored in the database.
     * @return array Returns the <option> HTML of the results to be used in the unit selection.
     */
    public function getUnit(){
        $unit = $this->model_bos->getUnitData();
        $html = '';
        foreach ($unit->result() as $row) {
            $html .= '<option value="' . $row->sysid . '">' . $row->unit_code . ' - ' . $row->unit_name .'</option>';
        }
        $data = [
            'html' => $html
        ];
        echo json_encode($data);
    }
    public function getCostCenters(){
        echo $this->bos->getCostCenterData();
    }
    //TODO UNFINISHED, please improve the logic in order for the ajax to receive the data for viewing.
    function getBtypeBudgets($btype_id, $cc_id, $year){
        $data = array(
            'qry'           =>false,
            'query_result'  =>null,
            'msg'           =>'No queries has been made, please use the right budget type.'
        );
        if ($btype_id == 77){
            $budget_data = $this->model_eprs->getCcOpexBudget($cc_id, $year);
            if ($budget_data){
                $data['qry'] = true;
                $data['query_result'] = $budget_data;
                $data['msg'] = 'OPEX budget retrieval successful!';
            }else{
                #return an error message
                $data['msg'] = 'OPEX budget empty!';
            }
        }else if ($btype_id == 76 || $btype_id == 78){
            $budget_data = $this->model_eprs->getCcCapexSpBudget($cc_id, $btype_id, $year);
            if ($budget_data){
                $data['qry'] = true;
                $data['query_result'] = $budget_data;
                $data['msg'] = 'CAPEX/SP budget retrieval successful!';
            }else{
                $data['msg'] = 'CAPEX/SP budget empty!';
            }
        }else{
            $data['msg'] = 'Budget ID is invalid, please contact the administrator.';
        }
        echo json_encode($data);
    }
    
    function toggleItemPrsRequest(){
        $item_id = $this->input->post('itemId');
        $toggled = $this->model_eprs->toggleItemPrsRequest($item_id);
        $toggled_value = null;
        $qry = false;
        $information = '';
        $func = 'warning';
        $msg = 'toggleItemPrsRequest query has been failed.';
        if ($toggled){
            $toggled_value = $toggled->row()->prs_request;
            if ($toggled_value == 1){
                $msg = 'Item ADDED to PRS request.';
                $information = 'ITEM ADDITION';
            }else{
                $information = 'ITEM REMOVAL';
                $msg = 'Item REMOVED to PRS request.';
            }
            
            $qry = true;
            $func = 'success';
        }
        echo json_encode(
            array(
                'qry'       =>$qry,
                'msg'       =>$msg,
                'func'      =>$func,
                'info'      =>$information,
                'toggleVal' =>$toggled_value
            )
        );
    }
    /**
     * Chooses which item toggle query to be used based on the action of the user on the prs checkbox.
     * @param int $budget_data_id The ID of the budget in `trn_budget_data`.
     * @param int $checked The action made by the user 1 for "checked", 0 for "unchecked".
     * @return array associative array of values.
     */
    function toggleBudgetItems($budget_data_id, $checked){
        $data = array();
        if ($checked == 1){
            $toggled = $this->model_eprs->togglePrsItemOne($budget_data_id);
            $data['qry'] = $toggled;
            $data['msg'] = 'Items within this budget will be added to the request.';
            $data['func'] = 'success';
        }else if ($checked == 0){
            $toggled = $this->model_eprs->togglePrsItemZero($budget_data_id);
            $data['qry'] = $toggled;
            $data['msg'] = 'The budget and the items within were removed from PRS request.';
            $data['func'] = 'success';
        }else{
            $data['qry'] = false;
            $data['msg'] = 'Budget does not match any approved budget in the system.';
            $data['func'] = 'warning';
        }
        return $data;
    }
    function toggleBtypeRequest(){
        $budget_data_id     = $this->input->post('budgetId');
        $checked            = $this->input->post('checked');
        $budget_toggle_val = false;
        $toggle_stats = $this->toggleBudgetItems($budget_data_id, $checked);
        $budget_toggle = $this->model_eprs->toggleBudgetPrsRequest($budget_data_id);
        if($budget_toggle){
            $budget_toggle_val = $budget_toggle->row()->prs_request;
        }
        $data = array(
            'toggleData'        =>$toggle_stats,
            'budgetToggleVal'   =>$budget_toggle_val
        );
        echo json_encode($data); 
    }
#########################################################################################################################################################
################################################################## CODE TESTING #########################################################################
#########################################################################################################################################################
    function testToggleItemPrs(){
        print_r($this->toggleItemPrsRequest(229));
    }
    function test(){
        $budgetid_arr = $this->input->post('budgeids');
        $num_of_budget_submited = 0;
        $budget_id_arr = array();
        foreach($budgetid_arr as $row){
                $budget_id_arr[] = $row;
                $num_of_budget_submited += 1;	
        }
        $data['budgetid'] = $budget_id_arr;				
        $data['budgetnum'] = $num_of_budget_submited;				
        $data['input'] = $this->input->post();
        echo json_encode($data);	
    }
    function testgetBtypeBudgets(){
        print_r($this->getBtypeBudgets(76, 14, 2017));
    }


    function tblsuppliers() {
        echo $this->purchasing->tbl_suppliers();
    }

}
