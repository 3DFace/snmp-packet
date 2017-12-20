<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

class InformRequestPDU extends AbstractBasicPDU
{

    public const TAG = 6;

    public static function getBasicTag(): int
    {
        return self::TAG;
    }

}
