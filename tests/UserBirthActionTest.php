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
use buffalokiwi\incentivedemo\UserBirthAction;
use buffalokiwi\incentivedemo\UserBirthSuccessException;
use buffalokiwi\incentivedemo\ValidationException;
use PHPUnit\Framework\TestCase;


class UserBirthActionTest extends TestCase
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
   * 1) Test that execute() calls IIncentiveEvent::validate()
   * 2) Test that calling execute() throws a successful UserBirthSuccessException message
   * 
   * @return void
   */
  public function testExecute() : void
  {
    $instance = new UserBirthAction();
    
    try {
      $instance->execute( $this->invalidMockEvent );
      $this->fail( UserBirthAction::class . '::execute() must call ' . IIncentiveEvent::class . '::validate()' );
    } catch ( ValidationException $e ) {
      //..expected
    }
    
    $this->expectException( UserBirthSuccessException::class );
    ( new UserBirthAction())->execute( $this->mockEvent );
  }
}