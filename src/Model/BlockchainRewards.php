<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\Json;

class BlockchainRewards extends Json
{
    function height(): int { return $this->get( 'height' )->asInt(); }
    function currentReward(): int { return $this->get( 'currentReward' )->asInt(); }
    function totalWavesAmount(): int { return $this->get( 'totalWavesAmount' )->asInt(); }
    function minIncrement(): int { return $this->get( 'minIncrement' )->asInt(); }
    function term(): int { return $this->get( 'term' )->asInt(); }
    function nextCheck(): int { return $this->get( 'nextCheck' )->asInt(); }
    function votingIntervalStart(): int { return $this->get( 'votingIntervalStart' )->asInt(); }
    function votingInterval(): int { return $this->get( 'votingInterval' )->asInt(); }
    function votingThreshold(): int { return $this->get( 'votingThreshold' )->asInt(); }
    function votes(): Votes { return $this->get( 'votes' )->asJson()->asVotes(); }
}   
