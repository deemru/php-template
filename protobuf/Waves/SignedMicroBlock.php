<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: waves/block.proto

namespace Waves;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>waves.SignedMicroBlock</code>
 */
class SignedMicroBlock extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.waves.MicroBlock micro_block = 1;</code>
     */
    protected $micro_block = null;
    /**
     * Generated from protobuf field <code>bytes signature = 2;</code>
     */
    protected $signature = '';
    /**
     * Generated from protobuf field <code>bytes total_block_id = 3;</code>
     */
    protected $total_block_id = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Waves\MicroBlock $micro_block
     *     @type string $signature
     *     @type string $total_block_id
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Waves\Block::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.waves.MicroBlock micro_block = 1;</code>
     * @return \Waves\MicroBlock|null
     */
    public function getMicroBlock()
    {
        return $this->micro_block;
    }

    public function hasMicroBlock()
    {
        return isset($this->micro_block);
    }

    public function clearMicroBlock()
    {
        unset($this->micro_block);
    }

    /**
     * Generated from protobuf field <code>.waves.MicroBlock micro_block = 1;</code>
     * @param \Waves\MicroBlock $var
     * @return $this
     */
    public function setMicroBlock($var)
    {
        GPBUtil::checkMessage($var, \Waves\MicroBlock::class);
        $this->micro_block = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes signature = 2;</code>
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Generated from protobuf field <code>bytes signature = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setSignature($var)
    {
        GPBUtil::checkString($var, False);
        $this->signature = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes total_block_id = 3;</code>
     * @return string
     */
    public function getTotalBlockId()
    {
        return $this->total_block_id;
    }

    /**
     * Generated from protobuf field <code>bytes total_block_id = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setTotalBlockId($var)
    {
        GPBUtil::checkString($var, False);
        $this->total_block_id = $var;

        return $this;
    }

}

