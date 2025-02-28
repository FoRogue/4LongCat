<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;

class VisitorController extends Controller {
    public function index() {
        return Visitor::with('department')->get();
    }

    public function store(Request $request) {
        $request->validate([
            'full_name' => 'required|string|max:150',
            'birth_date' => 'required|date',
            'position' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'document_type' => 'required|string',
            'document_series' => 'nullable|string|max:20',
            'document_number' => 'required|string|max:20',
            'document_issue_date' => 'required|date',
            'document_issued_by' => 'required|string|max:250',
            'passport_code' => 'nullable|string|max:7'
        ]);

        return Visitor::create($request->all());
    }

    public function show($id) {
        return Visitor::with('department')->findOrFail($id);
    }

    public function update(Request $request, $id) {
        $visitor = Visitor::findOrFail($id);
        $visitor->update($request->all());
        return $visitor;
    }

    public function destroy($id) {
        return Visitor::destroy($id);
    }
}

