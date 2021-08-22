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

use InvalidArgumentException;


/**
 * An immutable incentive event dto
 */
class ImmutableIncentiveEvent implements IIncentiveEvent
{
  /**
   * User id 
   * @var int
   */
  private int $userId;
  
  /**
   * Employer id 
   * @var int
   */
  private int $employerId;
  
  /**
   * Event name 
   * @var string
   */
  private string $eventName;
  
  /**
   * The employer incentive id 
   * @var int
   */
  private int $employerIncentiveId;
  
  
  /**
   * @param int $userId User id 
   * @param int $employerId Employer id 
   * @param string $eventName Event name 
   * @throws InvalidArgumentException
   */
  public function __construct( int $userId, int $employerId, int $employerIncentiveId, string $eventName )
  {
    if ( $userId < 1 )
      throw new InvalidArgumentException( 'userId must be greater than zero' );
    else if ( $employerId < 1 )
      throw new InvalidArgumentException( 'employerId must be greater than zero' );
    else if ( empty( $eventName ) || !preg_match( '/^[a-zA-Z0-9\-]+$/', $eventName ))
      throw new InvalidArgumentException( 'event name must be a non-empty alphanumeric string' );
    else if ( $employerIncentiveId < 1 )
      throw new InvalidArgumentException( 'employerIncentiveId must be greater than zero' );
    
    $this->userId = $userId;
    $this->employerId = $employerId;
    $this->eventName = $eventName;
    $this->employerIncentiveId = $employerIncentiveId;}
  
  
  /**
   * Retrieve the user id 
   * @return int
   */
  public function getUserId() : int
  {
    return $this->userId;
  }
  
  
  /**
   * Retrieve the employer id 
   * @return int
   */
  public function getEmployerId() : int
  {
    return $this->employerId;
  }
  
  
  /**
   * Retrieve the event name 
   * @return string
   */
  public function getEventName() : string
  {
    return $this->eventName;
  }

  
  /**
   * Retrieve the employer incentive id 
   * @return int
   */
  public function getEmployerIncentiveId() : int
  {
    return $this->employerIncentiveId;
  }
  
  
  /**
   * Validate.
   * 
   * Ensure that all id's are greater than zero and that event name is a non-empty string matching ^[a-zA-Z0-9\-]+$
   * @throws ValidationException
   */
  public function validate() : void
  {
    //..Do nothing, this is validated in the constructor
  }
}
