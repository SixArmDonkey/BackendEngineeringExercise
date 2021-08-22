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

use buffalokiwi\incentivedemo\IEmployerIncentiveAction;
use buffalokiwi\incentivedemo\IIncentive;
use buffalokiwi\incentivedemo\IIncentiveEvent;
use buffalokiwi\incentivedemo\ImmutableEmployerIncentive;
use buffalokiwi\incentivedemo\UserBirthSuccessException;
use buffalokiwi\incentivedemo\ValidationException;
use PHPUnit\Framework\TestCase;




class ImmutableEmployerIncentiveTest extends TestCase
{
  /**
   * A valid IIncentive mock 
   * @var IIncentive
   */
  private IIncentive $mockIncentive;
  
  /**
   * An IIncentive mock where id is zero and validate will throw a ValidationException
   * @var IIncentive
   */
  private IIncentive $invalidMockIncentive;
  
  /**
   * An IIncentive mock where id is zero and validate will NOT throw a ValidationException
   * @var IIncentive
   */
  private IIncentive $invalidMockIncentiveNoException;
  
  /**
   * Mock an incentive action that does nothing 
   * @var IEmployerIncentiveAction
   */
  private IEmployerIncentiveAction $mockAction;
  
  /**
   * Mock incentive event
   * @var IIncentiveEvent
   */
  private IIncentiveEvent $mockEvent;
  
