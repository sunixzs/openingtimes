<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=utf-8');

include_once __DIR__ . "/Openingtimes.php";

class OpeningtimesWeek extends Openingtimes
{
    /**
     * @var array
     */
    protected $weekdays = [
        "monday",
        "tuesday",
        "wednesday",
        "thursday",
        "friday",
        "saturday",
        "sunday"
    ];

    /**
     * Renders the output.
     *
     * @return string
     */
    public function render()
    {
        $html = "";

        $days = [];

        foreach ($this->weekdays as $weekday) {
            if ($this->configuration["weekdays"][$weekday]) {
                $days[] = $this->renderWeekday($weekday);
            }
        }

        if (count($days)) {
            $html .= '<table class="openingtimes openingtimes--week">';
            $html .= implode("", $days);
            $html .= '</table>';
        }

        return $html;
    }

    /**
     * @param string $weekday
     * @return string HTML
     */
    protected function renderWeekday($weekday) {
        $html = '';
        $value = $this->configuration["weekdays"][$weekday];

        switch (count($value)) {
            case 1:
                $html .= '<tr>';
                $html .= $this->renderWeekdayLabel($weekday);
                $html .= '<td>';
                switch ($value[0]["type"]) {
                    case "singletime":
                        $html .= sprintf($this->language["weekday_oneTime_singletime"], $value[0]["value"]);
                        break;
                    case "doubletime":
                        $html .= sprintf($this->language["weekday_oneTime_doubletime"], $value[0]["value1"], $value[0]["value2"]);
                        break;
                    case "string":
                        $html .= sprintf($this->language["weekday_oneTime_string"], htmlentities($value[0]["value"]));
                        break;
                }
                $html .= '</td>';
                $html .= '</tr>';
            break;
            case 2:
                $html .= '<tr>';
                $html .= $this->renderWeekdayLabel($weekday);
                $html .= '<td>';
                switch ($value[0]["type"]) {
                    case "singletime":
                        $html .= sprintf($this->language["weekday_twoTimes_partOne_singletime"], $value[0]["value"]);
                        break;
                    case "doubletime":
                        $html .= sprintf($this->language["weekday_twoTimes_partOne_doubletime"], $value[0]["value1"], $value[0]["value2"]);
                        break;
                    case "string":
                        $html .= sprintf($this->language["weekday_twoTimes_partOne_string"], htmlentities($value[0]["value"]));
                        break;
                }
                switch ($value[1]["type"]) {
                    case "singletime":
                        $html .= sprintf($this->language["weekday_twoTimes_partTwo_singletime"], $value[1]["value"]);
                        break;
                    case "doubletime":
                        $html .= sprintf($this->language["weekday_twoTimes_partTwo_doubletime"], $value[1]["value1"], $value[1]["value2"]);
                        break;
                    case "string":
                        $html .= sprintf($this->language["weekday_twoTimes_partTwo_string"], htmlentities($value[1]["value"]));
                        break;
                }
                $html .= '</td>';
                $html .= '</tr>';
            break;
        }

        return $html;
    }

    /**
     * @param string $weekday
     * @return string HTML
     */
    protected function renderWeekdayLabel($weekday) {
        return '<th>' . $this->language[$weekday] . '</th>';
    }
}

$ot = new OpeningtimesWeek(__DIR__ . "/Data/Openingtimes.txt");
echo $ot->render();
