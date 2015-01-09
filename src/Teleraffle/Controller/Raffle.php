<?php
namespace Teleraffle\Controller;

use Piano\Response;
use Piano\View;
use Teleraffle\Db;

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

            $randKey = "";
            for ($c = 0; $c < 4; $c++) {
                $randKey .= mt_rand(0, 9);
            }

            try {
                $this->application->db->perform("INSERT INTO raffles (`name`, `winner_count`, `key`, `drawn`) VALUES (?, ?, ?, ?)", [$name, $numWinners, $randKey, 0]);
            } catch (\Exception $e) {
                var_dump($e);
            }
        }

        // if there is a random key, load that, otherwise display the
        // create page with any errors (if any)
        if(count($_POST) >= 1 && count($errors) === 0) {
            return (new Response)->redirect("/view/$randKey");
        } else {
            return (new View)->render('create.phtml', ['error' => $errors ?: null]);
        }

    }

    public function view($id)
    {

        $raffle = $this->application->db->fetchOne("SELECT * FROM raffles WHERE `key`=?", [$id]);
        $entrants = $this->application->db->fetchAll("SELECT * FROM entrants WHERE raffle_id=?", [$raffle["id"]]);
        $numEntrants = count($entrants);


        $v = new View;
        $v->key = $raffle["key"];
        $v->name = $raffle["name"];
        $v->numEntrants = $numEntrants;
        $v->entrants = $entrants;
        $v->winners = $raffle["winners"];
        return $v->render('view.phtml');
    }

    public function winners($id)
    {
        $v = new \Piano\View;
        $raffle = $this->application->db->fetchOne("SELECT * FROM raffles WHERE `key`=?", [$id]);

        if ($raffle["drawn"]) {
            $v->already_drawn = 1;
            $winnerNums = $this->application->db->fetchAll("SELECT * FROM entrants WHERE raffle_id=? AND winner=1", [$raffle["id"]]);
        } else {
            $entrants = $this->application->db->fetchAll("SELECT * FROM entrants WHERE raffle_id=?", [$raffle["id"]]);
            $name = $raffle["name"];
            shuffle($entrants);
            $winners = $raffle["winner_count"];
            if ($winners > count($entrants)) {
                $winners = count($entrants);
            }
            for ($c = 0; $c < $winners; $c++) {
                $winner = $entrants[$c];
                $t = new \Services_Twilio("ACdbeb6551af084319a0ff37778134e2db", "63ebe1e4e9166f0ed4019d1bf0cbc902");
                try {
                    $num = $c + 1;
                    $winner["winner"] = $num;
                    $this->application->db->perform("UPDATE entrants SET winner=? WHERE raffle_id=? AND phone=?", [$num, $raffle["id"], $winner["phone"]]);
                    $sms = $t->account->sms_messages->create("512-524-6954", $winner["phone"], "You're winner #$num for $name!");
                } catch (\Exception $e) {
                    var_dump($e);
                }
                $winnerNums[] = $winner;
            }

            $entrants = array_slice($entrants, $winners);
            foreach ($entrants as $loser) {
                $t = new \Services_Twilio("ACdbeb6551af084319a0ff37778134e2db", "63ebe1e4e9166f0ed4019d1bf0cbc902");
                try {
                    $sms = $t->account->sms_messages->create("512-524-6954", $loser["phone"], "Sorry, you didn't win one of the $winners $name");
                } catch (\Exception $e) {
                    var_dump($e);
                }
            }

            $this->application->db->perform("UPDATE raffles SET drawn=1 WHERE id=?", [$raffle["id"]]);
        }

        $v->winners = $winnerNums;
        return $v->render('success.phtml');
    }
}