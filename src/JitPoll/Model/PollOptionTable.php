<?php

namespace JitPoll\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\ResultSet;

class PollOptionTable extends AbstractTableGateway {

    public function __construct($adapter) {
        $this->table = 'tbl_poll_option';
        $this->adapter = $adapter;
    }

    public function getOptions($cond = array()) {
        return $select = $this->select($cond);
        //return $select = $this->select(array('po_poll_id' => $postId));
        //return $select->toArray();
    }

    public function getPollOptions($pollId) {
        $select = $this->select(array('po_poll_id' => $pollId));
        return $select;
        //return $select->toArray();
    }

    public function isExists($cond) {
        if(empty($cond) || !is_array($cond)) return false;
        $select = $this->select($cond);
        $r = $select->current();
        if ($r)
            return true;
        else
            return false;
    }

    public function savePollOption($pollOptions, $pollId) {
        try {
            $poll = new \Zend\Db\TableGateway\TableGateway($this->table, $this->adapter);
            $row = array();
            $poArray = array();
            //var_dump($pollOptions); die();
            if (!empty($pollOptions)):
                foreach ($pollOptions as $index=>$option) {
                    if ($this->isExists(array('po_id'=>$index, 'po_poll_id'=>$pollId))) {
                        $row["po_option"] = $option;
                        $poll->update($row, array('po_id' => $index));
                        $poArray[] = $index;                        
                    }else if(!$this->isExists(array('po_option'=>$option, 'po_poll_id'=>$pollId))) {
                        $row["po_option"] = $option;
                        $row["po_poll_id"] = $pollId;
                        $rowset = $poll->insert($row);
                        //$cat_id = $this->adapter->getDriver()->getLastGeneratedValue();
                        $poArray[] = $poll->lastInsertValue;
                    }
                }
            endif;
            return $poArray;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function deletePollOption($po_id) {
        try {
            $po = new \Zend\Db\TableGateway\TableGateway($this->table, $this->adapter);
            $r = $po->delete(array('po_id' => $po_id));
            if ($r)
                return true;
            else
                return false;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
    public function deletePOByPoll($po_poll_id) {
        try {
            $po = new \Zend\Db\TableGateway\TableGateway($this->table, $this->adapter);
            $r = $po->delete(array('po_poll_id' => $po_poll_id));
            if ($r)
                return true;
            else
                return false;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

}