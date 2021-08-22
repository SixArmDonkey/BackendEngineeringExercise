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
 * User birth action.
 * 
 * When a user reports a birth, this action would award that user.
 */
class UserBirthAction implements IEmployerIncentiveAction
{
  /**
   * Execute the action 
   * @param IIncentiveEvent $event Related event 
   * @return void
   */
  public function execute( IIncentiveEvent $event ) : void
  {
    $event->validate();
    
    //..Success
    throw new UserBirthSuccessException();
  }
}

