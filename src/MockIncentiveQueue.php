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


use buffalokiwi\incentivedemo\IIncentiveEvent;
use buffalokiwi\incentivedemo\IIncentiveQueue;


/**
 * A mock queue.
 * 
 * Elements are added to the end of the queue when enqueue() is called.
 * Elements are removed from the head of the queue when dequeue() is called.
 */
class MockIncentiveQueue implements IIncentiveQueue
{
  /**
   * The queue 
   * @var array
   */
  private array $queue = [];
  
  /**
   * Factory for creating IIncentiveEvent instances 
   * f( int $userId, int $employerId, string $eventName ) : IIncentiveEvent
   * @var \Closure
   */
  private \Closure $eventFactory;
  
  
  /**
   * @param \Closure $eventFactory Factory for creating IIncentiveEvent instances 
   * f( int $userId, int $employerId, string $eventName ) : IIncentiveEvent
   */
  public function __construct( \Closure $eventFactory )
  {
    $this->eventFactory = $eventFactory;
  }
   
  
  /**
   * Enqueue an event for processing 
   * @param IIncentiveEvent $event event 
   * @return void
   */
  public function enqueue( IIncentiveEvent $event ) : void
  {
    $event->validate();
    $this->queue[] = $event;
  }
  
  
  /**
   * Retrieve an event from the queue.
   * If the queue is empty, this returns null.
   * @return IIncentiveEvent|null
   */
  public function dequeue() : ?IIncentiveEvent
  {
    if ( empty( $this->queue ))
      return null;
    
    return array_shift( $this->queue );
  }  
  
  
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
  public function createEvent( int $userId, int $employerId, string $eventName ) : IIncentiveEvent
  {
    $f = $this->eventFactory;
    $res = $f( $userId, $employerId, $eventName );
    if ( !( $res instanceof IIncentiveEvent ))
    {
      throw new \Exception( static::class . ' constructor argument $eventFactory must be a closure that returns instances of ' 
         . IIncentiveEvent::class );
    }
    
    //..Validate 
    $res->validate();
    
    return $res;
  }
}
