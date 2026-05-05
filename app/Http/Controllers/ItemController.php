<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::query()
            ->search($request->search)
            ->byCategory($request->category)
            ->latest()
            ->paginate(12);

        $categories = Item::distinct()->pluck('category');

        return view('inventory.index', compact('items', 'categories'));
    }
}
