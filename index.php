<?php
class ActivitySuggester
{
    private $participants;
    private $type;
    private $sender;

    public function __construct($participants, $type, $sender)
    {
        $participants = intval($participants);
        if (!is_int($participants) || $participants <= 0 || $participants >= 8) {
            throw new InvalidArgumentException('Participants повинні бути integer та в проміжку від 0 до 8.');
        }

        $validTypes = ["education", "recreational", "social", "diy", "charity", "cooking", "relaxation", "music", "busywork"];
        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException('Неправильний тип відпочинку. Правильними типами являються: ' . implode(', ', $validTypes));
        }

        $validSenders = ["file", "console"];
        if (!in_array($sender, $validSenders)) {
            throw new InvalidArgumentException('Неправильний спосіб відправки повідомлення. Правильними спосібами являються: ' . implode(', ', $validSenders));
        }

        $this->participants = $participants;
        $this->type = $type;
        $this->sender = $sender;
    }

    public function suggestActivity()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://www.boredapi.com/api/activity?participants=".$this->participants."&type=".$this->type,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $activity = json_decode($response, true);

        if ($this->sender == "file") {
            file_put_contents('activity.txt', $activity['activity']);
        } elseif ($this->sender == "console") {
            echo $activity['activity'];
        }
    }
}

$participants = $argv[1];
$type = $argv[2];
$sender = $argv[3];

$activitySuggester = new ActivitySuggester($participants, $type, $sender);
$activitySuggester->suggestActivity();
?>
