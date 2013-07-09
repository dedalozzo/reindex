<?php

//! @file RestoreCommand.php
//! @brief This file contains the RestoreCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


//! @brief Restores Programmazione.it v6.4 MySQL database.
//! @nosubgrouping
class RestoreCommand  extends AbstractCommand {


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("restore");
    $this->setDescription("Restores Programmazione.it v6.4 MySQL database.");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {
    $mysql = $this->_di['mysql'];

    // Alters Member.
    $sql = "DROP INDEX id_index ON Member";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));
    $sql = "ALTER TABLE Member DROP id";
    $result = mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));

    // Alters Item.
    $sql = "DROP INDEX id_index ON Item";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));
    $sql = "ALTER TABLE Item DROP id";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));

    // Alters Category.
    $sql = "DROP INDEX id_index ON Category";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));
    $sql = "ALTER TABLE Category DROP id";
    mysqli_real_query($mysql, $sql) or die(mysqli_error($mysql));
  }

}