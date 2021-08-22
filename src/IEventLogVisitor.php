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
 * A visitor that could be attached to the existing data logging endpoints.
 * Each endpoint would call processEvent() when data is logged.
 * 
 * In some future implementation, this may be replaced with specific visitors for each type of data being logged.
 * 
 */
interface IEventLogVisitor
{
  /**
   * Do something with 
   * @param string $name
   * @param int $userId
   * @param int $employerId
   * @param array $context This could be the data being logged or anything else that's relevant to the event.  
   * @return void
   */
  public function processEvent( string $name, int $userId, int $employerId, array $context = [] ) : void;
}

