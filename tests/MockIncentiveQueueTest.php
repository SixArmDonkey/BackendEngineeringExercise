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

use buffalokiwi\incentivedemo\IIncentiveEvent;
use buffalokiwi\incentivedemo\MockIncentiveQueue;
use buffalokiwi\incentivedemo\ValidationException;
use PHPUnit\Framework\TestCase;


class MockIncentiveQueueTest extends TestCase
{
  
  /**
   * Mock incentive event 
   * @var IIncentiveEvent
   */
  private IIncentiveEvent $mockEvent;
  
  
  /**
   * A mock incentive event that throws an exception when validate() is called 
   * @var IIncentiveEvent
   */
  private IIncentiveEvent $invalidMockEvent;
  
  
  
  public function setUp() : void
  {
    $this->mockEvent = $this->getMockBuilder( IIncentiveEvent::class )->getMock();
    
    $this->invalidMockEvent = $this->getMockBuilder( IIncentiveEvent::class )->getMock();
    $this->invalidMockEvent->method( 'validate' )->will( $this->throwException( new ValidationException()));
  }
  

  /**
   * Ensure that the queue calls IIncentiveEvent::validate()
   * @return void
   */
  public function testEnqueueCallsValidate() : void
  {
    $this->expectException( ValidationException::class );
    ( new MockIncentiveQueue( function() {} ))->enqueue( $this->invalidMockEvent );
  }
  

  /**
   * 1) Test that enqueue accepts an event
   * 2) Test that dequeue returns the same event from #1 
   * 3) Test that calling dequeue again returns null
   * @return void
   */
  public function testEnqueueDequeue() : void
  {
    $queue = new MockIncentiveQueue( function() {} );
    
    $queue->enqueue( $this->mockEvent );
    $this->assertSame( $this->mockEvent, $queue->dequeue());
    $this->assertNull( $queue->dequeue());
  }
  
  
  /**
   * Test that create event 
   * 1) Uses the object factory supplied to the constructor to return instances of IIncentiveEvent 
   * 2) calls IIncentiveEvent::validate()
   * @return void
   */
  public function testCreateEvent() : void
  {
    $this->assertSame( $this->mockEvent, ( new MockIncentiveQueue( fn() => $this->mockEvent ))->createEvent( 1, 1, 'name' ));
    $this->expectException( ValidationException::class );
    ( new MockIncentiveQueue( fn() => $this->invalidMockEvent ))->createEvent( 1, 1, 'name' );
  }
}
