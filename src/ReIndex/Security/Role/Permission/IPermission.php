<?php
/**
 * @file IPermission.php
 * @brief This file contains the IPermission interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission;


interface IPermission {

  
  function getName();


  function check();

}