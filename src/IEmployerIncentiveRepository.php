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

use InvalidArgumentException;


/**
 * A collection of IEmployerIncentive objects 
 */
interface IEmployerIncentiveRepository
{
  /**
   * Create a new employer incentive record based on some existing incentive.
   * This will link an available incentive to some employer.
   * @param int $employerId the employer id 
   * @param IIncentive $incentive Incentive 
   * @return IEmployerIncentive
   * @throws InvalidArgumentException if the Incentive has not been saved or if IIncentive::validate() fails.
   */
  public function create( int $employerId, IIncentive $incentive ) : IEmployerIncentive;
  
  
  /**
   * Retrieve an employer incentive by id 
   * @param int $id employer incentive id 
   * @return IEmployerIncentive
   * @throws RecordNotFoundException 
   */
  public function get( int $id ) : IEmployerIncentive;
  
  
  /**
   * Retrieve a list of incentives linked to an employer
   * @param int $employerId employer id 
   * @return IEmployerIncentive[] Linked incentives 
   */
  public function getForEmployer( int $employerId ) : array;
  
  
  /**
   * Given some incentive event, return the employer incentive record for processing.
   * @param IIncentiveEvent $event Incentive event 
   * @return IEmployerIncentive
   * @throws RecordNotFoundException if the employer incentive attached to the event does not exist 
   */
  public function getForEvent( IIncentiveEvent $event ) : IEmployerIncentive;
  
  
  /**
   * Retrieve a single employer incentive event by employer id and event name.
   * 
   * @param string $eventName Event name
   * @return IEmployerIncentive
   * @throws RecordNotFoundException 
   */
  public function getForEmployerByEvent( int $employerId, string $eventName ) : IEmployerIncentive;  
  
  
  /**
   * Persist an employer incentive record 
   * @param IEmployerIncentive $incentives incentives to save 
   * @return void
   */
  public function save( IEmployerIncentive ...$incentives ) : void;
}
