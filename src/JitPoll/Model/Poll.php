<?php
namespace JitPoll\Model;

use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

use UnbAdmin\Utility\UnbUtility;

class Poll implements InputFilterAwareInterface {
    public $poll_id;
    public $poll_title;
    public $poll_user_answer_option;
    public $poll_createdby;
    public $poll_start;
    public $poll_end;
    public $poll_status;
    public $poll_created;
    public $poll_updated;
    protected $inputFilter;                       // <-- Add this variable

    public function exchangeArray($data)    {
        $this->poll_id     = (isset($data['poll_id']))     ? $data['poll_id']     : null;
        $this->poll_title = (isset($data['poll_title'])) ? $data['poll_title'] : null;
        $this->poll_user_answer_option = (!empty($data['poll_user_answer_option']) && $data['poll_user_answer_option']=='Yes') ? $data['poll_user_answer_option']:'No';
        $this->poll_createdby  = (isset($data['poll_createdby']))  ? $data['poll_createdby']  : 0;
        $this->poll_start  = (isset($data['poll_start']))  ? $data['poll_start']  : null;
        $this->poll_end  = (isset($data['poll_end']))  ? $data['poll_end']  : null;
        $this->poll_status  = (isset($data['poll_status']))  ? $data['poll_status']  : 'Active';
        $this->poll_created = (isset($data['poll_created'])) ? $data['poll_created'] : date('Y-m-d H:i:s');
        $this->poll_updated  = (isset($data['poll_updated']))  ? $data['poll_updated']  : null;
    }
     // Add the following method:
    public function getArrayCopy()    {
        return get_object_vars($this);
    }

    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        //
    }
}