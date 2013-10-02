<?php

//! @file PrepareCommand.php
//! @brief This file contains the PrepareCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ElephantOnCouch\Generator\UUID;


//! @brief Prepares Programmazione.it v6.4 MySQL database to be imported.
//! @nosubgrouping
class PrepareCommand extends AbstractCommand {


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("prepare");
    $this->setDescription("Prepares Programmazione.it v6.4 MySQL database to be imported.");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {
    $mysql = $this->_di['mysql'];

    // Creates a new 'Redazione' user and assigns to every item and tag where idMember is null.
    $sql = "INSERT INTO Member (idMember, nickName, email, password, regDate) VALUES (1, 'Redazione', 'redazione@programmazione.it', MD5('chid0rmen0npigliap3sci'), NOW())";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));
    $sql = "UPDATE Item SET idMember = 1 WHERE idMember IS NULL";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));

    // Alters Member.
    $sql = "ALTER TABLE Member ADD id VARCHAR(255)";
    $result = mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));
    $sql = "CREATE INDEX id_index ON Member (id) USING BTREE";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));

    // Update Member.
    $sql = "SELECT idMember FROM Member";
    $result = mysqli_query($mysql, $sql) or die(mysqli_error($mysql));

    while ($row = mysqli_fetch_row($result)) {
      $uuid = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);
      $sql = "UPDATE Member SET id = '".$uuid."' WHERE idMember = ".$row[0];
      mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));
    }

    mysqli_free_result($result);

    // Alters Item.
    $sql = "ALTER TABLE Item ADD id VARCHAR(255)";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));
    $sql = "CREATE INDEX id_index ON Item (id) USING BTREE";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));

    // Updates Item.
    $sql = "SELECT idItem FROM Item";
    $result = mysqli_query($mysql, $sql) or die(mysqli_error($mysql));

    while ($row = mysqli_fetch_row($result)) {
      $uuid = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);
      $sql = "UPDATE Item SET id = '".$uuid."' WHERE idItem = ".$row[0];
      mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));
    }

    mysqli_free_result($result);


    // Alters Category.
    $sql = "ALTER TABLE Category ADD id VARCHAR(255)";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));
    $sql = "CREATE INDEX id_index ON Category (id) USING BTREE";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));

    // Updates Category.
    $sql = "SELECT idCategory FROM Category";
    $result = mysqli_query($mysql, $sql) or die(mysqli_error($mysql));

    while ($row = mysqli_fetch_row($result)) {
      $uuid = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);
      $sql = "UPDATE Category SET id = '".$uuid."' WHERE idCategory = ".$row[0];
      mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));
    }

    mysqli_free_result($result);
  }

}