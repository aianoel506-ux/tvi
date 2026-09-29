<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This class aims to be house the functions shared from BOS to PRS module.
 *
 * @author IT - ED <edrian.meg@gmail.com>
 */
class Model_eprs extends CI_Model{
    #Code Objective: Please make your function do simple things at once, minimise overcomplicated query functions.
    
    #Individual `SELECT` query related to PRS. Query statements with `JOIN` should not be allowed in here.
    #####################################################################################################################################################################
    ################################################################### INDIVIDUAL SELECT QUERIES #######################################################################
    #####################################################################################################################################################################
    //TODO UNUSED getCcOpexBudget use this in the new prs.
    /**
     * 
     * @param type $cc_id
     * @param type $year
     * @return type
     */
    function getCcOpexBudget($cc_id, $year){
        $statement = 'SELECT transaction_id, cc_id, acct_code_id, date_created, total_budget, balance, `status`
                        FROM bos_opex
                        where cc_id = ? and year(date_created) = ?';
        $query = $this->db->query($statement, array($cc_id, $year));
        return ($query->num_rows() > 0) ? $query:false;
    }
    //TODO UNUSED getCcCapexSpBudget use this in the new prs.
    /**
     * 
     * @param type $cc_id
     * @param type $btype_id
     * @param type $year
     * @return type
     */
    function getCcCapexSpBudget($cc_id, $btype_id, $year){
        $statement = 'SELECT sysid, budget_id, job_order_id, transaction_id, cc_id, acct_code_id, date_created, approved_budget, balance, `status` 
                        FROM bos_capex_sp
                        where cc_id = ? and btype_id = ? and year(date_created) = ?';
        $query = $this->db->query($statement, array($cc_id, $btype_id, $year));
        return ($query->num_rows() > 0) ? $query:false;
    }
    //TODO UNUSED getCapexSpBudget
    function getCapexSpBudget($budget_id){
        $statement = 'SELECT sysid, budget_id, job_order_id, transaction_id, cc_id, acct_code_id, date_created, approved_budget, balance, `status` 
                        FROM bos_capex_sp
                        where sysid = ?';
        $query = $this->db->query($statement, array($budget_id));
        return ($query->num_rows() > 0) ? $query:false;
    }
    #####################################################################################################################################################################
    ################################################################### COMPLEX SELECT QUERIES ##########################################################################
    #####################################################################################################################################################################
    
