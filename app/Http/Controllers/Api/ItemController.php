<?php

namespace App\Http\Controllers\Api;

use App\Services\Item\ItemService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Item\CreateRequest;
use App\Http\Requests\Item\UpdateRequest;

class ItemController extends Controller
{
    protected $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function index(): JsonResponse
    {
        $items = $this->itemService->getAllItems();
        return response()->json($items);
    }

    public function show($id): JsonResponse
    {
        try {
            $item = $this->itemService->getItemById($id);
            return response()->json($item);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(CreateRequest $request): JsonResponse
    {
        $item = $this->itemService->createItem($request->validated());
        return response()->json($item, 201);
    }

    public function update(UpdateRequest $request, $id): JsonResponse
    {
            $item = $this->itemService->updateItem($id, $request->validated());
            return response()->json($item);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->itemService->deleteItem($id);
            return response()->json(['message' => 'Item deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
