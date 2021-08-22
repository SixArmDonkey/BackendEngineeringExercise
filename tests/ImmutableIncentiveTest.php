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

use buffalokiwi\incentivedemo\ImmutableIncentive;
use PHPUnit\Framework\TestCase;




class ImmutableIncentiveTest extends TestCase
{
  /**
   * Test constructor
   * Test that $name must match /^[a-zA-Z0-9]+$/ or throw InvalidArgumentException 
   * @return void
   */
  public function testConstructorNameMatchesPattern() : void
  {
    //..Does nothing hopefully 
    new ImmutableIncentive( 'abcABC123', 'description', true, 1 );      
    
    //..This doesn't test much
    $this->expectException( InvalidArgumentException::class );
    new ImmutableIncentive( 'abcABC123!', 'description', true, 1 );    
  }
  
  
  /**
   * Test constructor 
   * Test that description must not be empty
   * Test that setting description to a space throws an exception 
   * @return void
   */
  public function testConstructorDescriptionNotEmpty() : void
  {
    try {
      new ImmutableIncentive( 'abcABC123', '', true, 1 );      
      $this->fail( ImmutableIncentive::class . '::__construct argument $description must throw an exception when empty' );
    } catch (Exception $ex) {
      //..Expected
    }

    try {
      new ImmutableIncentive( 'abcABC123', '  ', true, 1 );      
      $this->fail( ImmutableIncentive::class 
         . '::__construct argument $description must be trimmed and throw an exception when empty' );
    } catch (Exception $ex) {
      //..Expected
    }
    
    $this->expectNotToPerformAssertions();
  }
  
  
  /**
   * Test constructor
   * Test that $id must be greater than or equal to zero or throw InvalidArgumentException 
   * @return void
   */
  public function testConstuctorIdMustBeGreaterThanOrEqualToZero() : void
  {
    //..These should do nothing
    new ImmutableIncentive( 'abcABC123', 'description', true, 1 );
    new ImmutableIncentive( 'abcABC123', 'description', true, 0 );
    
    try {
      new ImmutableIncentive( 'abcABC123', 'description', true, -1 );
      $this->fail( ImmutableIncentive::class . '::__construct argument $id must throw InvalidArgumentException when less than zero' );
    } catch (Exception $ex) {
      //..Expected
    }
    
    $this->expectNotToPerformAssertions();
  }
  
  
  /**
   * Test that the id supplied to the constructor is returned by getId()
   * @return void
   */
  public function testGetId() : void
  {
    $instance = new ImmutableIncentive( 'abcABC123', 'description', true, 1 );
    $this->assertSame( 1, $instance->getId());
  }
  
  
  /**
   * Test that the name supplied to the constructor is returned by getName()
   * @return void
   */
  public function testGetName() : void
  {
    $instance = new ImmutableIncentive( 'abcABC123', 'description', true, 1 );
    $this->assertSame( 'abcABC123', $instance->getName());
  }


  /**
   * Test that the description supplied to the constructor is returned by getName()
   * @return void
   */
  public function testGetDescription() : void
  {
    $instance = new ImmutableIncentive( 'abcABC123', 'description', true, 1 );
    $this->assertSame( 'description', $instance->getDescription());
  }
  
  
  /**
   * Test that the active flag supplied to the constructor is returned by isActive
   * @return void
   */
  public function testisActive() : void
  {
    $instance = new ImmutableIncentive( 'abcABC123', 'description', true, 1 );
    $this->assertTrue( $instance->isActive());

    $instance = new ImmutableIncentive( 'abcABC123', 'description', false, 1 );
    $this->assertFalse( $instance->isActive());
  }  
  
  
  /**
   * Tests validate().
   * No tests here because validate does nothing()
   * @return void
   */
  public function testValidate() : void
  {
    //..No tests yet. The validate method does nothing.
    $instance = new ImmutableIncentive( 'abcABC123', 'description', false, 1 );
    $instance->validate();
    $this->expectNotToPerformAssertions();
  }
}