    /**
     * Returns the abbreviation of the module of the transaction.
     * @param int $transaction_id
     * @return object MySQL-Object.
     */
    function getModuleAbbrev($transaction_id){
        $statement = 'select code
                        from transaction_eprs a
                        left join prime_module_main b on a.module_id = b.sysid
                        where a.id = ?';
        $query = $this->db->query($statement, array($transaction_id));
        return ($query->num_rows())? $query:false;
    }
    function getRequestData($budget_type){
        
    }
    /**
     * Gets the data of the employee such as: person ID, employee ID, firstname, lastname, cost center ID, cost-center code and name, cost center type value, cost center type name, system user ID, role ID, and role description. 
     * @param type $employee_id
     * @return object MySQL object when successful, false otherwise.
     */
    function employeeDetails($employee_id){
        $statement = "select a.sysid personId, b.sysid empid, a.firstname, a.lastname, d.sysid ccid, concat(d.codes, ' - ', d.`desc`) costCenter, d.`type` costCenterTypeVal, 
                            (case when d.`type` = 1 then 'Main Office'
                                when d.`type` = 0 then 'Operations'
                                else 'No Office Designation'
                                END
                            ) 'Office Designation',
                            e.sysid systemId, g.sysid roleId, concat(g.`code`, ' - ', g.descriptions) role
                        from person a
                        left join prime_employee_main b on a.sysid = b.personid
                        left join prime_employee_costcenter c on b.sysid = c.empid and c.`status` = 1
                        left join prime_costcenter_main d on c.ccid = d.sysid
                        left join prime_system_users e on a.sysid = e.personid
                        left join prime_system_users_roles_matrix f on e.sysid = f.userid
                        left join prime_system_users_roles_main g on f.roleid = g.sysid
                        where e.sysid = ?";
        $query =  $this->db->query($statement, array($employee_id));
        return ($query->num_rows() > 0)? $query:false;
    }
    #####################################################################################################################################################################
    ################################################################### INSERTION QUERIES ###############################################################################
    #####################################################################################################################################################################
    /**
     * Insert in-transaction data logs.
     * @param array $array The array of data to be inserted.
     * @return boolean return true of the insert is successful, false otherwise.
     */
    function insertTransactionLogs($array){
        //TODO ADD include system user ID in the columns of this table.
        $module_id = $array['module_id'];
        $transaction_report_id = $array['transaction_report_id'];
        $types_parameter_id = $array['types_parameter_id'];
        $action_taken_id = $array['action_taken_id'];
        $role_id = $array['role_id'];
        $branch = $array['branch'];
        $changed_data_id = $array['changed_data_id'];
        
        $statement = 'INSERT INTO in_transaction_logs (`module_id`, `transaction_report_id`, `types_parameter_id`, `action_taken_id`, `role_id`, branch, changed_data_id) VALUES (?,?,?,?,?,?,?)';
        
        $this->db->query($statement, array($module_id, $transaction_report_id, $types_parameter_id, $action_taken_id, $role_id, $branch, $changed_data_id));
        return ($this->db->affected_rows() != 1)? false:true;
    }
    #####################################################################################################################################################################
    ################################################################### UPDATE QUERIES ##################################################################################
    #####################################################################################################################################################################
    function toggleItemPrsRequest($bos_item_id){
        $statement = 'UPDATE items_joborder_table 
                        SET prs_request = CASE
                                WHEN prs_request = 1 THEN prs_request = @prs_request := 0
                                ELSE @prs_request := 1
                            END
                        WHERE sysid = ?';
        $getUpdateValue = 'SELECT @prs_request prs_request';
        $this->db->query($statement, array($bos_item_id));
        return ($this->db->affected_rows() != 1)? false:$this->db->query($getUpdateValue);
    }
    function toggleBudgetPrsRequest($bos_budget_id){
        $statement = 'UPDATE trn_budget_data 
                        SET prs_request = CASE
                                WHEN prs_request = 1 THEN prs_request = @prs_request := 0
                                ELSE @prs_request := 1
                            END
                        WHERE sysid = ?';
        $getUpdateValue = 'SELECT @prs_request prs_request';
        $this->db->query($statement, array($bos_budget_id));
        return ($this->db->affected_rows() != 1)? false:$this->db->query($getUpdateValue);
    }
    /**
     * This toggles the `prs_request` column of the approved budget to zero, which means that the items inside that budget will not be included in the request.
     * @param int $budget_data_id this is the ID of the budget saved in the database. Specifically points to the ID of ` trn_budget_data` table.
     * @return boolean true if the transaction were completed and false when the transaction encountered a problem.
     */
    function togglePrsItemZero($budget_data_id){
        $statement = 'update items_joborder_table set prs_request = 0 where b_data_id = ?';
        $this->db->trans_begin();
        
        $this->db->query($statement, array($budget_data_id));
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    /**
     * This function toggles the `prs_request` column of the items in the given budget ID to 1 which in turn include all the items to the PRS request.
     * @param int $budget_data_id the ID of the budget in `trn_budget_data` table.
     * @return boolean
     */
    function togglePrsItemOne($budget_data_id){
        $statement = 'update items_joborder_table set prs_request = 1 where b_data_id = ?';
        
        $this->db->trans_begin();
        
        $this->db->query($statement, array($budget_data_id));
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    /**
     * Reset the `prs_request` column of `trn_budget_data` table to zero. Usually done after sending the prs request for approval.
     * @param int $budget_data_id The ID of trn_budget_data.
     * @return boolean
     */
    function togglePrsBudgetZero($budget_data_id){
        $statement = 'update trn_budget_data set prs_request = 0 where sysid = ?';
        $query = $this->db->query($statement, array($budget_data_id));
        return ($this->db->affected_rows() != 1)? false:true;
    }
}
