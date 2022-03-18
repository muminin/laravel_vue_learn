<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::orderBy('time', 'desc')->get();
        $response = [
            'message' => 'Transactions List',
            'data' => $transactions,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            'amount' => ['required', 'numeric'],
            'type' => ['required', 'in:expense,revenue'],
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

        try {
            $transactions = Transaction::create($request->all());
            $response = [
                'message' => 'Transactions created',
                'data' => $transactions,
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $err) {
            return response()->json([
                'message' => 'Failed',
                'data' => $err->errorInfo,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transactions = Transaction::findOrFail($id);

        $response = [
            'message' => "Detail of " . $transactions->title,
            'data' => $transactions,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $transactions = Transaction::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            'amount' => ['required', 'numeric'],
            'type' => ['required', 'in:expense,revenue'],
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

        try {
            $transactions->update($request->all());
            $response = [
                'message' => 'Transactions updated',
                'data' => $transactions,
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            return response()->json([
                'message' => 'Failed',
                'data' => $err->errorInfo,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transactions = Transaction::findOrFail($id);

        try {
            $transactions->delete();
            $response = [
                'message' => 'Transactions deleted',
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            return response()->json([
                'message' => 'Failed',
                'data' => $err->errorInfo,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
