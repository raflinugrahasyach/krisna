<?php

namespace App\Http\Controllers;

use App\Libraries\Wablas\Wablas;
use App\Libraries\Wablas\WablasMessage;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    private function sendResponse($status, $message)
    {
        return response()->json([
            'status' => $status,
            'reason' => $message
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'target' => 'required',
            'message' => 'required'
        ]);

        $mergetags = explode('|', $request->input('target'));
        $phone = convert_phone(array_shift($mergetags));

        $message = (count($mergetags) > 0)
            ? convert_message_target($request->input('message'), $mergetags)
            : $request->input('message');

        $template = Template::first();
        $token = $template ? $template->token : config('wablas.token');

        try {
            $pesan = WablasMessage::create()
                ->to($phone)
                ->content($message)
                ->toArray();

            $wablas = new Wablas();
            $wablas->setToken($token)->sendMessage($pesan);

            return $this->sendResponse(true, 'success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendResponse(false, 'something went wrong');
        }
    }
}
