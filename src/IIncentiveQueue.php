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
 * A queue for deferred incentive processing 
 */
interface IIncentiveQueue
{
  /**
   * Enqueue an event for processing 
   * @param IIncentiveEvent $event event 
   * @return void
   */
  public function enqueue( IIncentiveEvent $event ) : void;
  
  
  /**
   * Retrieve an event from the queue.
   * If the queue is empty, this returns null.
   * @return IIncentiveEvent|null
   */
  public function dequeue() : ?IIncentiveEvent;
  
  
  /**
   * This may not be the best place for this, but we need a factor for creating inventive events.
   * Since the logger system will have no knowledge of anything other than the event name, we need to reference the 
   * employer incentive repository to retrieve the incentive data for deferred processing.
   * 
   * @param int $userId User id 
   * @param int $employerId Employer id 
   * @param string $eventName incentive event name 
   * @return IIncentiveEvent
   * @throws ValidationException if the incentive event fails to validate
   * @throws RecordNotFoundException if the incentive record is not found
   * @throws Exception if the event factory does not return an instance of IIncentiveEvent
   */
  public function createEvent( int $userId, int $employerId, string $eventName ) : IIncentiveEvent;
}
