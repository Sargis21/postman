<?php

namespace App\Modules\PostOffice\Entity\PostOffice;

use App\Modules\PostOffice\Entity\Item\ItemAbstract;
use App\Modules\PostOffice\Entity\Postman\PostmanAbstract;

class CandidatePostOffice implements PostOfficeInterface
{

    /** @var ItemAbstract[] */
    private array $itemsQueue = [];

    /**
     * @param PostmanAbstract[] $postmen
     */
    public function __construct(public array $postmen)
    {
        // TODO: Implement __construct() method.
    }

    /**
     * Good time for filling postmen
     * @param ItemAbstract[] $items
     * @return PostmanAbstract[]
     */
    public function liveDay(array $items = []): array
    {
        $this->pushItemsInQueue($items);
        $this->fillPostmen();
        return $this->postmen;
    }

    /**
     * @return bool
     */
    public function isEmptyItemsQueue(): bool
    {
        return !count($this->itemsQueue);
    }

    /**
     * @return bool
     */
    public function isAllItemsDelivered(): bool
    {
        if (count($this->itemsQueue)) {
            return false;
        }

        foreach ($this->postmen as $postman) {
            if ($postman->hasItems()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ItemAbstract[] $items
     */
    private function pushItemsInQueue(array $items = []): void
    {
        $this->itemsQueue = array_merge($this->itemsQueue, $items);
    }

    /**
     * @return void
     */
    private function fillPostmen(): void
    {
        foreach ($this->itemsQueue as $index => $item) {
            $shufflePostman = collect($this->postmen)->shuffle()->all();
            /** @var PostmanAbstract $postman */
            $postman = current($shufflePostman);

            if (!$postman->getItemFreeSlotCount($item)) {
                continue;
            }

            $postman->putItem($item);

            unset($this->itemsQueue[$index]);
        }
    }
}
