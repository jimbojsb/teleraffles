<?php
namespace Teleraffle\Controller;

use Piano\Response;
use Piano\View;

class Raffle
{
    use \Piano\ControllerTrait;

    public function create()
    {

        // state variable, store validation errors here
        $errors = [];

        if ($_POST) {

            $name = $_POST["name"];
            $numWinners = $_POST["winners"];

            // do some basic validation of the data being submitted
            if(empty($name)) {
                $errors[] = 'The name of the raffle must not be empty.';
            }

            if (filter_var($numWinners, FILTER_VALIDATE_INT) === false || $numWinners < 1) {
                $errors[] = 'The number of winners must be an integer.';
            }

            $p = $this->application->redis;
            $randKey = "";
            for ($c = 0; $c < 4; $c++) {
                $randKey .= mt_rand(0, 9);
            }
            $newRaffleKey = "raffle:$randKey";
            $p->hmset(
                $newRaffleKey,
                [
                    'name' => $name,
                    'winners' => $numWinners,
                    'key' => $randKey,
                    'drawn' => 0
                ]
            );

        }

        // if there is a random key, load that, otherwise display the
        // create page with any errors (if any)
        if(count($_POST) >= 1 && count($errors) === 0) {
            return (new Response)->redirect("/view/$randKey");
        } else {
            return (new View)->render('create.phtml', ['error' => $errors]);
        }

    }

    public function view($id)
    {
        $p = $this->application->redis;
        $raffleKey = "raffle:$id";
        $raffle = $p->hgetall($raffleKey);
        $entrantsKey = "entrants:$id";
        $entrants = count($p->smembers($entrantsKey)) ?: 0;
        $v = new View;
        $v->key = $raffle["key"];
        $v->name = $raffle["name"];
        $v->entrants = $entrants;
        $v->winners = $raffle["winners"];
        return $v->render('view.phtml');
    }

    public function winners($id)
    {
        $p = $this->application->redis;
        $raffleKey = "raffle:$id";
        if ($p->hget($raffleKey, 'drawn') == 1) {
            return;
        }
        $entrantsKey = "entrants:$id";
        $winners = $p->hget($raffleKey, 'winners');
        $name = $p->hget($raffleKey, 'name');
        $entrants = $p->smembers($entrantsKey);
        shuffle($entrants);
        if ($winners > count($entrants)) {
            $winners = count($entrants);
        }
        for ($c = 0; $c < $winners; $c++) {
            $winner = $entrants[$c];
            $t = new \Services_Twilio("ACdbeb6551af084319a0ff37778134e2db", "63ebe1e4e9166f0ed4019d1bf0cbc902");
            try {
                $num = $c + 1;
                $sms = $t->account->sms_messages->create("512-524-6954", $winner, "You're winner #$num for $name!");
            } catch (\Exception $e) {
                var_dump($e);
            }
        }

        $entrants = array_slice($entrants, $winners);
        foreach ($entrants as $loser) {
            $t = new \Services_Twilio("ACdbeb6551af084319a0ff37778134e2db", "63ebe1e4e9166f0ed4019d1bf0cbc902");
            try {
                $sms = $t->account->sms_messages->create("512-524-6954", $loser, "Sorry, you didn't win one of the $winners $name");
            } catch (\Exception $e) {
                var_dump($e);
            }
        }

        $p->hset($raffleKey, 'drawn', 1);
        return (new View)->render('success.phtml');
    }
}