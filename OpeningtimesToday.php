<?php
header('Content-Type: text/html; charset=utf-8');

include_once __DIR__ . "/Openingtimes.php";

class OpeningtimesToday extends Openingtimes
{
    /**
     * Renders the output.
     *
     * @return string
     */
    public function render()
    {
        $html = "";

        switch (count($this->configuration["today"])) {
            case 1:
                $html .= '<div class="openingtimes openingtimes--today">';
                switch ($this->configuration["today"][0]["type"]) {
                    case "singletime":
                        $html .= sprintf($this->language["today_oneTime_singletime"], $this->configuration["today"][0]["value"]);
                        break;
                    case "doubletime":
                        $html .= sprintf($this->language["today_oneTime_doubletime"], $this->configuration["today"][0]["value1"], $this->configuration["today"][0]["value2"]);
                        break;
                    case "string":
                        $html .= sprintf($this->language["today_oneTime_string"], htmlentities($this->configuration["today"][0]["value"]));
                        break;
                }
                $html .= '</div>';
            break;
            case 2:
                $html .= '<div class="openingtimes openingtimes--today">';
                switch ($this->configuration["today"][0]["type"]) {
                    case "singletime":
                        $html .= sprintf($this->language["today_twoTimes_partOne_singletime"], $this->configuration["today"][0]["value"]);
                        break;
                    case "doubletime":
                        $html .= sprintf($this->language["today_twoTimes_partOne_doubletime"], $this->configuration["today"][0]["value1"], $this->configuration["today"][0]["value2"]);
                        break;
                    case "string":
                        $html .= sprintf($this->language["today_twoTimes_partOne_string"], htmlentities($this->configuration["today"][0]["value"]));
                        break;
                }
                switch ($this->configuration["today"][1]["type"]) {
                    case "singletime":
                        $html .= sprintf($this->language["today_twoTimes_partTwo_singletime"], $this->configuration["today"][1]["value"]);
                        break;
                    case "doubletime":
                        $html .= sprintf($this->language["today_twoTimes_partTwo_doubletime"], $this->configuration["today"][1]["value1"], $this->configuration["today"][1]["value2"]);
                        break;
                    case "string":
                        $html .= sprintf($this->language["today_twoTimes_partTwo_string"], htmlentities($this->configuration["today"][1]["value"]));
                        break;
                }
                $html .= '</div>';
            break;
        }

        return $html;
    }
}

$ot = new OpeningtimesToday(__DIR__ . "/Data/Openingtimes.txt");
echo $ot->render();
