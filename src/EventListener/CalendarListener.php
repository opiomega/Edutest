<?php


namespace App\EventListener;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\SeanceRepository;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use DateTime;

class CalendarListener
{
    private $bookingRepository;

    public function __construct(
        SeanceRepository $bookingRepository
    ) {
        $this->bookingRepository = $bookingRepository;
    }

    public function load(CalendarEvent $calendar): void
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        // Modify the query to fit to your entity and needs
        // Change booking.beginAt by your start date property
       /* $bookings = $this->bookingRepository
            ->createQueryBuilder('booking')
            ->where('booking.beginAt BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;*/
$bookings=$this->bookingRepository->findAll();
        foreach ($bookings as $booking) {
            $date=$booking->getDate();
          
            $begint=$booking->getBeginTime();
            $begint=date('H:i',strtotime($begint));
            $endt=$booking->getEndTime();
            $endt=date('H:i',strtotime($endt));
            $begint=date('Y-m-d H:i',strtotime("$date $begint"));
            $begint=new DateTime($begint);
            
            dump($begint);
            $endt=date('Y-m-d H:i',strtotime("$date $endt "));
            $endt=new DateTime($endt);
            
            $cat=$booking->getCategory();
            $group=$booking->getEducationgroup()->getName();
            $name=$group;
            // this create the events with your data (here booking data) to fill calendar
            $bookingEvent = new Event(
                $name,
                $begint,
                $endt  // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $bookingEvent->setOptions([
                'backgroundColor' => 'red',
                'borderColor' => 'red',
            ]);
            $bookingEvent->addOption('url', 'https://github.com');

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($bookingEvent);
        }
    }
}
