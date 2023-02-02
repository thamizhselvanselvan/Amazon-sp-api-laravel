<?php

namespace App\Http\Controllers\V2\Masters;

use App\Http\Controllers\Controller;
use App\Models\V2\Masters\Currency;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CurrencyController extends Controller
{
    //

    public function index(Request $request)
    {
        if ($request->isMethod('get')) {
            if ($request->ajax()) {
                $data = Currency::query()
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->toArray();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $actionBtn = '<div class="d-flex"><a href="/v2/master/store/currency/edit/' . $row['id'] . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                        $actionBtn .= '<button data-id="' . $row['id'] . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                        return $actionBtn;
                    })
                    ->editColumn('status', function ($row) {
                        return $row['status'] ? 'Active' : 'Inactive';
                       
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('v2.masters.store.currency.index');
        } else {
            $request->validate([
                'currency' => 'required',
                'code' => 'required'
            ]);
            Currency::create([
                'name' => $request->currency,
                'code' => $request->code,
                'status' => $request->status,

            ]);

            return redirect()->intended('/v2/master/store/currency')->with('success', 'Currency  has been added successfully');
        }
    }

    public function add()
    {
        return view('v2.masters.store.currency.add');
    }

    public function edit($id)
    {
        $currency = Currency::find($id);
        return view('v2.masters.store.currency.edit',compact('currency'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'currency' => 'required',
            'code' => 'required'
        ]);
        $records = [
            'name' => $request->currency,
            'code' => $request->code,
            'status' => $request->status
        ];
        Currency::where('id', $id)->update($records);
        return redirect()->intended('/v2/master/store/currency')->with('success', 'Currency has been updated successfully');
    }

    public function delete(Request $request)
    {
        Currency::find($request->id)->delete();
        return redirect()->intended('/v2/master/store/currency')->with('success', 'Currency has been pushed to Bin successfully');
    }

    public function trashView(Request $request)
    {
        if ($request->ajax()) {
            $currency = Currency::onlyTrashed()->get();

            return DataTables::of($currency)
                ->addIndexColumn()

                ->addColumn('action', function ($currency) {
                    return '<button data-id="' . $currency->id . '" class="restore btn btn-success"><i class="fas fa-trash-restore"></i> Restore</button>';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] ? 'Active' : 'Inactive';
                   
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('v2.masters.store.currency.trash-view');
    }

    public function restore(Request $request)
    {

        Currency::where('id', $request->id)->restore();
        return response()->json(['success' => 'Currency has restored successfully']);
    }
}
