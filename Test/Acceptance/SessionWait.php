<?php 

trait SessionWait {

    public function spin ($closure, $wait = 60)
    {
        for ($i = 0; $i < $wait; $i++)
        {
            try {
                if ($closure($this)) {
                    return true;
                }
            } catch (Exception $e) {
                // do nothing
            }

            sleep(1);
        }

        $backtrace = debug_backtrace();

        throw new Exception(
            "Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n" .
            $backtrace[1]['file'] . ", line " . $backtrace[1]['line']
        );
    } 

}
