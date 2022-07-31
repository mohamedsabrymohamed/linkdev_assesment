<?php

namespace Drupal\eventmanagment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;

/**
 * Class MydataController
 * @package Drupal\mymodule\Controller
 */
class ShowController extends ControllerBase
{

  /**
   * @return array
   */
  public function show($id)
  {

    $conn = Database::getConnection();

    $query = $conn->select('events', 'm')
      ->condition('id', $id)
      ->fields('m');
    $data = $query->execute()->fetchAssoc();
    $title = $data['title'];
    $description = $data['description'];
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];
    $category = $data['category'];


    $file =File::load($data['fid']);
    $picture = $file->url();

    return [
      '#type' => 'markup',
      '#markup' => "<h1>$title</h1><br>
                        <img src='$picture' width='100' height='100' /> <br>
                        <p>$description</p>
                        <p>from: $start_date to: $end_date</p>
                        <p>$category</p>"
    ];
  }

}
