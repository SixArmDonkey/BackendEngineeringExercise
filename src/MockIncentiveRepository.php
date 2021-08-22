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

use Closure;
use Exception;
use InvalidArgumentException;


/**
 * A mock incentive repository containing two sample incentives for the demo.
 * 
 * Note: I did not write tests for this since it is not really part of this library.  This would be replaced by a real version that 
 * connects to some real persistence engine.
 */
class MockIncentiveRepository implements IIncentiveRepository
{
  private const DB = [
    1 => ['name' => 'data-logged-5-sequential-days', 'description' => 'The user has logged data five days in a row', 'active' => true],
    2 => ['name' => 'user-birth', 'description' => 'The user has reported a birth', 'active' => true]      
  ];
  
  
  /**
   * Creates instances of IIncentive 
   * 
   * f( int $id, string $name, string $description, bool $isActive ) : IIncentive 
   * 
   * @var Closure
   */
  private Closure $incentiveFactory;

  
  /**
   * @param Closure $incentiveFactory An object factory creating instances of IIncentive 
   * f( int $id, string $name, string $description, bool $isActive ) : IIncentive
   */
  public function __construct( Closure $incentiveFactory )
  {
    $this->incentiveFactory = $incentiveFactory;
  }
  
  
  /**
   * Create a new IIncentive instance.  
   * @param string $name Incentive name/caption
   * @param string $description Incentive description 
   * @return IIncentive
   * @throws InvalidArgumentException Depending on the implementation, this may throw an exception.
   */
  public function create( string $name = '', string $description = '' ) : IIncentive
  {
    return $this->createIncentive( 0, $name, $description, true );
  }
  
  
  /**
   * Retrieve an incentive by id 
   * @param int $id id 
   * @return IIncentive
   * @throws RecordNotFoundException if the incentive is not found 
   */
  public function get( int $id ) : IIncentive
  {
    if ( !isset( self::DB[$id] ))
      throw new RecordNotFoundException();
    
    $row = self::DB[$id];
    return $this->createIncentive( $id, $row['name'], $row['description'], $row['active'] );
  }
  
  
  /**
   * Retrieve a list of all active incentives 
   * @return IIncentive[] 
   */
  public function getActiveIncentives() : array
  {
    $out = [];
    foreach( self::DB as $row )
    {
      if ( $row['active'] )
        $out[] = $this->createIncentive( $row['id'], $row['name'], $row['description'], $row['active'] );
    }
    
    return $out;
  }
  
  
  /**
   * Save a list of incentives.
   * If the incentive id is zero, then it is added, otherwise it is updated.
   * This SHOULD contain unique checks to avoid duplicates.
   * 
   * Note: This should use transactions.  
   * 
   * @param IIncentive $incentives Incentives to save 
   * @return void
   * @throws ValidationException if the incentive is invalid 
   */
  public function save( IIncentive ...$incentives ) : void
  {
    foreach( $incentives as $i )
    {
      $i->validate();
      
      if ( empty( $i->getId()))
      {
        //..This is a new record
        throw new Exception( 'New records may not be saved to the mock repository' );
      }
      else
      {
        //..Update
        //..Do nothing, this is a demo
      }
    }
  }
  
  
  /**
   * Toggle the active state of some incentive 
   * @param int $id
   * @param bool $active
   * @return void
   * @throws RecordNotFoundException if the incentive is not found 
   */
  public function setActive( int $id, bool $active ) : void
  {
    if ( !isset( self::DB[$id] ))
      throw new RecordNotFoundException();
    
    //..Do nothing as part of the demo
    
    //..This would update the active flag 
  }
  
  
  /**
   * Creates an incentive via the incentive factory 
   * @param int $id id 
   * @param string $name name 
   * @param string $description description 
   * @param bool $isActive active state
   * @return IIncentive the incentive 
   * @throws Exception If the factory does not return an instance of IIncentive 
   */
  private function createIncentive( int $id, string $name, string $description, bool $isActive ) : IIncentive 
  {
    $f = $this->incentiveFactory;
    
    $res = $f( $id, $name, $description, $isActive );
    
    if ( !( $res instanceof IIncentive ))
    {
      throw new Exception( 'Incentive factory supplied to constructor of ' . static::class 
        . ' must return an instance of ' . IIncentive::class . ' got ' 
        . (( is_object( $res )) ? get_class( $res ) : gettype( $res )));
    }
    
    return $res;
  }
}
