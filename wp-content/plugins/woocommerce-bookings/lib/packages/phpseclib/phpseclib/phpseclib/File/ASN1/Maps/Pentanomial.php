<?php

/**
 * Pentanomial
 *
 * PHP version 5
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 *
 * Modified by woocommerce on 14-October-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Automattic\WooCommerce\Bookings\Vendor\phpseclib3\File\ASN1\Maps;

use Automattic\WooCommerce\Bookings\Vendor\phpseclib3\File\ASN1;

/**
 * Pentanomial
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class Pentanomial
{
    const MAP = [
        'type' => ASN1::TYPE_SEQUENCE,
        'children' => [
            'k1' => ['type' => ASN1::TYPE_INTEGER], // k1 > 0
            'k2' => ['type' => ASN1::TYPE_INTEGER], // k2 > k1
            'k3' => ['type' => ASN1::TYPE_INTEGER], // k3 > h2
        ]
    ];
}