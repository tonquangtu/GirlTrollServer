<?php

namespace Psr\Log;

/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
<<<<<<< HEAD
     * @return null
=======
     * @return void
>>>>>>> 67b05d5e7720b465ed0046694efd7fb52fabfae7
     */
    public function setLogger(LoggerInterface $logger);
}
