<?php

function heapSort(&$arr)
{
    $length = count($arr);
    buildHeap($arr);
    $heapSize = $length - 1;
    for ($i = $heapSize; $i >= 0; $i--) {
        list($arr[0], $arr[$heapSize]) = [$arr[$heapSize], $arr[0]];
        $heapSize--;
        heapify(0, $heapSize, $arr);
    }
}

//创建初始堆
function buildHeap(&$arr)
{
    $length = count($arr);
    $heapSize = $length - 1;
    for ($i = ($length / 2); $i >= 0; $i--) {
        heapify($i, $heapSize, $arr);
    }
}

//使其符合堆的规范
function heapify(int $k, int $heapSize, array &$arr)
{
    $largest = $k;
    $left = 2 * $k + 1;
    $right = 2 * $k + 2;
    if ($left <= $heapSize && $arr[$k] < $arr[$left]) {
        $largest = $left;
    }
    if ($right <= $heapSize && $arr[$largest] < $arr[$right]) {
        $largest = $right;
    }
    if ($largest != $k) {
        list($arr[$largest], $arr[$k]) = [$arr[$k], $arr[$largest]];
        heapify($largest, $heapSize, $arr);
    }
}
$start = microtime(true);
heapSort($arr);
$end = microtime(true);
$used = $end - $start;
echo "used $used s" . PHP_EOL;