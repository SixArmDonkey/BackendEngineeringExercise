<?php
/**
 * This file is subject to the terms and conditions defined in
 * file 'LICENSE.txt', which is part of this source code package.
 *
 * Copyright (c) 2021 John Quinn <johnquinn3@gmail.com>
 * 
 * @author John Quinn
 */

declare( strict_types=1 );

namespace buffalokiwi\incentivedemo;


/**
 * Represents some incentive.
 * This is the master incentive record, and does not contain any specifics about 
 * employers or processing.
 */
interface IIncentive
{
  /**
   * Retrieve the incentive id.
   * This returns zero if the record has not yet been persisted.
   * @return int
   */
  public function getId() : int;
  
  
  /**
   * Retrieve the incentive name 
   * @return string
   */
  public function getName() : string;
  
  
  /**
   * Retrieve the incentive description
   * @return string
   */
  public function getDescription() : string;
  
  
  /**
   * Test if the incentive record is active.
   * Active means available for employers to use in for engagement programs.
   * @return bool
   */
  public function isActive() : bool;
  
  
  /**
   * Validate the incentive record.
   * 
   * 1) Ensure that name is a non-empty string matching ^[a-zA-Z0-9\-\-]+$
   * 2) Description must be a non-empty string
   * 
   * @return void
   * @throws ValidationException
   */
  public function validate() : void;
}

