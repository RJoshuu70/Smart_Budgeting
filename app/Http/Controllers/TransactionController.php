<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('category')
            ->where('user_id', Auth::id())
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(15);

        $categories = Category::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:1',
            'date'        => 'required|date',
            'note'        => 'nullable|string|max:255',
        ], [
            'amount.min' => 'Nominal harus lebih dari 0.',
            'amount.numeric' => 'Masukkan angka yang valid.',
        ]);

        Transaction::create([
            'user_id'     => Auth::id(),
            'type'        => $request->type,
            'category_id' => $request->category_id,
            'amount'      => $request->amount,
            'date'        => $request->date,
            'note'        => $request->note,
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'type'        => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:1',
            'date'        => 'required|date',
            'note'        => 'nullable|string|max:255',
        ]);

        $transaction->update($request->only(
            'type', 'category_id', 'amount', 'date', 'note'
        ));

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())
            ->findOrFail($id);

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }
}