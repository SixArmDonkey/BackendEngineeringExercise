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
 * Mock incentive processor
 * 
 * This will retrieve items from the queue until the queue returns null
 * For each item in the queue, if a corresponding employee incentive record can be found, the actions linked to that 
 * incentive record are executed.
 * 
 * A real implementation would also include logging capabilities.
 * 
 */
class MockIncentiveProcessor implements IIncentiveProcessor
{
  /**
   * The queue
   * @var IIncentiveQueue
   */
  private IIncentiveQueue $queue;
  
  /**
   * Produces IEmployerIncentive objects, containing the necessary code (actions) for processing the event
   * @var IEmployerIncentiveRepository
   */
  private IEmployerIncentiveRepository $employerRepo;
  
  
  /**
   * 
   * @param IIncentiveQueue $queue The queue used to retrieve incentive events for processing
   * @param IEmployerIncentiveRepository $employerRepo Produces IEmployerIncentive objects, containing 
   * the necessary code (actions) for processing the event
   */
  public function __construct( IIncentiveQueue $queue, IEmployerIncentiveRepository $employerRepo )
  {
    $this->queue = $queue;
    $this->employerRepo = $employerRepo;
  }
  
  
  
  /**
   * Process the queue until completion 
   * @return void
   */
  public function run() : void
  {    
    //..Grab an item from the queue until empty 
    while( null !== ( $curEvent = $this->queue->dequeue()))
    {
      try {
        //..Get the employer incentive by name 
        $incentive = $this->employerRepo->getForEvent( $curEvent );
        
        //..Execute the actions attached to the employer incentive object 
        $incentive->processEvent( $curEvent );
        
      } catch( RecordNotFoundException $e ) {
        //..The employer intentive associated with the event was not found 
        //..This should be logged 
      } catch( ValidationException $e ) {
        //..The event or employer incentive object failed validation 
        //..This should be logged 
      }
    }
  }
}

