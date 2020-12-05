<?php
  include_once('../includes/session.php');
  include_once('../database/db_pet.php');

  $csrf = $_POST['csrf'];
  if($csrf != $_SESSION['csrf']){
    $_SESSION['messages'][] = array('type' => 'error', 'content' => 'Failed to add pet adopter!');
    die(header('Location: ' . $_SERVER['HTTP_REFERER']));
  }

  if(!isset($_SESSION['email'])){
    die(header("Location: ../pages/login.php"));
  }
  
  $email = $_SESSION['email'];
  $pet = $_SESSION['curr_pet'];
  $adopter = $_GET['adopter'];
  $accept = $_POST['accept'];
  $decline = $_POST['decline'];

  $state = 0;

  if($accept != NULL && $decline == NULL) $state = 1;
  else if($accept == NULL && $decline != NULL) $state = -1;
  else $state = 0;

  if($email == NULL){
    die(header("Location: ../pages/login.php"));
  }
  else if($email != $pet['user'] || $pet['adoptedBy'] != NULL){
    die(header("Location: ../pages/pet.php?pet_id={$pet['id']}"));
  }

  try {

    if(!is_id($pet)){
      throw new Exception('Invalid Pet id');
    }

    setPetAdoptState($adopter, $pet['id'], $state);
    $_SESSION['messages'][] = array('type' => 'success', 'content' => 'Added adopter to pet!');
  } catch (Exception $e) {
    // die($e->getMessage());
    $_SESSION['messages'][] = array('type' => 'error', 'content' => 'Failed to add pet adopter!');
  }
  header("Location: ../pages/pet.php?pet_id={$pet['id']}");
?>