<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

class ResponsePDU extends AbstractBasicPDU
{

    public const TAG = 2;

    public static function getBasicTag(): int
    {
        return self::TAG;
    }

}
