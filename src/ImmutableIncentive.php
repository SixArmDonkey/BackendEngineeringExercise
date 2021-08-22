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



/**
 * Simple immutable incentive for demo.
 */
class ImmutableIncentive implements IIncentive
{
  /**
   * Unique id 
   * @var int
   */
  private int $id;
  
  /**
   * incentive name 
   * @var string
   */
  private string $name;
  
  /**
   * Description
   * @var string
   */
  private string $description;
    
  /**
   * Active state
   * @var bool
   */
  private bool $isActive;
  
  
  /**
   * 
   * @param string $name Incentive name 
   * @param string $description Incentive description 
   * @param bool $isActive Active state
   * @param int $id Incentive id.  Set to zero for new incentive 
   * @throws \InvalidArgumentException
   */
  public function __construct( string $name, string $description, bool $isActive, int $id = 0 )
  {
    if ( !preg_match( '/^[a-zA-Z0-9\-]+$/', $name ))
      throw new \InvalidArgumentException( 'Name must be a non-empty alphanumeric string' );
    else if ( empty( trim( $description )))
      throw new \InvalidArgumentException( 'Description must not be empty' );
    else if ( $id < 0 )
      throw new \InvalidArgumentException( 'id must be greater than or equal to zero' );
    
    $this->id = $id;
    $this->name = $name;
    $this->description = $description;
    $this->isActive = $isActive;        
  }
  
  
  /**
   * Retrieve the incentive id.
   * This returns zero if the record has not yet been persisted.
   * @return int
   */
  public function getId() : int
  {
    return $this->id;
  }
  
  
  /**
   * Retrieve the incentive name 
   * @return string
   */
  public function getName() : string
  {
    return $this->name;
  }
  
  
  /**
   * Retrieve the incentive description
   * @return string
   */
  public function getDescription() : string
  {
    return $this->description;
  }
  
  
  /**
   * Test if the incentive record is active.
   * Active means available for employers to use in for engagement programs.
   * @return bool
   */
  public function isActive() : bool
  {
    return $this->isActive;
  }
  
  
  /**
   * Validate the incentive record.
   * 
   * 1) Ensure that name is a non-empty string matching ^[a-zA-Z0-9\-\-]+$
   * 2) Description must be a non-empty string
   * 
   * @return void
   * @throws ValidationException
   */
  public function validate() : void
  {
    //..do nothing.  This object is immutable and validated during construction
  }
}