  /**
   * Mock incentive event where validate() throws a ValidationException
   * @var IIncentiveEvent
   */
  private IIncentiveEvent $invalidMockEvent;
  
  
  public function setUp() : void
  {
    //..A valid IIncentive mock 
    $this->mockIncentive = $this->getMockBuilder( IIncentive::class )->getMock();
    $this->mockIncentive->method( 'getId' )->willReturn( 1 );
    
    //..An IIncentive mock where id is zero and validate will throw a ValidationException
    $this->invalidMockIncentive = $this->getMockBuilder( IIncentive::class )->getMock();
    $this->invalidMockIncentive->method( 'getId' )->willReturn( 0 );
    $this->invalidMockIncentive->method( 'validate' )->will( $this->throwException( new ValidationException()));

    //..An IIncentive mock where id is zero and validate will NOT throw a ValidationException
    $this->invalidMockIncentiveNoException = $this->getMockBuilder( IIncentive::class )->getMock();
    $this->invalidMockIncentiveNoException->method( 'getId' )->willReturn( 0 );

    //..Mock an incentive action that does nothing 
    $this->mockAction = $this->getMockBuilder( IEmployerIncentiveAction::class )->getMock();
    
    //..Mock incentive event
    $this->mockEvent = $this->getMockBuilder( IIncentiveEvent::class )->getMock();
    $this->mockEvent->method( 'getUserId' )->willReturn( 1 );
    $this->mockEvent->method( 'getEmployerId' )->willReturn( 1 );
    $this->mockEvent->method( 'getEventName' )->willReturn( 'testevent' );    
    
    //..Mock incentive event where validate() throws a ValidationException
    $this->invalidMockEvent = $this->getMockBuilder( IIncentiveEvent::class )->getMock();
    $this->invalidMockEvent->method( 'getUserId' )->willReturn( 1 );
    $this->invalidMockEvent->method( 'getEmployerId' )->willReturn( 1 );
    $this->invalidMockEvent->method( 'getEventName' )->willReturn( 'testevent' ); 
    $this->invalidMockEvent->method( 'validate' )->will( $this->throwException( new ValidationException()));    
  }
  
  
  /**
   * Tests constructor validation
   * $id must be greater than zero
   * @return void
   */
  public function testConstructorIdMustBeGreaterThanZero() : void
  {
    try {
      new ImmutableEmployerIncentive( 0, 1, $this->mockIncentive, $this->mockAction );
      $this->fail( ImmutableEmployerIncentive::class 
        . '::__construct must throw \InvalidArgumentException when $id is less than one' );
    } catch ( InvalidArgumentException $e ) {
      //..Expected 
    }
    
    $this->expectNotToPerformAssertions();
  }
  
  
  /**
   * Tests constructor validation
   * $employerId must be greater than zero 
   * @return void
   */
  public function testConstructorEmployerIdMustBeGreaterThanZero() : void
  {
    try {
      new ImmutableEmployerIncentive( 1, 0, $this->mockIncentive, $this->mockAction );
      $this->fail( ImmutableEmployerIncentive::class 
        . '::__construct must throw \InvalidArgumentException when $employerId is less than one' );
    } catch ( InvalidArgumentException $e ) {
      //..Expected 
    }    
    
    $this->expectNotToPerformAssertions();
  }
  
  
  /**
   * Tests constructor validation 
   * 1) Test that passing the mock incentive does nothing
   * 2) Test that passing the invalid mock incentive exception throws a ValidationException 
   * 3) Test that passing the invalid incentive mock causes an InvalidArgumentException to be thrown when $incentive->getId() returns zero.
   * @return void
   */
  public function testConstructorIIncentiveValidate() : void
  {
    //..This should do nothing 
    new ImmutableEmployerIncentive( 1, 1, $this->mockIncentive, $this->mockAction );
    
    try {
      new ImmutableEmployerIncentive( 1, 1, $this->invalidMockIncentive, $this->mockAction );
      $this->fail( ImmutableEmployerIncentive::class . '::__construct must throw ' . ValidationException::class 
        . ' when $incentive (IIncentive::validate()) throws a ValidationException' );
    } catch ( ValidationException $e ) {
      //..Expected 
    }    
        
    try {
      new ImmutableEmployerIncentive( 1, 1, $this->invalidMockIncentiveNoException, $this->mockAction );
      $this->fail( ImmutableEmployerIncentive::class . '::__construct must throw \InvalidArgumentException '
        . ' when $incentive id (IIncentive::getId()) returns zero.' );
    } catch ( InvalidArgumentException $e ) {
      //..Expected 
    }    
    
    
    $this->expectNotToPerformAssertions();    
  }
  
  
  /**
   * Tests the constructor
   * Test that passing zero IEmployerIncentiveAction objects throws an exception 
   */
  public function testConstructorEmployerIncentiveActionListNotEmpty() : void
  {
    try {
      new ImmutableEmployerIncentive( 1, 1, $this->mockIncentive );
      $this->fail( ImmutableEmployerIncentive::class . '::__construct must throw \InvalidArgumentException when zero ' 
        . IEmployerIncentiveAction::class . ' objects are supplied' );
    } catch ( InvalidArgumentException $e ) {
      //..Expected 
    }
    
    $this->expectNotToPerformAssertions();
  }
  
  
  /**
   * Test that getId() returns the same value passed to the constructor 
   * @return void
   */
  public function testGetId() : void
  {
    $instance = new ImmutableEmployerIncentive( 1, 2, $this->mockIncentive, $this->mockAction );
    
    $this->assertSame( 1, $instance->getId());    
  }
  
  
  /**
   * Test that getEmployerId() returns the same value passed to the constructor
   * @return void
   */
  public function testGetEmployerId() : void
  {
    $instance = new ImmutableEmployerIncentive( 1, 2, $this->mockIncentive, $this->mockAction );    
    $this->assertSame( 2, $instance->getEmployerId());
  }
  
  
  /**
   * Test that getIncentive() returns the same value passed to the constructor
   * @return void
   */
  public function testGetIncentive() : void
  {
    $instance = new ImmutableEmployerIncentive( 1, 2, $this->mockIncentive, $this->mockAction );    
    $this->assertSame( $this->mockIncentive, $instance->getIncentive());    
  }
  
  
  /**
   * Test that validate() calls IIncentive::validate on the object supplied to the constructor 
   * @return void
   */
  public function testValidate() : void
  {
    $incentive = $this->getMockBuilder( IIncentive::class )->getMock();
    $incentive->method( 'getId' )->willReturn( 1 );
    
    try {
      $instance = new ImmutableEmployerIncentive( 1, 2, $incentive, $this->mockAction );
      
      //..Adding this here because the constructor calls validate()
      $incentive->method( 'validate' )->will( $this->throwException( new ValidationException()));
      
      //..Call validate()
      $instance->validate();
      
      $this->fail( ImmutableEmployerIncentive::class . '::validate() must throw ' . ValidationException::class 
        . ' when the internal incentive (IIncentive::validate()) throws a ValidationException' );
    } catch ( ValidationException $e ) {
      //..Expected 
    }
    
    $this->expectNotToPerformAssertions();    
  }
  
  
  /**
   * 1) Test that validate() is called
   * 2) Test that IIncentiveEvent::validate() is called 
   * 3) Ensure that each supplied action's execute() method is called 
   * @return void
   */
  public function testProcessEvent() : void
  {
    $incentive = $this->getMockBuilder( IIncentive::class )->getMock();
    $incentive->method( 'getId' )->willReturn( 1 );
    
    try {
      $instance = new ImmutableEmployerIncentive( 1, 2, $incentive, $this->mockAction );
      
      //..Adding this here because the constructor calls validate()
      $incentive->method( 'validate' )->will( $this->throwException( new ValidationException()));
      
      //..Test that validate() is called 
      $instance->processEvent( $this->mockEvent );
      
      $this->fail( ImmutableEmployerIncentive::class . '::processEvent() must call validate()' );
    } catch ( ValidationException $e ) {
      //..Expected 
    }
    
    
    try {
      $instance = new ImmutableEmployerIncentive( 1, 2, $this->mockIncentive, $this->mockAction );
      
      //..Test that validate() is called 
      $instance->processEvent( $this->invalidMockEvent );
      
      $this->fail( ImmutableEmployerIncentive::class . '::processEvent() must call ' 
        . IIncentiveEvent::class . '::validate()' );
    } catch ( ValidationException $e ) {
      //..Expected
    }
    
    
    try {
      $action1 = $this->getMockBuilder( IEmployerIncentiveAction::class )->getMock();
      //..Using UserBirthSuccessException to have something unuque
      $action1->method( 'execute' )->will( $this->throwException( new UserBirthSuccessException()));
      
      $instance = new ImmutableEmployerIncentive( 1, 2, $this->mockIncentive, $action1 );
      
      //..Test that action execute is called 
      $instance->processEvent( $this->mockEvent );
      
      $this->fail( ImmutableEmployerIncentive::class . '::processEvent() must call ' 
        . IEmployerIncentiveAction::class . '::execute()' );
    } catch ( UserBirthSuccessException $e ) {
      //..Expected
    }
    
    
    try {
      $action1 = $this->getMockBuilder( IEmployerIncentiveAction::class )->getMock();
      
      $action2 = $this->getMockBuilder( IEmployerIncentiveAction::class )->getMock();
      //..Using UserBirthSuccessException to have something unuque
      $action2->method( 'execute' )->will( $this->throwException( new UserBirthSuccessException()));
      
      $instance = new ImmutableEmployerIncentive( 1, 2, $this->mockIncentive, $action1, $action2 );
      
      //..Test that action execute is called on the second action
      //..This should ensure that there's a loop and that multiple actions are handled.
      $instance->processEvent( $this->mockEvent );
      
      $this->fail( ImmutableEmployerIncentive::class . '::processEvent() must call ' 
        . IEmployerIncentiveAction::class . '::execute() for each action supplied to the constructor' );
    } catch ( UserBirthSuccessException $e ) {
      //..Expected
    }
    
    $this->expectNotToPerformAssertions();    
  }
}
