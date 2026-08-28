<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use App\Services\Permissions\PermissionHelperService;

class CurrencyController extends Controller
{
    private $module = 'CURRENCY_MODULE';
    private $permission;

    public function __construct()
    {
        $this->permission = new PermissionHelperService;
    }

    public function index()
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        // dd(Currency::find(1));

        if (!Currency::find(1)) {
            $Currency = Currency::Create(["pound" => 1.00, "dollar" => 0.00, "euro" => 0.00]);
        }


        return view('currency.index', ['currency' => Currency::findOrFail(1)], compact('IS_UPDATABLE'));
    }

    public function update(Request $request, $id)
    {
        if(isset($request->id)){
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        } else {
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);
        }
        $validated = $request->validate([
            'pound' => 'required|numeric',
            'euro' => 'required|numeric',
            'dollar' => 'required|numeric',
        ]);

        // dd($validated);

        Currency::updateOrCreate(['id' => $id], $validated);

        return redirect()->route('currency.index')->with('success', 'Currency updated sucessfully.');
    }
}
