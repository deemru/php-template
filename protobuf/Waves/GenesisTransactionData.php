<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: waves/transaction.proto

namespace Waves;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>waves.GenesisTransactionData</code>
 */
class GenesisTransactionData extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bytes recipient_address = 1;</code>
     */
    protected $recipient_address = '';
    /**
     * Generated from protobuf field <code>int64 amount = 2;</code>
     */
    protected $amount = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $recipient_address
     *     @type int|string $amount
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Waves\Transaction::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bytes recipient_address = 1;</code>
     * @return string
     */
    public function getRecipientAddress()
    {
        return $this->recipient_address;
    }

    /**
     * Generated from protobuf field <code>bytes recipient_address = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setRecipientAddress($var)
    {
        GPBUtil::checkString($var, False);
        $this->recipient_address = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 amount = 2;</code>
     * @return int|string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Generated from protobuf field <code>int64 amount = 2;</code>
     * @param int|string $var
     * @return $this
     */
    public function setAmount($var)
    {
        GPBUtil::checkInt64($var);
        $this->amount = $var;

        return $this;
    }

}

