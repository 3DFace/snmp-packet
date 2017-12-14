<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

use ASN1\Type\Tagged\ImplicitlyTaggedType;

interface PDU
{

    public function equals(PDU $pdu): bool;

    public function toASN1(): ImplicitlyTaggedType;

}
