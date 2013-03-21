<?php
namespace Teleraffle\Controller;

class Sms
{
    use \Piano\ControllerTrait;

    public function receive()
    {
        $p = $this->application->redis;
        $msgBody = $_POST['Body'];
        $msgFrom = $_POST["From"];
        $raffleKey = "raffle:$msgBody";
        if ($p->exists($raffleKey)) {
            $entrantsKey = "entrants:$msgBody";
            $name = $p->hget($raffleKey, 'name');
            if (!$p->sismember($entrantsKey, $msgFrom)) {
                $p->sadd($entrantsKey, $msgFrom);
                echo "<Response><Sms>Your entry for $name is accepted!</Sms></Response>";
            } else {
                echo "<Response><Sms>You're already entered for $name!</Sms></Response>";
            }
        }
    }
}