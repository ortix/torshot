<?php

namespace App\Console\Commands;


use AdamBrett\ShellWrapper\Command\Builder;

class CommandBuilder extends Builder
{

    /**
     * Converts command to string and appends output redirects. ALWAYS USE LAST
     */
    public function toBackground()
    {
        $this->command = $this->command . ' > /dev/null 2>/dev/null & echo $!';
        return $this;
    }

}