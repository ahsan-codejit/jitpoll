<?php

namespace UnbAdmin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\ResultSet;

class PostCategoryTable extends AbstractTableGateway {

  public function __construct($adapter) {
    $this->table = 'tbl_post_category';
    $this->adapter = $adapter;
  }

  public function getCategories($cond = array()) {
    return $select = $this->select($cond);
    return $select = $this->select(array('pc_post_id' => $postId));
    //return $select->toArray();
  }

  public function getPostCategories($postId) {
    $select = $this->select(array('pc_post_id' => $postId));
    $categories = array();
    if ($select) {
      foreach ($select as $s) {
        $categories[] = $s->pc_cat_id;
      }
    }
    return $categories;
    //return $select->toArray();
  }

  public function isExists($cat, $post) {
    $select = $this->select(array('pc_cat_id' => $cat, 'pc_post_id' => $post));
    $r = $select->current();
    if ($r)
      return true;
    else
      return false;
  }

  public function savePostCategory($categories, $post_id) {
    try {
      $category = new \Zend\Db\TableGateway\TableGateway($this->table, $this->adapter);
      $category->delete(array('pc_post_id' => $post_id));
      $row = array();
      if (!empty($categories)):
        foreach ($categories as $cat) {
          if (!$this->isExists($cat, $post_id)) {
            $row["pc_cat_id"] = $cat;
            $row["pc_post_id"] = $post_id;
            $rowset = $category->insert($row);
            //$cat_id = $this->adapter->getDriver()->getLastGeneratedValue();
            //return $cat_id = $category->lastInsertValue;
          }
        }
      endif;
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function deletePostCategory($post_id) {
    try {
      $category = new \Zend\Db\TableGateway\TableGateway($this->table, $this->adapter);
      $category->delete(array('pc_post_id' => $post_id));
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

}
