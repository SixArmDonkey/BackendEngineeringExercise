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
 * Used when an incentive award requires multiple events to be logged prior to triggering the award.
 */
class SequentialDataAction implements IEmployerIncentiveAction
{
  /**
   * Number of actions required to receive an award
   * @var int
   */
  private int $actionsToAward;
  
  /**
   * Handles persisting number of actions for combinations of employer incentive id to user
   * @var IIncentiveCounter
   */  
  private IIncentiveCounter $counter;
  
  
  /**
   * 
   * @param int $actionsToAward Number of actions required to receive an award
   * @param IIncentiveCounter $counter Handles persisting number of actions for combinations of employer incentive id to user
   */
  public function __construct( int $actionsToAward, IIncentiveCounter $counter )
  {
    if ( $actionsToAward < 2 )
      throw new \InvalidArgumentException( 'Minimum value of $actionsToAward is 2.  If less than 2, why use this?' );
    
    $this->actionsToAward = $actionsToAward;
    $this->counter = $counter;
  }
  
  
  /**
   * Execute the action 
   * @param IIncentiveEvent $event Related event 
   * @return void
   */
  public function execute( IIncentiveEvent $event ) : void
  {
    $event->validate();
    
    $cur = $this->counter->getAndIncrementCount( $event->getEmployerIncentiveId(), $event->getUserId(), $this->actionsToAward );
    
    if ( $cur >= $this->actionsToAward )
    {
      //..award the user 
      throw new DataLoggedFor5DaysException();
    }    
  }
}
