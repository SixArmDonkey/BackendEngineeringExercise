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

namespace buffalokiwi\incentivedemo\demo;

require_once( __DIR__ . '../../../vendor/autoload.php' );

use buffalokiwi\incentivedemo\IEmployerIncentive;
use buffalokiwi\incentivedemo\IEmployerIncentiveAction;
use buffalokiwi\incentivedemo\IIncentive;
use buffalokiwi\incentivedemo\IIncentiveEvent;
use buffalokiwi\incentivedemo\ImmutableEmployerIncentive;
use buffalokiwi\incentivedemo\ImmutableIncentive;
use buffalokiwi\incentivedemo\ImmutableIncentiveEvent;
use buffalokiwi\incentivedemo\MockIncentiveCounter;
use buffalokiwi\incentivedemo\MockIncentiveEmployerRepository;
use buffalokiwi\incentivedemo\MockIncentiveProcessor;
use buffalokiwi\incentivedemo\MockIncentiveQueue;
use buffalokiwi\incentivedemo\MockIncentiveRepository;
use buffalokiwi\incentivedemo\SequentialDataAction;
use buffalokiwi\incentivedemo\UserBirthAction;
use buffalokiwi\incentivedemo\UserBirthSuccessException;


/**
 * This is a 'lil script to illustrate how to 'wire' this up.
 */

//..Set up the two mock repositores

//..Incentive repository 
//..This contains global incentives that are available for employers to use within their programs
$incentiveRepo = new MockIncentiveRepository( 
  //..This is the object factory for incentives 
  fn( int $id, string $name, string $description, bool $isActive ) : IIncentive 
    => new ImmutableIncentive( $name, $description, $isActive, $id )
);


//..Mock employer incentive repository
//..This contains links between incentives and employers and provides "active" incentive events for processing 
$empRepo = new MockIncentiveEmployerRepository( 
  //..Need this to attach incentives to employer incentives 
  $incentiveRepo, 
        
  //..Object factory for creating instances of IEmployerIncentive 
  fn( int $id, int $employerId, IIncentive $incentive, IEmployerIncentiveAction ...$actions ) : IEmployerIncentive 
    => new ImmutableEmployerIncentive( $id, $employerId, $incentive, ...$actions ),
        
  //..A map of incentive names to actions/strategies
  //..This is what the event processor uses to determine what to do
  //..This also gives us the ability to easily swap out action implementations   
  [
    'data-logged-5-sequential-days' => [new SequentialDataAction( 5, new MockIncentiveCounter( 60 * 60 * 24 ))],
    'user-birth' => [new UserBirthAction()]
  ]
);

 
//..Create the queue
//..The existing log endpoints would add an incentive event to this queue, and the incentive processor 
//  reads from this queue to process event actions and award users.
//..The IIncentiveQueue object could be a wrapper for a real message system.
$queue = new MockIncentiveQueue( function( int $userId, int $employerId, string $eventName ) use($empRepo) : IIncentiveEvent {
  return new ImmutableIncentiveEvent( 
    $userId, 
    $employerId, 
    $empRepo->getForEmployerByEvent( $employerId, $eventName )->getId(), 
    $eventName 
  );  
});


//..Create the incentive processor
//..This is a separate system that is run by some task scheduler
//..Event processing is deferred until the processor is executed
$processor = new MockIncentiveProcessor( $queue, $empRepo );



//..Now we simulate some of the log endpoints adding events to the queue

//..These 5 calls will log a single event since they are not separated by 24 hours.
//..See SequentialDataActionTest for proof that the success exception is thrown 
$queue->enqueue( $queue->createEvent( 1, 1, 'data-logged-5-sequential-days' ));
$queue->enqueue( $queue->createEvent( 1, 1, 'data-logged-5-sequential-days' ));
$queue->enqueue( $queue->createEvent( 1, 1, 'data-logged-5-sequential-days' ));
$queue->enqueue( $queue->createEvent( 1, 1, 'data-logged-5-sequential-days' ));
$queue->enqueue( $queue->createEvent( 1, 1, 'data-logged-5-sequential-days' ));


//..This single event will throw a UserBirthSuccessException in this demo script.
$queue->enqueue( $queue->createEvent( 1, 1, 'user-birth' ));


//..And finally, process the queue 

try {
  $processor->run();
} catch( UserBirthSuccessException $e ) {
  echo "\n User birth recorded \n";
}



/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////

//..This is an example of how the sequantial data counter will throw a DataLoggedFor5DaysException after
//  5 log entries.  This removes the time delay for demo purposes.  


//..Recreating some of the above objects 

//..Mock employer incentive repository
//..This contains links between incentives and employers and provides "active" incentive events for processing 
$empRepo = new MockIncentiveEmployerRepository( 
  //..Need this to attach incentives to employer incentives 
  $incentiveRepo, 
        
  //..Object factory for creating instances of IEmployerIncentive 
  fn( int $id, int $employerId, IIncentive $incentive, IEmployerIncentiveAction ...$actions ) : IEmployerIncentive 
    => new ImmutableEmployerIncentive( $id, $employerId, $incentive, ...$actions ),
        
  //..A map of incentive names to actions/strategies
  //..This is what the event processor uses to determine what to do
  //..This also gives us the ability to easily swap out action implementations   
  [
    'data-logged-5-sequential-days' => [new SequentialDataAction( 5, new MockIncentiveCounter( 0 ))], //..Delay is set to zero seconds 
    'user-birth' => [new UserBirthAction()]
  ]
);


//..Create the incentive processor
//..This is a separate system that is run by some task scheduler
//..Event processing is deferred until the processor is executed
$processor = new MockIncentiveProcessor( $queue, $empRepo );



//..Now we simulate some of the log endpoints adding events to the queue

//..These 5 calls will log a single event since they are not separated by 24 hours.
//..See SequentialDataActionTest for proof that the success exception is thrown 
$queue->enqueue( $queue->createEvent( 1, 1, 'data-logged-5-sequential-days' ));
$queue->enqueue( $queue->createEvent( 1, 1, 'data-logged-5-sequential-days' ));
$queue->enqueue( $queue->createEvent( 1, 1, 'data-logged-5-sequential-days' ));
$queue->enqueue( $queue->createEvent( 1, 1, 'data-logged-5-sequential-days' ));
$queue->enqueue( $queue->createEvent( 1, 1, 'data-logged-5-sequential-days' ));

//..Adding a 6th event to show how the exception is only thrown after a 5 events.
$queue->enqueue( $queue->createEvent( 1, 1, 'data-logged-5-sequential-days' ));

//..And finally, process the queue 

try {
  $processor->run();
} catch( \buffalokiwi\incentivedemo\DataLoggedFor5DaysException $e ) {
  echo "\n Data logged for 5 days \n";
}
