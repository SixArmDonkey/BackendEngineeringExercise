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
 * The incentive processor handles removing items from some processing queue, and will execute 
 * actions for each queue entry.
 */
interface IIncentiveProcessor
{
  /**
   * Process the queue until completion 
   * @return void
   */
  public function run() : void;
}
