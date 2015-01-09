<?php
namespace Teleraffle\Controller;

class Sms
{
    use \Piano\ControllerTrait;

    public function receive()
    {
        $msgBody = $_POST['Body'];
        $msgFrom = $_POST["From"];

        $raffle = $this->application->db->fetchOne("SELECT * FROM raffles WHERE `key`=?", [$msgBody]);
        if ($raffle) {
            $name = $raffle["name"];
            $alreadyEntered = $this->application->db->fetchOne("SELECT * FROM entrants WHERE raffle_id=? AND phone=?", [$raffle["id"], $msgFrom]);
            if (!$alreadyEntered) {
                $this->application->db->perform("INSERT INTO entrants (`raffle_id`, `phone`, `winner`) VALUES (?,?,?)", [$raffle["id"], $msgFrom, 0]);
                echo "<Response><Sms>Your entry for $name is accepted!</Sms></Response>";
            } else {
                echo "<Response><Sms>You're already entered for $name!</Sms></Response>";
            }
        }
    }
}