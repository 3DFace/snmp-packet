<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;


use ASN1\Element;

interface ScopedPDUData
{

    public function toASN1(): Element;

    public function equals($x): bool;

}
