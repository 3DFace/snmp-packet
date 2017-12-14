<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

include_once __DIR__ . '/bootstrap.php';

$seq_bin = hex2bin('3011060d2b0601040194780102070302000500');
$msg_bin = hex2bin('302c020100040770726976617465a01e02010102010002010030133011060d2b0601040194780102070302000500');

$start = microtime(true);

for ($i = 0; $i < 1000; $i++) {
    /** @noinspection PhpUnhandledExceptionInspection */
    \ASN1\Type\Constructed\Sequence::fromDER($seq_bin);
}

echo microtime(true) - $start;
