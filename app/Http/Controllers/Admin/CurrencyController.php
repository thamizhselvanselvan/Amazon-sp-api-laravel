<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Currency::latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    return ($row->status) ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="/admin/currencys/' . $row->id . '/edit" class="edit btn btn-success"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger ml-2"><i class="far fa-trash-alt"></i> Remove</button>';

                    return $actionBtn;
                })
                ->make(true);
        }

        return view('admin.currency.index');
    }
}
