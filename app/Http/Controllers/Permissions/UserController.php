<?php

namespace App\Http\Controllers\Permissions;

use Exception;
use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = User::has('roles')->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $editRoute      = 'admin.assign.user.edit';
                    $dataId         = Crypt::encryptString($data->id);

                    $action = "";
                    $action .= '
                    <a class="btn btn-warning btn-icon" type="button" href="' . route($editRoute, $dataId) . '">
                        <i data-feather="edit"></i>
                    </a> ';

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->addColumn('role', function ($data) {
                    return implode(', ', $data->getRoleNames()->toArray());
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.permission.assign.user.index', [
            'breadcrumb' => 'Permission To User'
        ]);
    }


    public function create()
    {
        return view('admin.permission.assign.user.create', [
            'breadcrumb'    => 'Permissions To User',
            'btnSubmit'     => 'Save',
            'roles'         => Role::get(),
            'users'         => User::get(['id', 'name', 'username'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user'   => 'required',
            'role'   => 'required'
        ]);

        try {
            DB::beginTransaction();

            $user = User::find($request->user);
            $user->assignRole($request->role);

            DB::commit();

            if (isset($_POST['btnSimpan'])) {
                return redirect()->route('admin.assign.user.index')
                    ->with('success', "{$user->name} has been assigned to the role");
            } else {
                return redirect()->route('admin.assign.user.create')
                    ->with('success', "{$user->name} has been assigned to the role");
            }
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }

    public function edit($id)
    {
        return view('admin.permission.assign.user.edit', [
            'breadcrumb'    => 'Permissions To User',
            'btnSubmit'     => 'Sync',
            'data'          => User::find($id),
            'roles'         => Role::get(),
            'users'         => User::get(['id', 'name', 'username'])
        ]);

        try {
            $id = Crypt::decryptString($id);
            $data = User::find($id);

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found..");
            }

            return view('admin.permission.assign.user.edit', [
                'breadcrumb'    => 'Permissions To User',
                'btnSubmit'     => 'Sync',
                'data'          => $data,
                'roles'         => Role::get(),
                'users'         => User::get(['id', 'name', 'username'])
            ]);
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }

    public function update(Request $request, $id)
    {
        $id = Crypt::decryptString($id);
        $data = User::find($id);

        if (!$data) {
            return redirect()
                ->back()
                ->with('error', "Data not found");
        }

        DB::beginTransaction();
        try {
            $request->validate([
                'user'   => 'required',
                'role'   => 'required'
            ]);

            $data->syncRoles($request->role);

            DB::commit();

            return redirect()->route('admin.assign.user.index')
                ->with('success', "{$data->name} has been updated the role");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }
}
