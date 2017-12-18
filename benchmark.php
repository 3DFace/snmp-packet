<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

include_once __DIR__ . '/bootstrap.php';

$msg_bin = hex2bin('302c020100040770726976617465a01e02010102010002010030133011060d2b0601040194780102070302000500');

function decodePlain(){
    global $msg_bin;
    $raw = \ASN1\Type\UnspecifiedType::fromDER($msg_bin);
    $msg = $raw->asSequence();
    $msg->at(0)->asInteger()->intNumber(); // version
    $msg->at(1)->asOctetString()->string(); // community
    $pdu = $msg->at(2)->asTagged()->asImplicit(\ASN1\Element::TYPE_SEQUENCE, 0)->asSequence();
    $pdu->at(0)->asInteger()->intNumber(); // request-id
    $pdu->at(1)->asInteger()->intNumber(); // error-status
    $pdu->at(2)->asInteger()->intNumber(); // error-index
    $bind_list = $pdu->at(3)->asSequence();
    /** @var \ASN1\Type\Constructed\Sequence $bind */
    $bind = $bind_list->at(0)->asSequence();
    $bind->at(0)->asObjectIdentifier();
    $bind->at(1)->asNull();
}

function decodeMessage(){
    global $msg_bin;
    /** @noinspection PhpUnhandledExceptionInspection */
    \dface\SnmpPacket\Message\MessageV1::fromBinary($msg_bin);
}

$iterations = 2000;

echo "\nDecoding MessageV1 with GetRequest PDU $iterations times:\n\n";

echo 'underlying decoder: ';
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    decodePlain();
}
echo ($raw_time = microtime(true) - $start)."\n";

echo 'library decoder:    ';
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    decodeMessage();
}
echo ($lib_time = microtime(true) - $start)."\n";

$overhead = (int)(($lib_time/$raw_time - 1) * 100);
echo "\noverhead: $overhead%\n";
