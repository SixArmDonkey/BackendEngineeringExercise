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
 * A mock incentive counter for storing the number of times some employer action has been encountered.
 * 
 * Internally stores data in an array
 * 
 * As per the instructions, this implementation will only allow the count to be incremented once every 24 hours.
 */
class MockIncentiveCounter implements IIncentiveCounter
{
  /**
   * Simulated database
   * 
   * ['employerId:userId' => [count, lastUpdate]]
   * 
   * @var array 
   */
  private $db = [];
  
  /**
   * Optional delay used to prevent the count from incrementing until the elapsed time between the previous 
   * counter increment event and now is greater than interval.
   * @var int
   */
  private int $interval;
  
  
  /**
   * 
   * @param int $interval Optional delay used to prevent the count from incrementing until the elapsed time between the previous 
   * counter increment event and now is greater than interval.
   * @throws \InvalidArgumentException
   */
  public function __construct( int $interval = 60 * 60 * 24 )
  {
    if ( $interval < 0 )
      throw new \InvalidArgumentException( 'interval must be greater than or equal to zero' );
    
    $this->interval = $interval;
  }
  
  
  /**
   * Retrieve the count 
   * @param int $employerIncentiveId Incentive id 
   * @param int $userId User id 
   * @return int count 
   * @throws InvalidArgumentException 
   */
  public function getCount( int $employerIncentiveId, int $userId ) : int
  {
    $key = $this->createKey( $employerIncentiveId, $userId );
    
    if ( !isset( $this->db[$key] ))
      return 0;
    
    return reset( $this->db[$key] );
  }

  
  /**
   * Should be an atomic operation for first incrementing the count and then returning the result.
   * 
   * The real implementation should do the following:
   * 
   * 1) Start a transaction and select the row for update - this will lock the row 
   * 2) Update the counter if the last update was greater than 24 hours ago
   *   a) If the current count + 1 is greater than or equal to $max, then the counter is reset and max is returned
   *   b) If the current count + 1 is less than max, then the counter is incremented and the counter value is returned
   * 3) Commit and release lock
   * 
   * @param int $employerIncentiveId Incentive id 
   * @param int $userId User id 
   * @param int $max Max count for reset 
   * @return int
   */
  public function getAndIncrementCount( int $employerIncentiveId, int $userId, int $max = 0 ) : int
  {
    //..This is a mock implementation and is not atomic.
    $key = $this->createKey( $employerIncentiveId, $userId );
    
    //..Initialize the count if necessary 
    if ( !isset( $this->db[$key] ))
    {
      $this->db[$key] = [1,time()];
      return 1;
    }
    
    list( $count, $lastUpdate ) = $this->db[$key];
    
    //..If difference between last update and now is less than the supplied interval, do nothing
    if ( time() - $lastUpdate < $this->interval )
    {
      //..Do nothing.
      return $count;
    }
      
    //..Check for max and reset if necessary 
    if ( $max > 0 && $count + 1 >= $max )
    {
      $this->resetCount( $employerIncentiveId, $userId );
      return $max;
    }
    
    //..Increment the count and set the last update timestamp
    $this->db[$key] = [$count + 1, time()];
    
    //..Return the incremented count 
    return $count + 1;
  }
  
  
  /**
   * Sets the count to zero.
   * If the record is not found, this will do nothing.
   * @param int $employerIncentiveId incentive id 
   * @param int $userId user id 
   * @return void
   * @throws InvalidArgumentException 
   */
  public function resetCount( int $employerIncentiveId, int $userId ) : void
  {
    $key = $this->createKey( $employerIncentiveId, $userId );
    
    if ( !isset( $this->db[$key] ))
      return;
    
    //..Since the count is zero, time must also equal zero
    $this->db[$key] = [0,0];
  }
  
  
  /**
   * Retrieve the mock database composite key 
   * @param int $employerIncentiveId employer incentive id 
   * @param int $userId user id 
   * @return string db key "employerIncentiveId:userId"
   * @throws \InvalidArgumentException if employerIncentiveId or userId are less than one
   */
  private function createKey( int $employerIncentiveId, int $userId ) : string
  {
    if ( $employerIncentiveId < 1 )
      throw new \InvalidArgumentException( 'employerIncentiveId must be greater than zero' );
    else if ( $userId < 1 )
      throw new \InvalidArgumentException( 'userId must be greater than zero' );

    return (string)$employerIncentiveId . ':' . (string)$userId;    
  }
}
