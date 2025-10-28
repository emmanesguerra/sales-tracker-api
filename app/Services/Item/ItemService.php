<?php

namespace App\Services\Item;

use App\Repositories\Item\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ItemService
{
    protected $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function getAllItems()
    {
        return $this->itemRepository->getAll();
    }

    public function getItemById(int $id)
    {
        return $this->itemRepository->findById($id) ?? throw new ModelNotFoundException("Item not found");
    }

    public function createItem(array $data)
    {
        return $this->itemRepository->create($data);
    }

    public function updateItem(int $id, array $data)
    {
        return $this->itemRepository->update($id, $data);
    }

    public function deleteItem(int $id)
    {
        return $this->itemRepository->delete($id);
    }
}
