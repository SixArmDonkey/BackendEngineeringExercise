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

use buffalokiwi\incentivedemo\DataLoggedFor5DaysException;
use buffalokiwi\incentivedemo\IIncentiveCounter;
use buffalokiwi\incentivedemo\IIncentiveEvent;
use buffalokiwi\incentivedemo\SequentialDataAction;
use buffalokiwi\incentivedemo\ValidationException;
use PHPUnit\Framework\TestCase;


class SequentialDataActionTest extends TestCase
{
  /**
   * Mock incentive counter
   * @var IIncentiveCounter
   */
  private IIncentiveCounter $mockCounter;
  
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
    $this->mockCounter = $this->getMockBuilder( IIncentiveCounter::class )->getMock();
    $this->mockCounter->method( 'getAndIncrementCount' )->willReturn( 0 );
    
    $this->mockEvent = $this->getMockBuilder( IIncentiveEvent::class )->getMock();
    
    $this->invalidMockEvent = $this->getMockBuilder( IIncentiveEvent::class )->getMock();
    $this->invalidMockEvent->method( 'validate' )->will( $this->throwException( new ValidationException()));
  }
  
  
  
  /**
   * Test that the constructor argument $actionsToAward throws an InvalidArgumentException when less than 2.
   * @return void
   */
  public function testConstruct() : void
  {
    //..Should do nothing
    new SequentialDataAction( 2, $this->mockCounter );
    
    $this->expectException( InvalidArgumentException::class );
    new SequentialDataAction( 1, $this->mockCounter );
  }
  
  
  /**
   * 1) Test that execute() calls IIncentiveEvent::validate()
   * 2) Test that calling execute() when IIncentiveCounter::getAndIncrementCount() returns a value less that two does nothing
   * 3) Test that calling execute() when IIncentiveCounter::getAndIncrementCount() returns a value equal to the 
   * value of the $actionsToAward argument supplied to the constructor throws a DataLoggedFor5DaysException
   * 
   * @return void
   */
  public function testExecute() : void
  {
    $instance = new SequentialDataAction( 2, $this->mockCounter );
    
    try {
      $instance->execute( $this->invalidMockEvent );
      $this->fail( SequentialDataAction::class . '::execute() must call ' . IIncentiveEvent::class . '::validate()' );
    } catch ( ValidationException $e ) {
      //..expected
    }
    
    
    $counter = $this->getMockBuilder( IIncentiveCounter::class )->getMock();
    $counter->method( 'getAndIncrementCount' )->willReturn( 1 );
    
    //..This should do nothing 
    ( new SequentialDataAction( 2, $counter ))->execute( $this->mockEvent );
    
    //..Pretend the counter hit 2 and expect the successful exception
    $counter = $this->getMockBuilder( IIncentiveCounter::class )->getMock();
    $counter->method( 'getAndIncrementCount' )->willReturn( 2 );    
    $this->expectException( DataLoggedFor5DaysException::class );
    ( new SequentialDataAction( 2, $counter ))->execute( $this->mockEvent );
  }
}
