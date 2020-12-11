<?php
  include_once('../includes/session.php');
  include_once('../database/db_pet.php');

  $csrf = $_POST['csrf'];
  if($csrf != $_SESSION['csrf']){
    $_SESSION['messages'][] = array('type' => 'error', 'content' => 'Failed to update pet!');
    die(header('Location: ' . $_SERVER['HTTP_REFERER']));
  }

  if(!isset($_SESSION['email'])){
    die(header("Location: ../pages/login.php"));
  }

  $email = $_SESSION['email'];
  $pet = $_POST['pet_id'];

  try {
      if(!is_id($pet)){
        throw new Exception('Invalid Pet id');
      }

      if(getPetOwner($pet) == $email){
        $name = clean_text($_POST['name']);
        $species = clean_text($_POST['species']);
        $age = clean_text($_POST['age']);
        $color = clean_text($_POST['color']);
        $location = clean_text($_POST['location']);

        if ($msg = invalid_pet($name, $species, $age, $color, $location)) {
          throw new Exception($msg);
        }

        updatePet($pet, $name, $species, $age, $color, $location, $email);
        $_SESSION['messages'][] = array('type' => 'success', 'content' => 'Pet Updated!');
      }
      else{
        throw new PDOException('User Does Not Own This Pet');
      }

  } catch (Exception $e) {
    $_SESSION['messages'][] = array('type' => 'error', 'content' => 'Failed to add pet! '.$e->getMessage());
  }
  header("Location: ../pages/pet.php?pet_id={$pet}");
?>