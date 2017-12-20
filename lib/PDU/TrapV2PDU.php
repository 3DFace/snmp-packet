<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

class TrapV2PDU extends AbstractBasicPDU
{

    public const TAG = 7;

    public static function getBasicTag(): int
    {
        return self::TAG;
    }

}
