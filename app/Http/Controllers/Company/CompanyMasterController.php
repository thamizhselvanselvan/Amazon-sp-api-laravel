<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company\CompanyMaster;
use PragmaRX\Health\Support\Resource;
use Yajra\DataTables\Facades\DataTables;

class CompanyMasterController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CompanyMaster::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="d-flex"><a href="company/edit/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('company.index');
    }

    public function add()
    {
        return view('company.add');
    }

    public function create(Request $request)
    {
        $request->validate([
            'company_name' => 'required'
        ]);
        CompanyMaster::create([
            'company_name' => $request->company_name,

        ]);
        return redirect()->intended('/company')->with('success', 'Company ' . $request->company_name . ' has been created successfully');
    }

    public function edit($id)
    {
        $company = CompanyMaster::where('id', $id)->first();
        return view('company.edit', compact('company'));
        // return $request->id;
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required'
        ]);

        CompanyMaster::where('id', $request->id)->update(['company_name' => $request->company_name]);
        return redirect()->intended('/company')->with('success', 'Company ' . $request->company_name . ' has been updated successfully');
    }

    public function trash(Request $request)
    {
       CompanyMaster::where('id', $request->id)->delete();

   return redirect()->intended('/company')->with('success', 'Company has been pushed to Bin successfully');
    }

    public function trashView(Request $request)
    {
        if ($request->ajax()) {
            $asins = CompanyMaster::onlyTrashed()->get();

            return DataTables::of($asins)
                ->addIndexColumn()

                ->addColumn('action', function ($company) {
                    return '<button data-id="' . $company->id . '" class="restore btn btn-success"><i class="fas fa-trash-restore"></i> Restore</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('company.trash-view');
    }

    public function restore(Request $request) {

        // return($request);
        CompanyMaster::where('id', $request->id)->restore();
        
        return response()->json(['success' => 'Company has restored successfully']);
    }

}
