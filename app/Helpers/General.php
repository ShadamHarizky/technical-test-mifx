<?php

function usd_to_rupiah_format(mixed $usd): string
{
    $rupiah = $usd * 14000;
    return 'Rp ' . number_format($rupiah, 2, ',', '.');
}