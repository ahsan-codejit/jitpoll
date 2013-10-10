<?php

namespace JitPoll\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use JitPoll\Model\Poll;
use Zend\Mvc\MvcEvent;

class IndexController extends AbstractActionController {

  protected $pollTable;
  protected $pollOptionTable;

  public function onDispatch(MvcEvent $e) {
    $this->Auth()->checkAuth();
    $this->Auth()->setLayoutUserInfo($this);
    return parent::onDispatch($e);
  }

  public function indexAction() {
    // grab the paginator from the CategoryTable
    $polls = $this->getPollTable()->fetchAll(null,true);
    // set the current page to what has been passed in query string, or to 1 if none set
    $polls->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
    // set the number of items per page to 10
    $polls->setItemCountPerPage(5);

    //set title, menu and submenu active
    $this->layout()->title = "UNB - Poll List";
    $this->layout()->main_menu_active = "poll";
    $this->layout()->sub_menu_active = "open";

    return new ViewModel(array(
        'polls' => $polls,
    ));
  }
  public function pollAction(){
      $pollId = (int) $this->params()->fromRoute('id', 0);
    if (!$pollId) {
      return $this->redirect()->toRoute('jitpoll', array(
                  'action' => 'add'
      ));
    }
    $poll = $this->getPollTable()->getPoll($pollId);
    $pollOptions = $this->getPollOptionTable()->getPollOptions($pollId);
    return array(
        'poll'=>$poll,
        'pollOptions'=>$pollOptions,
        'id'=>$pollId
    );
  }
  public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $poll = new Poll();
            $poll->exchangeArray($request->getPost());
            $pollId = $this->getPollTable()->savePoll($poll);
            if ($pollId) {
                $options = $request->getPost('poll_options');
                $this->getPollOptionTable()->savePollOption($options, $pollId);
            }
            return $this->redirect()->toRoute('jitpoll');
        }

        //set title, menu and submenu active
        $this->layout()->title = "UNB - Add Pull";
        $this->layout()->main_menu_active = "pull";
        $this->layout()->sub_menu_active = "open";

        return array();
    }

  public function editAction() {
    $pollId = (int) $this->params()->fromRoute('id', 0);
    if (!$pollId) {
      return $this->redirect()->toRoute('jitpoll', array(
                  'action' => 'add'
      ));
    }   

    $request = $this->getRequest();
    if ($request->isPost()) {
      $options = $request->getPost('poll_options');
      //var_dump($options);      //die();
      $pollObj = new Poll();
      $pollObj->exchangeArray($request->getPost());
      $ur = $this->getPollTable()->savePoll($pollObj);
      if ($pollId) {
        $options = $request->getPost('poll_options');
        $this->getPollOptionTable()->savePollOption($options, $pollId);
        }
    }

    //set title, menu and submenu active
    $this->layout()->title = "UNB - Edit Category";
    $this->layout()->main_menu_active = "news_category";
    $this->layout()->sub_menu_active = "open";
    $poll = $this->getPollTable()->getPoll($pollId);
    //var_dump($poll);
    return array(
        'id' => $pollId,
        'form' => '',
        'poll' => $poll,
        'polloptions' => $this->getPollOptionTable()->getPollOptions($pollId),
    );
  }

  public function deleteAction() {
    $id = (int) $this->params()->fromRoute('id', 0);
    if (!$id) {
      return $this->redirect()->toRoute('jitpoll');
    }

    $request = $this->getRequest();
    if ($request->isPost()) {
      $del = $request->getPost('del', 'No');

      if ($del == 'Yes') {
        $id = (int) $request->getPost('id');
        $r = $this->getPollOptionTable()->deletePOByPoll($id);
        if($r) $this->getPollTable()->deletePoll($id);
      }

      // Redirect to list of albums
      return $this->redirect()->toRoute('jitpoll');
    }

    //set title, menu and submenu active
    $this->layout()->title = "UNB - Delete Poll";
    $this->layout()->main_menu_active = "poll";
    $this->layout()->sub_menu_active = "open";

    return array(
        'poll_id' => $id,
        'poll' => $this->getPollTable()->getPoll($pollId)
    );
  }

  public function ajaxDeleteAction() {
    $id = (int) $this->params()->fromRoute('id', 0);
    if (!$id) {
      return $this->redirect()->toRoute('jitpoll');
    }
    $r = $this->getPollOptionTable()->deletePOByPoll($id);
    if($r) $this->getPollTable()->deletePoll($id);

    // Redirect to list of poll
    return $this->redirect()->toRoute('jitpoll');
  }

  public function getPollTable() {
    if (!$this->pollTable) {
      $sm = $this->getServiceLocator();
      $this->pollTable = $sm->get('JitPoll\Model\PollTable');
    }
    return $this->pollTable;
  }
  public function getPollOptionTable() {
    if (!$this->pollOptionTable) {
      $sm = $this->getServiceLocator();
      $this->pollOptionTable = $sm->get('PollOptionTable');
    }
    return $this->pollOptionTable;
  }

  private function getTable($table) {
    return $this->getServiceLocator()->get($table);
  }

}
