<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;

class VisitController extends Controller {
    public function index() {
        return Visit::with('visitor')->get();
    }

    public function store(Request $request) {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'entry_time' => 'required|date_format:Y-m-d H:i:s',
            'exit_time' => 'nullable|date_format:Y-m-d H:i:s',
            'note' => 'nullable|string|max:256'
        ]);

        return Visit::create($request->all());
    }

    public function show($id) {
        return Visit::with('visitor')->findOrFail($id);
    }

    public function update(Request $request, $id) {
        $visit = Visit::findOrFail($id);
        $visit->update($request->all());
        return $visit;
    }

    public function destroy($id) {
        return Visit::destroy($id);
    }
}
