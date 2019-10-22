<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=utf-8');

include_once __DIR__ . "/Openingtimes.php";

class OpeningtimesTooltip extends Openingtimes
{
    /**
     * Renders the output.
     *
     * @return string
     */
    public function render()
    {
        return $this->configuration["tooltip"] ? '<div class="openingtimes openingtimes--tooltip">' . $this->configuration["tooltip"] . '</div>' : "";
    }
}

$ot = new OpeningtimesTooltip(__DIR__ . "/Data/Openingtimes.txt");
echo $ot->render();
