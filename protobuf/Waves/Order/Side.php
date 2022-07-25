<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: waves/order.proto

namespace Waves\Order;

use UnexpectedValueException;

/**
 * Protobuf type <code>waves.Order.Side</code>
 */
class Side
{
    /**
     * Generated from protobuf enum <code>BUY = 0;</code>
     */
    const BUY = 0;
    /**
     * Generated from protobuf enum <code>SELL = 1;</code>
     */
    const SELL = 1;

    private static $valueToName = [
        self::BUY => 'BUY',
        self::SELL => 'SELL',
    ];

    public static function name($value)
    {
        if (!isset(self::$valueToName[$value])) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no name defined for value %s', __CLASS__, $value));
        }
        return self::$valueToName[$value];
    }


    public static function value($name)
    {
        $const = __CLASS__ . '::' . strtoupper($name);
        if (!defined($const)) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no value defined for name %s', __CLASS__, $name));
        }
        return constant($const);
    }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Side::class, \Waves\Order_Side::class);

