<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

use ASN1\Element;
use ASN1\Type\Constructed\Sequence;
use dface\SnmpPacket\Message\MessageV1;

include_once __DIR__ . '/bootstrap.php';

$msg_bin = hex2bin('302c020100040770726976617465a01e02010102010002010030133011060d2b0601040194780102070302000500');

function decodePlain()
{
    global $msg_bin;
    /** @var Sequence $msg */
    $msg = Sequence::fromDER($msg_bin);
    $msg->at(0)->asInteger()->intNumber(); // version
    $msg->at(1)->asOctetString()->string(); // community
    $pdu = $msg->at(2)->asTagged()->asImplicit(Element::TYPE_SEQUENCE, 0)->asSequence(); // PDU
    $pdu->at(0)->asInteger()->intNumber(); // request-id
    $pdu->at(1)->asInteger()->intNumber(); // error-status
    $pdu->at(2)->asInteger()->intNumber(); // error-index
    $bind_list = $pdu->at(3)->asSequence(); // bindings
    /** @var Sequence $bind */
    $bind = $bind_list->at(0)->asSequence(); // first binding
    $bind->at(0)->asObjectIdentifier()->oid(); // key
    $bind->at(1)->asNull(); // value
}

function decodeMessage()
{
    global $msg_bin;
    /** @noinspection PhpUnhandledExceptionInspection */
    MessageV1::fromBinary($msg_bin);
}

$iterations = 3000;

echo "\nDecoding MessageV1 with GetRequest PDU $iterations times:\n\n";

echo 'underlying decoder: ';
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    decodePlain();
}
echo ($raw_time = microtime(true) - $start) . "\n";

echo 'library decoder:    ';
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    decodeMessage();
}
echo ($lib_time = microtime(true) - $start) . "\n";

$overhead = (int)(($lib_time / $raw_time - 1) * 100);
echo "\noverhead: $overhead%\n";
