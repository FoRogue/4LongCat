<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller {
    public function index() {
        return Department::all();
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:150|unique:departments']);
        return Department::create($request->all());
    }

    public function show($id) {
        return Department::findOrFail($id);
    }

    public function update(Request $request, $id) {
        $department = Department::findOrFail($id);
        $department->update($request->all());
        return $department;
    }

    public function destroy($id) {
        return Department::destroy($id);
    }
}

