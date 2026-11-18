<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::withCount([
            'loans as active_loans' => fn ($q) => $q->whereNull('returned_on'),
        ])->orderBy('name')->simplePaginate(10);

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.form', ['member' => new Member()]);
    }

    public function store(Request $request)
    {
        Member::create($this->validated($request));

        return redirect()->route('members.index')->with('ok', 'Member added.');
    }

    public function edit(Member $member)
    {
        return view('members.form', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $member->update($this->validated($request, $member));

        return redirect()->route('members.index')->with('ok', 'Member updated.');
    }

    public function destroy(Member $member)
    {
        if ($member->loans()->active()->exists()) {
            return back()->with('bad', 'This member still has books. Return them first.');
        }

        $member->delete();

        return back()->with('ok', 'Member deleted.');
    }

    public function card(Member $member)
    {
        return view('members.card', compact('member'));
    }

    private function validated(Request $request, ?Member $member = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:members,code,' . $member?->id],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);
    }
}
