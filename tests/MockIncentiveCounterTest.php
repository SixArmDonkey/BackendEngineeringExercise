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

use buffalokiwi\incentivedemo\MockIncentiveCounter;
use PHPUnit\Framework\TestCase;


class MockIncentiveCounterTest extends TestCase
{
  /**
   * Tests that $interval must be greater than or equal to zero 
   * @return void
   */
  public function testConstruct() : void
  {
    //..Expect nothing to happen 
    new MockIncentiveCounter( 0 );
    new MockIncentiveCounter( 1 );
    
    $this->expectException( InvalidArgumentException::class );
    new MockIncentiveCounter( -1 );
  }
  
  
  /**
   * 1) When supplied with the same args, test that getAndIncrementCount() returns "1" on the first call 
   * and "2" on the second call to getAndIncrementCount()
   * 
   * @return void
   */
  public function testGetAndIncrementCountReturnsCount() : void
  {
    $instance = new MockIncentiveCounter( 0 );
    
    $this->assertSame( 1, $instance->getAndIncrementCount( 1, 1 ));
    $this->assertSame( 2, $instance->getAndIncrementCount( 1, 1 ));
  }


  /**
   * This is the same test as above, but with multiple user/employer combinations in the mix.
   * This tests that the counter can internally store more than one counter.
   * 
   * @return void
   */
  public function testGetAndIncrementCountWithMultipleKeysReturnsCount() : void
  {
    $instance = new MockIncentiveCounter( 0 );
    
    $this->assertSame( 1, $instance->getAndIncrementCount( 1, 1 ));
    $this->assertSame( 2, $instance->getAndIncrementCount( 1, 1 ));
    
    $this->assertSame( 1, $instance->getAndIncrementCount( 2, 2 ));
    $this->assertSame( 2, $instance->getAndIncrementCount( 2, 2 ));
    
    $this->assertSame( 3, $instance->getAndIncrementCount( 1, 1 ));
    $this->assertSame( 3, $instance->getAndIncrementCount( 2, 2 ));
  }

    
  /**
   * 1) Test that when setting $max to 1, getAndIncrementCount() must return one each time it is called.  Call this twice.
   * 2) Test that when setting $max to 2, getAndIncrementCount() must return two after two calls
   * 3) Test that calling the method again using the same criteria as #2 returns 1 after running tests for #2.  This is 
   * due to the counter resetting internally after hitting $max.
   * 
   * Note: ensure that userId and employerId are different for tests 1 and 2 
   * @return void
   */
  public function testGetAndIncrementCountMaxArgument() : void
  {
    $instance = new MockIncentiveCounter( 0 );
    
    $this->assertSame( 1, $instance->getAndIncrementCount( 1, 1, 1 ));
    $this->assertSame( 1, $instance->getAndIncrementCount( 1, 1, 1 ));
    
    $this->assertSame( 1, $instance->getAndIncrementCount( 2, 2, 2 ));
    $this->assertSame( 2, $instance->getAndIncrementCount( 2, 2, 2 ));
    
    //..Test reset and increment 
    $this->assertSame( 1, $instance->getAndIncrementCount( 2, 2, 2 ));
  }
  
  
  /**
   * The interval supplied to the constructor is used to prevent a count from incrementing prior to the elapsed time
   * equaling interval.
   * 
   * Tests:
   * Supply an interval of 2 to the constructor.
   * 
   * 1) Test that getAndIncrementCount() allows the count to increment to one
   * 2) Test that calling getAndIncrementCount() immediately after #1 still returns one
   * 3) sleep for 2 seconds and call getAndIncrementCount() again.  This should now return 2.
   * 
   * @return void
   */
  public function testGetAndIncrementCountWithInterval() : void
  {
    $instance = new MockIncentiveCounter( 2 ); //..2 second interval
    
    //..Explicitly setting $max to zero to ensure max is not causing 1 to be returned more than once.    
    $this->assertSame( 1, $instance->getAndIncrementCount( 1, 1, 0 ));
    $this->assertSame( 1, $instance->getAndIncrementCount( 1, 1, 0 ));
    
    sleep( 2 );
    
    $this->assertSame( 2, $instance->getAndIncrementCount( 1, 1, 0 ));
  }
  
     
  /**
   * 1) Test that getCount() returns zero for an unknown counter
   * 2) Test that calling getCount() after calling getAndIncrementCount() returns the same value as getAndIncrementCount()
   * 
   * @return void
   */
  public function testGetCount() : void
  {
    $instance = new MockIncentiveCounter( 0 );
    
    $this->assertSame( 0, $instance->getCount( 1, 1 ));
    $this->assertSame( $instance->getAndIncrementCount( 1, 1, 0 ), $instance->getCount( 1, 1, ));
  }
  
  
  /**
   * 1) Test that calling getCount() after resetCount() returns zero
   * 2) Test that calling getCount() after resetCount() after getAndIncrementCount() returns zero
   * @return void
   */
  public function testResetCount() : void
  {
    $instance = new MockIncentiveCounter( 0 );
    
    $instance->resetCount( 1, 1 );
    $this->assertSame( 0, $instance->getCount( 1, 1 ));
    
    $instance->getAndIncrementCount( 1, 1, 0 );
    $this->assertSame( 1, $instance->getCount( 1, 1 ));
    $instance->resetCount( 1, 1 );
    $this->assertSame( 0, $instance->getCount( 1, 1 ));
  }
  
  
  /**
   * resetCount() must internally set the last update time to zero.  
   * 
   * 1) Test that calling getAndIncrementCount() returns 1 after calling resetCount() with an interval 
   * @return void
   */
  public function testResetWithInterval() : void
  {
    $instance = new MockIncentiveCounter( 10 );
    
    //..Testing interval is active 
    $this->assertSame( 1, $instance->getAndIncrementCount( 1, 1, 0 ));
    $this->assertSame( 1, $instance->getAndIncrementCount( 1, 1, 0 ));
    
    //..Test after reset
    $instance->resetCount( 1, 1 );
    $this->assertSame( 1, $instance->getAndIncrementCount( 1, 1, 0 ));
  }
}
