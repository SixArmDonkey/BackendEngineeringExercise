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
 * A collection of IIncentive objects
 */
interface IIncentiveRepository
{
  /**
   * Create a new IIncentive instance.  
   * @param string $name Incentive name/caption
   * @param string $description Incentive description 
   * @return IIncentive
   * @throws InvalidArgumentException Depending on the implementation, this may throw an exception.
   */
  public function create( string $name = '', string $description = '' ) : IIncentive;
  
  
  /**
   * Retrieve an incentive by id 
   * @param int $id id 
   * @return IIncentive
   * @throws RecordNotFoundException if the incentive is not found 
   */
  public function get( int $id ) : IIncentive;
  
  
  /**
   * Retrieve a list of all active incentives 
   * @return IIncentive[] 
   */
  public function getActiveIncentives() : array;
  
  
  /**
   * Save a list of incentives.
   * If the incentive id is zero, then it is added, otherwise it is updated.
   * This SHOULD contain unique checks to avoid duplicates.
   * 
   * @param IIncentive $incentives Incentives to save 
   * @return void
   * @throws ValidationException if the incentive is invalid 
   */
  public function save( IIncentive ...$incentives ) : void;
  
  
  /**
   * Toggle the active state of some incentive 
   * @param int $id
   * @param bool $active
   * @return void
   * @throws RecordNotFoundException if the incentive is not found 
   */
  public function setActive( int $id, bool $active ) : void;
}
