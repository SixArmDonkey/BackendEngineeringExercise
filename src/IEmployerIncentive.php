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
 * Represents an incentive linked to an employer account
 */
interface IEmployerIncentive
{
  /**
   * Retrieve the employer incentive id 
   * @return int
   */
  public function getId() : int;
  
  
  /**
   * Retrieve the employer id 
   * @return int
   */
  public function getEmployerId() : int;
  
  
  /**
   * Retrieve the incentive for this employer incentive 
   * @return IIncentive
   */
  public function getIncentive() : IIncentive;
  
  
  /**
   * Validate 
   * 
   * Ensure that employer id is greater than zero and that the linked incentive validates
   * 
   * @return void
   */
  public function validate() : void;
  
  
  /**
   * Process some event against this employer incentive 
   * @param IIncentiveEvent $event Event to process 
   * @return void
   * @throws ValidationException
   */
  public function processEvent( IIncentiveEvent $event ) : void;
}

