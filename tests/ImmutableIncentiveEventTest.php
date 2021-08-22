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

use buffalokiwi\incentivedemo\ImmutableIncentiveEvent;
use PHPUnit\Framework\TestCase;


class ImmutableIncentiveEventTest extends TestCase
{
 /**
   * Test constructor
   * Test that $eventName must match /^[a-zA-Z0-9]+$/ or throw InvalidArgumentException 
   * @return void
   */
  public function testConstructorEventNameMatchesPattern() : void
  {
    //..Does nothing hopefully 
    new ImmutableIncentiveEvent( 1, 2, 1, 'abcABC123' );
    
    //..This doesn't test much    
    $this->expectException( InvalidArgumentException::class );
    new ImmutableIncentiveEvent( 1, 2, 1, '' );
  }
  
  
  /**
   * Test constructor
   * Test that $userId must be greater than zero or throw InvalidArgumentException 
   * @return void
   */
  public function testConstuctorUserIdMustBeGreaterThanZero() : void
  {
    //..These should do nothing
    new ImmutableIncentiveEvent( 1, 2, 1, 'abcABC123' );
    
    try {
      new ImmutableIncentiveEvent( 0, 2, 1, 'abcABC123' );

      $this->fail( ImmutableIncentiveEvent::class . '::__construct argument $userId must throw InvalidArgumentException when less than one' );
    } catch (Exception $ex) {
      //..Expected
    }
    
    
    try {
      new ImmutableIncentiveEvent( -1, 2, 1, 'abcABC123' );

      $this->fail( ImmutableIncentiveEvent::class . '::__construct argument $userId must throw InvalidArgumentException when less than one' );
    } catch (Exception $ex) {
      //..Expected
    }
    
    
    $this->expectNotToPerformAssertions();
  }


  /**
   * Test constructor
   * Test that $employerId must be greater than zero or throw InvalidArgumentException 
   * @return void
   */
  public function testConstuctorEmployerIdMustBeGreaterThanZero() : void
  {
    //..These should do nothing
    new ImmutableIncentiveEvent( 1, 1, 1, 'abcABC123' );

    try {
      new ImmutableIncentiveEvent( 1, 0, 1, 'abcABC123' );

      $this->fail( ImmutableIncentiveEvent::class . '::__construct argument $employerId must throw InvalidArgumentException when less than one' );
    } catch (Exception $ex) {
      //..Expected
    }
    
    try {
      new ImmutableIncentiveEvent( 1, -1, 1, 'abcABC123' );

      $this->fail( ImmutableIncentiveEvent::class . '::__construct argument $employerId must throw InvalidArgumentException when less than one' );
    } catch (Exception $ex) {
      //..Expected
    }
    
    $this->expectNotToPerformAssertions();
  }

  

  /**
   * Test constructor
   * Test that $employerIncentiveId must be greater than zero or throw InvalidArgumentException 
   * @return void
   */
  public function testConstuctorEmployerIncentiveIdMustBeGreaterThanZero() : void
  {
    //..These should do nothing
    new ImmutableIncentiveEvent( 1, 1, 1, 'abcABC123' );

    try {
      new ImmutableIncentiveEvent( 1, 0, 0, 'abcABC123' );

      $this->fail( ImmutableIncentiveEvent::class . '::__construct argument $employerIncentiveId must throw InvalidArgumentException when less than one' );
    } catch (Exception $ex) {
      //..Expected
    }
    
    try {
      new ImmutableIncentiveEvent( 1, 1, -1, 'abcABC123' );

      $this->fail( ImmutableIncentiveEvent::class . '::__construct argument $employerIncentiveId must throw InvalidArgumentException when less than one' );
    } catch (Exception $ex) {
      //..Expected
    }
    
    $this->expectNotToPerformAssertions();
  }  
  
  
  /**
   * Test that the user id supplied to the constructor is returned by getId()
   * @return void
   */
  public function testGetUserId() : void
  {
    $instance = new ImmutableIncentiveEvent( 1, 2, 1, 'abcABC123' );
    $this->assertSame( 1, $instance->getUserId());
  }
  
  
  /**
   * Test that the employer id supplied to the constructor is returned by getId()
   * @return void
   */
  public function testGetEmployerId() : void
  {
    $instance = new ImmutableIncentiveEvent( 1, 2, 1, 'abcABC123' );
    $this->assertSame( 2, $instance->getEmployerId());
  }
  
  
  /**
   * Test that the event name supplied to the constructor is returned by getId()
   * @return void
   */
  public function testGetEventName() : void
  {
    $instance = new ImmutableIncentiveEvent( 1, 2, 1, 'abcABC123' );
    $this->assertSame( 'abcABC123', $instance->getEventName());
  }
  
  
  /**
   * Tests validate().
   * No tests here because validate does nothing()
   * @return void
   */
  public function testValidate() : void
  {
    //..No tests yet. The validate method does nothing.
    $instance = new ImmutableIncentiveEvent( 1, 2, 1, 'abcABC123' );
    $instance->validate();
    $this->expectNotToPerformAssertions();
  }  
}