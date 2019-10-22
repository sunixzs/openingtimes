<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=utf-8');

// set local for strftime output in table head.
setlocale(LC_TIME, "de_DE");

include_once __DIR__ . "/Openingtimes.php";

class OpeningtimesFuture extends Openingtimes
{
    /**
     * Renders the output.
     *
     * @return string
     */
    public function render()
    {
        if (!count($this->configuration["future"])) {
            return "";
        }
        
        $entries = [];

        foreach ($this->configuration["future"] as $entry) {
            $entries[] = $this->renderEntry($entry);
        }

        $html = "";
        if (count($entries)) {
            $html .= '<table class="openingtimes openingtimes--future">';
            $html .= implode("", $entries);
            $html .= '</table>';
        }

        return $html;
    }

    /**
     * @param array $entry
     * @return string HTML
     */
    protected function renderEntry($entry) {
        $html = '';
        $value = $entry["values"];
        switch (count($value)) {
            case 1:
                $html .= '<tr>';
                $html .= $this->renderDayLabel($entry["DateTime"]);
                $html .= '<td>';
                switch ($value[0]["type"]) {
                    case "singletime":
                        $html .= sprintf($this->language["future_oneTime_singletime"], $value[0]["value"]);
                        break;
                    case "doubletime":
                        $html .= sprintf($this->language["future_oneTime_doubletime"], $value[0]["value1"], $value[0]["value2"]);
                        break;
                    case "string":
                        $html .= sprintf($this->language["future_oneTime_string"], htmlentities($value[0]["value"]));
                        break;
                }
                $html .= '</td>';
                $html .= '</tr>';
            break;
            case 2:
                $html .= '<tr>';
                $html .= $this->renderDayLabel($entry["DateTime"]);
                $html .= '<td>';
                switch ($value[0]["type"]) {
                    case "singletime":
                        $html .= sprintf($this->language["future_twoTimes_partOne_singletime"], $value[0]["value"]);
                        break;
                    case "doubletime":
                        $html .= sprintf($this->language["future_twoTimes_partOne_doubletime"], $value[0]["value1"], $value[0]["value2"]);
                        break;
                    case "string":
                        $html .= sprintf($this->language["future_twoTimes_partOne_string"], htmlentities($value[0]["value"]));
                        break;
                }
                switch ($value[1]["type"]) {
                    case "singletime":
                        $html .= sprintf($this->language["future_twoTimes_partTwo_singletime"], $value[1]["value"]);
                        break;
                    case "doubletime":
                        $html .= sprintf($this->language["future_twoTimes_partTwo_doubletime"], $value[1]["value1"], $value[1]["value2"]);
                        break;
                    case "string":
                        $html .= sprintf($this->language["future_twoTimes_partTwo_string"], htmlentities($value[1]["value"]));
                        break;
                }
                $html .= '</td>';
                $html .= '</tr>';
            break;
        }

        return $html;
    }

    /**
     * @param \DateTime $dt
     * @return string HTML
     */
    protected function renderDayLabel($dt) {
        if (!$dt) {
            return "<th></th>";
        }

        return "<th>" . strftime("%A, %e. %B %Y", $dt->getTimestamp()) . "</th>";
    }
}

$ot = new OpeningtimesFuture(__DIR__ . "/Data/Openingtimes.txt");
echo $ot->render();
