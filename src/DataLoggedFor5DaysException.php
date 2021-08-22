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
 * This is thrown by SequentialDataAction when some event has been logged 5 days in a row
 */
class DataLoggedFor5DaysException extends \Exception {} 

