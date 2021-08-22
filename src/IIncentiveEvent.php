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
 * Inventive events are used to queue some user action for processing.
 * This can be anything.
 */
interface IIncentiveEvent
{
  /**
   * Retrieve the user id 
   * @return int
   */
  public function getUserId() : int;
  
  
  /**
   * Retrieve the employer id 
   * @return int
   */
  public function getEmployerId() : int;
  
  
  /**
   * Retrieve the event name 
   * @return string
   */
  public function getEventName() : string;
  
 
  /**
   * Retrieve the employer incentive id 
   * @return int
   */
  public function getEmployerIncentiveId() : int;
      
  
  /**
   * Validate.
   * 
   * Ensure that all id's are greater than zero and that event name is a non-empty string matching ^[a-zA-Z0-9\-]+$
   * @throws ValidationException
   */
  public function validate() : void;
}
