<?php

namespace App\Http\Controllers\V2\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\V2\Masters\CompanyMaster;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CompanyMasterController extends Controller
{

    public function index(Request $request)
    {
        if ($request->isMethod('get')) {
            if ($request->ajax()) {
                $data = CompanyMaster::query()
                    ->where('user_id',Auth::id())
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->toArray();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $actionBtn = '<div class="d-flex"><a href="/v2/master/company/edit/' . $row['id'] . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                        $actionBtn .= '<button data-id="' . $row['id'] . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                        return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('v2.masters.company.index');
        }
        else {

            $request->validate([
                'company_name' => 'required'
            ]);
            CompanyMaster::create([
                'company_name' => $request->company_name,
                'user_id' => Auth::id(),
    
            ]);
            return redirect()->intended('/v2/master/company')->with('success', 'Company ' . $request->company_name . ' has been created successfully');
        }

    }

    public function create()
    {
        return view('v2.masters.company.add');
    }

    // public function add(Request $request)
    // {
    //     $request->validate([
    //         'company_name' => 'required'
    //     ]);
    //     CompanyMaster::create([
    //         'company_name' => $request->company_name,

    //     ]);
    //     return redirect()->intended('/v2/master/company')->with('success', 'Company ' . $request->company_name . ' has been created successfully');
    // }

    public function edit($id)
    {
        $company = CompanyMaster::find($id);
        return view('v2.masters.company.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required'
        ]);

        CompanyMaster::where('id', $request->id)->update(['company_name' => $request->company_name]);
        return redirect()->intended('/v2/master/company')->with('success', 'Company ' . $request->company_name . ' has been updated successfully');
    }

    public function trash(Request $request)
    {
        CompanyMaster::where('id', $request->id)->delete();

        return redirect()->intended('/company')->with('success', 'Company has been pushed to Bin successfully');
    }

    public function trashView(Request $request)
    {
        if ($request->ajax()) {
            $company = CompanyMaster::onlyTrashed()->get();

            return DataTables::of($company)
                ->addIndexColumn()

                ->addColumn('action', function ($company) {
                    return '<button data-id="' . $company->id . '" class="restore btn btn-success"><i class="fas fa-trash-restore"></i> Restore</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('v2.masters.company.trash-view');
    }

    public function restore(Request $request)
    {

        CompanyMaster::where('id', $request->id)->restore();

        return response()->json(['success' => 'Company has restored successfully']);
    }
}
