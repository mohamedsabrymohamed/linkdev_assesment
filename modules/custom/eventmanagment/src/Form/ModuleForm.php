<?php

namespace Drupal\eventmanagment\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;


class ModuleForm extends FormBase
{
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'module_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $conn = Database::getConnection();
    $data = array();
    if (isset($_GET['id'])) {
      $query = $conn->select('events', 'm')
        ->condition('id', $_GET['id'])
        ->fields('m');
      $data = $query->execute()->fetchAssoc();
    }
    $form['picture'] = array(
      '#title' => t('picture'),
      '#description' => $this->t('Chossir Image gif png jpg jpeg'),
      '#type' => 'managed_file',
      '#required' => true,
      '#upload_location' => 'public://images/',
      '#upload_validators' => array(
        'file_validate_extensions' => array('gif png jpg jpeg')),
    );

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('title'),
      '#required' => true,
      '#size' => 60,
      '#default_value' => (isset($data['title'])) ? $data['title'] : '',
      '#maxlength' => 128,
      '#wrapper_attributes' => ['class' => 'col-md-6 col-xs-12']
    ];
    $form['description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('description'),
      '#required' => true,
      '#size' => 255,
      '#default_value' => (isset($data['description'])) ? $data['description'] : '',
      '#wrapper_attributes' => ['class' => 'col-md-6 col-xs-12']
    ];
    $form['start_date'] = [
      '#type' => 'date',
      '#title' => $this->t('start_date'),
      '#required' => true,
      '#default_value' => (isset($data['start_date'])) ? $data['start_date'] : '',
      '#wrapper_attributes' => ['class' => 'col-md-6 col-xs-12']
    ];
    $form['end_date'] = [
      '#type' => 'date',
      '#title' => $this->t('end_date'),
      '#required' => true,
      '#default_value' => (isset($data['end_date'])) ? $data['end_date'] : '',
      '#wrapper_attributes' => ['class' => 'col-md-6 col-xs-12']
    ];
    $form['category'] = [
      '#type' => 'select',
      '#title' => $this
        ->t('Select category'),
      '#options' => [
        'Education',
        'Economics',
        'Business'
      ],
      '#wrapper_attributes' => ['class' => 'col-md-6 col-xs-12'],
      '#default_value' => (isset($data['category'])) ? $data['category'] : '',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('save'),
      '#buttom_type' => 'primary'
    ];
    return $form;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if (is_numeric($form_state->getValue('title'))) {
      $form_state->setErrorByName('title', $this->t('Error, The First Name Must Be A String'));
    }
    if ($form_state->getValue('start_date') < $form_state->getValue('end_date')) {
      $form_state->setErrorByName('end_date', $this->t('Error, End Date Should be a Date after Start Date'));
    }
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $picture = $form_state->getValue('picture');
    $data = array(
      'title' => $form_state->getValue('title'),
      'description' => $form_state->getValue('description'),
      'start_date' => $form_state->getValue('start_date'),
      'end_date' => $form_state->getValue('end_date'),
      'category' => $form_state->getValue('category'),
      'fid' => $picture[0],
    );

    // save file as Permanent
    $file = File::load($picture[0]);
    $file->setPermanent();
    $file->save();

    if (isset($_GET['id'])) {
      // update data in database
      \Drupal::database()->update('events')->fields($data)->condition('id', $_GET['id'])->execute();
    } else {
      // insert data to database
      \Drupal::database()->insert('events')->fields($data)->execute();
    }

    // show message and redirect to list page
    \Drupal::messenger()->addStatus('Succesfully saved');
    $url = new Url('eventmodule.display_data');
    $response = new RedirectResponse($url->toString());
    $response->send();
  }


}
