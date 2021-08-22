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
use InvalidArgumentException;


/**
 * A mock incentive repository containing two records linked to "employer 1" 
 * and one of the sample incentives for the demo.
 * 
 * "Employer 1" is some employer in the system.
 * 
 * Note: I did not write tests for this since it is not really part of this library.  This would be replaced by a real version that 
 * connects to some real persistence engine.
 */
class MockIncentiveEmployerRepository implements IEmployerIncentiveRepository
{
  private const DB = [
    1 => ['employerId' => 1, 'incentiveId' => 1],
    2 => ['employerId' => 1, 'incentiveId' => 2]
  ];
  
  
  /**
   * Incentive repository 
   * @var IIncentiveRepository
   */
  private IIncentiveRepository $incentiveRepo;
  
  /**
   * IEmployerIncentive object factory 
   * 
   * f( int $id, int $employerId, IIncentive $incentive, IEmployerAction ...$actions ) : IEmployerIncentive 
   * 
   * @var Closure
   */
  private Closure $objectFactory;
  
  
  /**
   * A map of [action name => IEmployerIncentiveAction[]]
   * @var array
   */
  private array $actionMap;
  
  
  /**
   * 
   * @param IIncentiveRepository $incentiveRepo
   * @param Closure $objectFactory Object factory returning instances of IEmployerIncentive 
   * f( int $id, int $employerId, IIncentive $incentive, IEmployerAction ...$actions ) : IEmployerIncentive 
   * @param array $actionMap
   * @throws InvalidArgumentException
   */
  public function __construct( IIncentiveRepository $incentiveRepo, Closure $objectFactory, array $actionMap )
  {
    $this->incentiveRepo = $incentiveRepo;
    $this->objectFactory = $objectFactory;
    
    //..Validate the action map 
    foreach( $actionMap as $eventName => $actionList )
    {
      if ( empty( $eventName ) || !is_string( $eventName ))
        throw new \InvalidArgumentException( 'Action map keys must be non empty strings' );
      else if ( empty( $actionList ) || !is_array( $actionList ))
        throw new \InvalidArgumentException( 'Action map values must be a non-empty array' );
      
      foreach( $actionList as $action )
      {
        if ( !( $action instanceof IEmployerIncentiveAction ))
          throw new \InvalidArgumentException( 'Action map values must be an array of ' . IEmployerIncentiveAction::class );
      }
    }
    
    $this->actionMap = $actionMap;
  }
  
  
  /**
   * Create a new employer incentive record based on some existing incentive.
   * This will link an available incentive to some employer.
   * @param int $employerId the employer id 
   * @param IIncentive $incentive Incentive 
   * @return IEmployerIncentive
   * @throws InvalidArgumentException if the Incentive has not been saved or if IIncentive::validate() fails.
   */
  public function create( int $employerId, IIncentive $incentive ) : IEmployerIncentive
  {
    return $this->createEmployerIncentive( 0, $employerId, $incentive );
  }
  
  
  /**
   * Retrieve an employer incentive by id 
   * @param int $id employer incentive id 
   * @return IEmployerIncentive
   * @throws RecordNotFoundException 
   */
  public function get( int $id ) : IEmployerIncentive
  {
    if ( !isset( self::DB[$id] ))
      throw new RecordNotFoundException();
    
    return self::DB[$id];    
  }
  
  
  /**
   * Retrieve a list of incentives linked to an employer.
   * 
   * @param int $employerId employer id 
   * @return IEmployerIncentive[] Linked incentives 
   * @throws RecordNotFoundException if some linked incentive does not exist 
   */
  public function getForEmployer( int $employerId ) : array
  {
    $out = [];
    
    foreach( self::DB as $id => $row )
    {
      if ( $row['employerId'] == $employerId )
        $out[] = $this->createEmployerIncentive( $id, $row['employerId'], $this->incentiveRepo->get( $row['incentiveId'] ));
    }
    
    return $out;
  }
  
  
  /**
   * Retrieve a single employer incentive event by employer id and event name.
   * 
   * This method is extremely inefficient, and I would never write something like this outside of a mock/demo system.   
   * 
   * @param string $eventName Event name
   * @return IEmployerIncentive
   * @throws RecordNotFoundException 
   */
  public function getForEmployerByEvent( int $employerId, string $eventName ) : IEmployerIncentive
  {
    foreach( $this->getForEmployer( $employerId ) as $rec )
    {
      if ( $rec->getIncentive()->getName() == $eventName )
        return $rec;
    }
    
    throw new RecordNotFoundException();
  }
  
  
  /**
   * Given some incentive event, return the employer incentive record for processing.
   * @param IIncentiveEvent $event Incentive event 
   * @return IEmployerIncentive
   * @throws RecordNotFoundException if the employer incentive attached to the event does not exist 
   */
  public function getForEvent( IIncentiveEvent $event ) : IEmployerIncentive
  {
    $event->validate();
    
    if ( empty( $event->getEmployerIncentiveId()) || !isset( self::DB[$event->getEmployerIncentiveId()] ))
      throw new RecordNotFoundException();
    
    $row = self::DB[$event->getEmployerIncentiveId()];
    return $this->createEmployerIncentive( 
      $event->getEmployerIncentiveId(), 
      $row['employerId'], 
      $this->incentiveRepo->get( $row['incentiveId'] )
    );
  }
  
  
  /**
   * Persist an employer incentive record 
   * @param IEmployerIncentive $incentives incentives to save 
   * @return void
   */
  public function save( IEmployerIncentive ...$incentives ) : void
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
   * 
   * @param int $id
   * @param int $employerId
   * @param IIncentive $incentive
   * @return IEmployerIncentive
   * @throws InvalidArgumentException if the supplied incentive has no associated actions
   * @throws ValidationException If IIncentive or IEmployerIncentive fail to validate
   * @throws Excetion if the object factory does not return an instance of IEmployerIncentive 
   */
  private function createEmployerIncentive( int $id, int $employerId, IIncentive $incentive ) : IEmployerIncentive
  {
    $incentive->validate();
    
    //..Do we want to throw an exception here?
    if ( !isset( $this->actionMap[$incentive->getName()] ))
      throw new \InvalidArgumentException( 'This incentive does not have any associated employer actions' );
        
    $f = $this->objectFactory;
    $res = $f( $id, $employerId, $incentive, ...$this->actionMap[$incentive->getName()] );
    
    if ( !( $res instanceof IEmployerIncentive ))
    {
      throw new \Exception( 'Employer incentive factory supplied to constructor of ' . static::class 
        . ' must return an instance of ' . IEmployerIncentive::class . '. got ' 
        . (( is_object( $res )) ? get_class( $res ) : gettype( $res ))); 
    }
    
    $res->validate();
    
    return $res;
  }  
}
