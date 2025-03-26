<?php

namespace App\Repositories\Item;

interface ItemRepositoryInterface
{
    public function getAll();
    
    public function findById(int $id);

    public function findByArray(string $field, array $data);

    public function findOneRecordBy(string $field, string $data);
    
    public function create(array $data);
    
    public function update(int $id, array $data);
    
    public function delete(int $id);
}
