<?php
namespace App\EventListener;

use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;


class CalendarEventListener
{
    private $entityManager;
    private $container;

    public function __construct(EntityManager $entityManager,ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container=$container;
    }

    public function loadEvents(CalendarEvent $calendarEvent)
    {


        $startDate = $calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();

        

       $user= $this->container->get('security.context')->getToken()->getUser();
        $userRoles=$user->getRoles();


        if($userRoles[0]=="ROLE_POWER_USER"){
            $userAbsents=$this->entityManager->getRepository("App:StudentAbsent")->findBy(array("student"=>$user));

            $i=0;
            foreach($userAbsents as $userAbsent) {
                if(($userAbsent->getstatus()==false)&&($i % 2==0)){
                    $beginDate=new \DateTime($userAbsent->getDate()->format('Y-m-d').$userAbsent->getBeginTime()->format('H:m:s'));
                }
                $eventEntity = new EventEntity($user->getUsername(), $companyEvent->getStartDatetime(), $companyEvent->getEndDatetime());
            }
        }elseif ($userRoles[0]=="ROLE_TEACHER"){
            $userAbsents=$this->entityManager->getRepository("AppBundle:TeachersAbsent")->findBy(array("teachers"=>$user,"status"=>false ));
            //var_dump($userAbsents);
            foreach($userAbsents as $userAbsent) {
                $eventEntity = new EventEntity($user->getUsername(), new \DateTime($userAbsent->getDate()), null, true);
                //optional calendar event settings
                $eventEntity->setAllDay(true); // default is false, set to true if this is an all day event
                $eventEntity->setBgColor('#FF0000'); //set the background color of the event's label
                $eventEntity->setFgColor('#FFFFFF'); //set the foreground color of the event's label
                $eventEntity->setUrl('http://www.google.com'); // url to send user to when event label is clicked
                $eventEntity->setCssClass('my-custom-class'); // a custom class you may want to apply to event labels

                //finally, add the event to the CalendarEvent for displaying on the calendar
                $calendarEvent->addEvent($eventEntity);
            }
        }

    }

}