<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

class ReportPDU extends AbstractBasicPDU
{

    public const TAG = 8;

    public static function getTag(): int
    {
        return self::TAG;
    }

}
