<?php

namespace Drupal\eventmanagment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Class DisplayTableController
 * @package Drupal\mymodule\Controller
 */
class DisplayTableController extends ControllerBase
{

  public function index()
  {
    //create table header
    $header_table = array(
      'id' => t('ID'),
      'title' => t('title'),
      'description' => t('description'),
      'start_date' => t('start date'),
      'end_date' => t('end date'),
      'category' => t('category'),
      'view' => t('View'),
      'delete' => t('Delete'),
      'edit' => t('Edit'),
    );


    // get data from database
    $query = \Drupal::database()->select('events', 'm');
    $query->fields('m', ['id', 'title', 'description', 'start_date', 'end_date' , 'category']);
    $results = $query->execute()->fetchAll();
    $rows = array();
    foreach ($results as $data) {
      $url_delete = Url::fromRoute('eventmodule.delete_form', ['id' => $data->id], []);
      $url_edit = Url::fromRoute('eventmodule.add_form', ['id' => $data->id], []);
      $url_view = Url::fromRoute('eventmodule.show_data', ['id' => $data->id], []);
      $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);
      $linkEdit = Link::fromTextAndUrl('Edit', $url_edit);
      $linkView = Link::fromTextAndUrl('View', $url_view);

      //get data
      $rows[] = array(
        'id' => $data->id,
        'title' => $data->title,
        'description' => $data->description,
        'start_date' => $data->start_date,
        'end_date' => $data->end_date,
        'category' => $data->category,
        'view' => $linkView,
        'delete' => $linkDelete,
        'edit' =>  $linkEdit,
      );

    }
    // render table
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No data found'),
    ];
    return $form;

  }

}
