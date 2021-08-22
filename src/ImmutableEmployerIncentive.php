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
 * An immutable employer incentive object.
 * 
 * The employer incentive is a link between an incentive and an employer.
 * This object may contain one or more actions, which are to be executed on calls to processEvent()
 */
class ImmutableEmployerIncentive implements IEmployerIncentive
{
  /**
   * Unique id 
   * @var in
   */
  private int $id;
  
  /**
   * Employer id 
   * @var int
   */
  private int $employerId;
  
  /**
   * Associated Incentive 
   * @var IIncentive
   */
  private IIncentive $incentive;
  
  /**
   * One or more actions to be executed 
   * @var array
   */
  private array $actions;
  
  
  /**
   * 
   * @param int $id Unique id.  Set to zero for new record.
   * @param int $employerId Employer id 
   * @param IIncentive $incentive Associated incentive 
   * @param IEmployerIncentiveAction $actions One or more actions to execute during calls to processEvent()
   * @throws InvalidArgumentException
   */
  public function __construct( int $id, int $employerId, IIncentive $incentive, IEmployerIncentiveAction ...$actions )
  {
    if ( $id < 1 )
      throw new InvalidArgumentException( 'id must be greater than or equal to zero' );
    else if ( $employerId < 1 )
      throw new InvalidArgumentException( 'employerId must be greater than or equal to zero' );
    else if ( empty( $actions ))
      throw new InvalidArgumentException( 'At least one action must be specified' );
    
    $incentive->validate();
    
    if ( $incentive->getId() < 1 )
      throw new InvalidArgumentException( 'Uncommitted incentives may not be attached to employer incentives.  Please save the supplied IIncentive prior to creating this object' );
    
    $this->id = $id;
    $this->employerId = $employerId;
    $this->incentive = $incentive;
    $this->actions = $actions;
  }
  
  
  /**
   * Retrieve the employer incentive id 
   * @return int
   */
  public function getId() : int
  {
    return $this->id;
  }
  
  
  /**
   * Retrieve the employer id 
   * @return int
   */
  public function getEmployerId() : int
  {
    return $this->employerId;
  }
  
  
  /**
   * Retrieve the incentive for this employer incentive 
   * @return IIncentive
   */
  public function getIncentive() : IIncentive
  {
    return $this->incentive;
  }
  
  
  /**
   * Validate 
   * 
   * Ensure that employer id is greater than zero and that the linked incentive validates
   * 
   * @return void
   */
  public function validate() : void
  {
    //..We do not know if the attached incentive is immutable, so we validate here.
    $this->incentive->validate();
    
    //..Everything else is immutable and validated in the constructor 
  }
  
  
  /**
   * Process some event against this employer incentive.
   * 
   * 1) This object and the associated incentive are validated. Exceptions may be thrown 
   * 2) Each action supplied to the constructor are executed against the incentive event 
   * 
   * Note: Ideally, the actions would be encapsulated by some unit of work, and any exceptions would roll back 
   * any changes made by the actions.  Since this is a time-limited exercise, I have omitted the unit of work.
   * 
   * @param IIncentiveEvent $event Event to process 
   * @return void
   * @throws ValidationException
   */
  public function processEvent( IIncentiveEvent $event ) : void
  {
    $this->validate();
    $event->validate();
    
    //..See note in phpdoc
    foreach( $this->actions as $action )
    {
      $action->execute( $event );
    }
  }
}

