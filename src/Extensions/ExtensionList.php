<?php

namespace PicPay\Prokers\Extensions;

use SplDoublyLinkedList;

class ExtensionList extends SplDoublyLinkedList
{
    public function toArray(): array
    {
        $this->rewind();
        $result = [];

        foreach ($this as $item) {
            $result[] = $item;
        }

        return $result;
    }
}
