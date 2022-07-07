<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class TransactionWithStatus extends Transaction
{
    function applicationStatus(): int
    {
        $status = $this->getOr( 'applicationStatus', ApplicationStatus::SUCCEEDED_S )->asString();
        switch( $status )
        {
            case ApplicationStatus::SUCCEEDED_S: return ApplicationStatus::SUCCEEDED;
            case ApplicationStatus::SCRIPT_EXECUTION_FAILED_S: return ApplicationStatus::SCRIPT_EXECUTION_FAILED;
            default: return ApplicationStatus::UNKNOWN;
        }
    }
}
