<?php

namespace JitPoll\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class PollTable {
    protected $tableGateway;
    protected $table;
    public function __construct(TableGateway $tableGateway)    {
        $this->tableGateway = $tableGateway;
        $this->table = 'tbl_poll';
    }

    public function fetchAll($cond = array(),$paginated=false)    {
        if($paginated) {
            // create a new Select object for the table smf_personal_messages
            $select = new Select($this->table);
            if(!empty($cond)) $select->where($cond);
            // create a new result set based on the PersonalMessage entity
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Poll());
            // create a new pagination adapter object
            $paginatorAdapter = new DbSelect(
                // our configured select object
                $select,
                // the adapter to run it against
                $this->tableGateway->getAdapter(),
                // the result set to hydrate
                $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getPoll($id)    {
        $id  = (int) $id;
        if(empty($id)) return null;
        $rowset = $this->tableGateway->select(array('poll_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function savePoll(Poll $poll)    {
        
        $id = (int)$poll->poll_id;
        if ($id == 0) {
            $data = array(
                'poll_title' => $poll->poll_title,
                'poll_user_answer_option' => $poll->poll_user_answer_option,
                'poll_createdby'  => $poll->poll_createdby,
                'poll_start'  => $poll->poll_start,
                'poll_end'  => $poll->poll_end,
                'poll_status'  => $poll->poll_status,
                'poll_created'  => $poll->poll_created,
            );
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;  
        } else {
            if ($this->getPoll($id)) {
                $data = array();
                if(!empty($poll->poll_title)) $data['poll_title'] = $poll->poll_title;
                if(!empty($poll->poll_user_answer_option)) $data['poll_user_answer_option'] = $poll->poll_user_answer_option;
                if(!empty($poll->poll_createdby)) $data['poll_createdby'] = $poll->poll_createdby;
                if(!empty($poll->poll_start)) $data['poll_start'] = $poll->poll_start;
                if(!empty($poll->poll_end)) $data['poll_end'] = $poll->poll_end;
                if(!empty($poll->poll_status)) $data['poll_status'] = $poll->poll_status;
                $data['poll_updated']  = date('Y-m-d H:i:s');
                $r = $this->tableGateway->update($data, array('poll_id' => $id));
                if($r) return $id;
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deletePoll($id)    {
        $this->tableGateway->delete(array('poll_id' => $id));
    }
}
