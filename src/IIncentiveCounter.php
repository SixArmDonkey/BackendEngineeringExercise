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
 * A mechanism for storing the number of times some employer action has been encountered.
 * 
 * Provides methods for retrieving, incrementing and resetting the count based on employer incentive id and user id.
 */
interface IIncentiveCounter 
{
  /**
   * Retrieve the count 
   * @param int $employerIncentiveId Incentive id 
   * @param int $userId User id 
   * @return int count 
   * @throws InvalidArgumentException 
   */
  public function getCount( int $employerIncentiveId, int $userId ) : int;
  
  
  /**
   * Should be an atomic operation for first incrementing the count and then returning the result.
   * 
   * The real implementation should do the following:
   * 
   * 1) Start a transaction and select the row for update - this will lock the row 
   * 2) Update the counter 
   *   a) If the current count + 1 is greater than or equal to $max, then the counter is reset and max is returned
   *   b) If the current count + 1 is less than max, then the counter is incremented and the counter value is returned
   * 3) Commit and release lock
   * 
   * @param int $employerIncentiveId Incentive id 
   * @param int $userId User id 
   * @param int $max Max count for reset 
   * @return int
   */
  public function getAndIncrementCount( int $employerIncentiveId, int $userId, int $max = 0 ) : int; 
  
  
  /**
   * Sets the count to zero.
   * If the record is not found, this will do nothing.
   * @param int $employerIncentiveId incentive id 
   * @param int $userId user id 
   * @return void
   * @throws InvalidArgumentException 
   */
  public function resetCount( int $employerIncentiveId, int $userId ) : void;
}
